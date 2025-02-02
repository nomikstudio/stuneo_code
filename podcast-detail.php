<?php
// Session starten und Basis-URL setzen
session_start();

// Benutzer-ID aus der Session abrufen
$user_id = $_SESSION['user_data']['user_id'] ?? null;


// Podcast-ID aus der URL
$podcast_id = $_GET['id'] ?? null;

if (!$podcast_id) {
    die(__('Podcast not found.'));
}


// Datenbankverbindung und Podcast aus der Datenbank abrufen
include 'includes/db.php';

$stmt = $conn->prepare("
    SELECT p.*, c.name 
    FROM podcasts p 
    LEFT JOIN podcast_categories c ON p.category_id = c.category_id 
    WHERE p.podcast_id = :podcast_id
");
$stmt->execute(['podcast_id' => $podcast_id]);
$podcast = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$podcast) {
    die(__('Podcast not found.'));
}

// RSS-Feed abrufen
$rss_url = $podcast['rss_url'];
$rss_content = file_get_contents($rss_url);

if (!$rss_content) {
    die(__('Failed to fetch RSS feed.'));
}

try {
    // RSS-Feed parsen
    $rss = new SimpleXMLElement($rss_content);
} catch (Exception $e) {
    die(__('Invalid RSS feed.'));
}

// Seitentitel aus dem RSS setzen
$page = htmlspecialchars($rss->channel->title ?? "Podcast Details");

// Access-Control einbinden
include 'access_control.php';
include_once('includes/header.php');
include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

// Trailer und Episoden extrahieren
$episodes = $rss->channel->item;
$trailer = null;
$regularEpisodes = [];

foreach ($episodes as $episode) {
    if (isset($episode->children('itunes', true)->episodeType) && (string)$episode->children('itunes', true)->episodeType === 'trailer') {
        $trailer = $episode;
    } else {
        $regularEpisodes[] = $episode;
    }
}

// Fortschritt aus der Datenbank abrufen
$progressStmt = $conn->prepare("
    SELECT episode_guid, current_time_user 
    FROM user_podcast_progress 
    WHERE user_id = :user_id AND podcast_id = :podcast_id
");
$progressStmt->execute([':user_id' => $user_id, ':podcast_id' => $podcast_id]);
$progressData = $progressStmt->fetchAll(PDO::FETCH_ASSOC);

$progressMap = [];
foreach ($progressData as $progress) {
    $progressMap[$progress['episode_guid']] = $progress['current_time_user'];
}

// Owner des Podcasts abrufen
$ownerStmt = $conn->prepare("
    SELECT o.name, o.slug 
    FROM radio_owners o
    JOIN owner_podcasts op ON o.owner_id = op.owner_id
    WHERE op.podcast_id = :podcast_id
");
$ownerStmt->execute(['podcast_id' => $podcast_id]);
$owner = $ownerStmt->fetch(PDO::FETCH_ASSOC);

// Wenn keine Zeile für den Owner vorhanden ist, setze 'Unknown' und '#'
if (!$owner) {
    $owner_name = 'Unknown';
    $owner_slug = '#';
} else {
    $owner_name = $owner['name'];
    $owner_slug = $owner['slug'];
}
?>


<link rel="stylesheet" href="src/css/podcast.css?v=<?= time(); ?>">


<!-- Hauptbereich -->
<div class="main-content">
    <!-- Header mit Titel -->
    <header class="level mb-6">
        <div class="level-left">
            <h3 class="has-text-white" style="font-size: 24px; font-weight: 800;">
                <?= htmlspecialchars($rss->channel->title); ?>
            </h3>
        </div>
        <div class="level-right hide-on-mobile">
            <?php include_once('includes/account_button.php'); ?>
        </div>
    </header>

<!-- Podcast-Header -->
<div class="podcast-header mb-6" style="background-color: rgba(26, 26, 26, 0.88); backdrop-filter: blur(10px); padding: 2rem; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.1);">
    <div class="columns is-vcentered">
        <div class="column is-narrow">
            <figure class="image is-128x128">
                <img src="<?= htmlspecialchars($rss->channel->image->url); ?>" alt="<?= htmlspecialchars($rss->channel->title); ?>" class="is-rounded">
            </figure>
            <?php if (!empty($podcast['is_adult']) && $podcast['is_adult'] == 1): ?>
                <!-- Badge for Adult Content -->
                <span style="
                    position: absolute;
                    top: 10px;
                    left: 10px;
                    background-color: #ff0000;
                    color: #fff;
                    font-size: 12px;
                    font-weight: bold;
                    padding: 0.2rem 0.5rem;
                    border-radius: 5px;
                    z-index: 10;">
                    18+
                </span>
            <?php endif; ?>
        </div>
        <div class="column">
            <h1 class="title has-text-white">
                <?= htmlspecialchars($rss->channel->title); ?>
            </h1>

            <p class="subtitle has-text-grey-light mt-3" style="font-size: 15px;">
                <?php 
                $maxWords = 100;
                $channelDescription = $rss->channel->description ?? __('No description available.');
                $plainTextDescription = strip_tags($channelDescription);
                $descriptionWords = explode(' ', $plainTextDescription);

                if (count($descriptionWords) > $maxWords): 
                    $shortDescription = implode(' ', array_slice($descriptionWords, 0, $maxWords));
                ?>
                    <span class="short-description"><?= htmlspecialchars($shortDescription); ?>...</span>
                    <span class="full-description is-hidden"><?= nl2br(htmlspecialchars($plainTextDescription)); ?></span>
                    <button class="toggle-description button is-small" style="background: rgba(31, 31, 31, 0.7); backdrop-filter: blur(8px); border-radius: 16px;">
                        <?= __('More'); ?>
                    </button>
                <?php else: ?>
                    <?= nl2br(htmlspecialchars($plainTextDescription)); ?>
                <?php endif; ?>
            </p>

            <div class="podcast-info">
                <!-- Author Badge -->
                <?php if (!empty($rss->channel->children('itunes', true)->author)): ?>
                    <p class="episode-count">
                        <b><?= __('Author'); ?>:</b> <?= htmlspecialchars($rss->channel->children('itunes', true)->author); ?>
                    </p>    
                <?php endif; ?>

                <!-- Anzahl der Episoden -->
                <p class="episode-count">
                    <b><?= __('Episodes'); ?>:</b> <?= count($rss->channel->item ?? []); ?>
                </p>

                <!-- Category -->
                 <p class="episode-count">
                    <b><?= __('Category'); ?>:</b> <?= htmlspecialchars($podcast['name'] ?? 'Uncategorized'); ?>
                </p>
            </div>

            <div class="buttons mt-3">
                <?php if (!empty($trailer) && !empty($trailer->enclosure['url'])): ?>
                    <button class="button is-light is-rounded podcast-play-button"
                        data-audio="<?= htmlspecialchars($trailer->enclosure['url']); ?>"
                        data-title="<?= htmlspecialchars($rss->channel->title); ?>"
                        data-subtitle="<?= __('Trailer'); ?>"
                        data-thumbnail="<?= htmlspecialchars($rss->channel->image->url); ?>">
                        <span class="icon"><i class="ti ti-player-play-filled"></i></span>
                        <span><?= __('Play Trailer'); ?></span>
                    </button>
                <?php endif; ?>

                <button class="button is-light is-rounded share-button" data-id="<?= $podcast_id; ?>" data-title="<?= htmlspecialchars($rss->channel->title); ?>">
                    <span class="icon"><i class="ti ti-share"></i></span>
                    <span><?= __('Share'); ?></span>
                </button>
                 <!-- Owner Badge -->
                 <?php if (!empty($owner_name)): ?>
                    <div class="podcast-owner-badge">
                        <!-- Wenn kein Owner existiert, zeige nur # an -->
                        <a href="<?= $owner_slug === '#' ? '/#' : 'owner/' . htmlspecialchars($owner_slug); ?>" class="button is-light is-rounded">
                            <span class="icon"><i class="ri-user-fill"></i></span>
                            <span><?= htmlspecialchars($owner_name); ?></span>
                        </a>
                    </div>
                <?php endif; ?>


                
            </div>
        </div>
    </div>
</div>



    <!-- Trailer anzeigen -->
<?php if ($trailer): ?>
    <div class="trailer mb-6" style="background-color: rgba(31, 31, 31, 0.3); backdrop-filter: blur(10px); padding: 1rem; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="columns is-vcentered">
            <div class="column is-narrow">
                <button class="button is-primary is-medium is-rounded podcast-play-button"
                    data-audio="<?= htmlspecialchars($trailer->enclosure['url']); ?>"
                    data-title="<?= htmlspecialchars($trailer->title); ?>"
                    data-subtitle="<?= __('Trailer'); ?>"
                    data-thumbnail="<?= htmlspecialchars($rss->channel->image->url); ?>">
                    <span class="icon"><i class="ti ti-player-play-filled"></i></span>
                </button>
            </div>
            <div class="column">
                <h4 class="title is-5 has-text-white"><?= htmlspecialchars($trailer->title); ?></h4>
                <p class="has-text-grey-light">
                    <?= htmlspecialchars_decode($trailer->description ?? __('No description available.')); ?>
                </p>
            </div>
            <div class="column is-narrow has-text-right">
                <p class="has-text-grey">
                    <?= date('d.m.Y', strtotime($trailer->pubDate)); ?>
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Folgen anzeigen -->
<div class="episodes">
    <h2 class="title is-4 has-text-white"><?= __('Episodes'); ?></h2>
    <div>
        <?php if ($regularEpisodes): ?>
            <?php 
            $progressMap = [];
            if ($user_id) {
                $progressStmt = $conn->prepare("
                    SELECT episode_guid, current_time_user 
                    FROM user_podcast_progress 
                    WHERE user_id = :user_id AND podcast_id = :podcast_id
                ");
                $progressStmt->execute([':user_id' => $user_id, ':podcast_id' => $podcast_id]);
                $progressData = $progressStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($progressData as $progress) {
                    $progressMap[$progress['episode_guid']] = $progress['current_time_user'];
                }
            }
            ?>

            <?php foreach ($regularEpisodes as $episode): ?>
                <?php 
                $episodeGuid = htmlspecialchars($episode->guid ?? '');
                $currentProgress = $progressMap[$episodeGuid] ?? 0;
                $audioUrl = htmlspecialchars($episode->enclosure['url'] ?? '');
                $title = htmlspecialchars($episode->title ?? __('Untitled'));
                $description = htmlspecialchars_decode($episode->description ?? __('No description available.'));
                $publishDate = date('d.m.Y', strtotime($episode->pubDate ?? 'now'));

                $duration = (string) $episode->children('itunes', true)->duration ?? '';
                $episodeLength = 0;

                if (!empty($duration)) {
                    $timeParts = explode(':', $duration);
                    if (count($timeParts) === 3) {
                        $episodeLength = ($timeParts[0] * 3600) + ($timeParts[1] * 60) + $timeParts[2];
                    } elseif (count($timeParts) === 2) {
                        $episodeLength = ($timeParts[0] * 60) + $timeParts[1];
                    } elseif (count($timeParts) === 1) {
                        $episodeLength = $timeParts[0];
                    }
                } elseif (isset($episode->enclosure['length'])) {
                    $bitrate = 128 * 1024;
                    $episodeLength = (int) $episode->enclosure['length'] * 8 / $bitrate;
                } else {
                    $episodeLength = 3600;
                }

                $progressPercent = $episodeLength > 0 
                    ? round(min(($currentProgress / $episodeLength) * 100, 100)) 
                    : 0;
                ?>

                <div class="episode-item mb-4" 
                    style="background-color: rgba(31, 31, 31, 0.3); 
                            backdrop-filter: blur(10px); 
                            padding: 1.5rem; 
                            border-radius: 10px; 
                            border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="columns is-vcentered">
                        <!-- Play-/Repeat-Buttons -->
                        <div class="column is-narrow">
                            <div class="button-group">
                                <!-- Play-Button -->
                                <button class="podcast-play-button button is-primary is-medium is-rounded"
                                    data-audio="<?= $audioUrl; ?>"
                                    data-title="<?= $title; ?>"
                                    data-subtitle="<?= htmlspecialchars($rss->channel->title); ?>"
                                    data-thumbnail="<?= htmlspecialchars($rss->channel->image->url); ?>"
                                    data-podcast-id="<?= htmlspecialchars($podcast_id); ?>"
                                    data-episode-guid="<?= $episodeGuid; ?>"
                                    data-current-progress="<?= $progressPercent === 100 ? 0 : $currentProgress; ?>">
                                    <span class="icon">
                                        <i class="<?= $progressPercent === 100 ? 'ti ti-reload' : 'ti ti-player-play-filled'; ?>"></i>
                                    </span>
                                </button>

                                <!-- Reload-Button: Nur anzeigen, wenn Fortschritt > 0 oder = 100 -->
                                <?php if ($progressPercent > 0): ?>
                                    <button class="restart-episode-button button is-danger is-small is-rounded mt-2"
                                        data-audio="<?= $audioUrl; ?>"
                                        data-title="<?= $title; ?>"
                                        data-thumbnail="<?= htmlspecialchars($rss->channel->image->url); ?>"
                                        data-subtitle="<?= htmlspecialchars($rss->channel->title); ?>"
                                        data-podcast-id="<?= htmlspecialchars($podcast_id); ?>"
                                        data-episode-guid="<?= $episodeGuid; ?>"
                                        data-current-progress="0">
                                        <span class="icon">
                                            <i class="ti ti-reload"></i>
                                        </span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Episodendetails -->
                        <div class="column">
                            <h4 class="title is-5 has-text-white"><?= $title; ?></h4>
                            <p class="has-text-grey-light">
                                <?php 
                                $maxWords = 100; 
                                // HTML-Tags entfernen und Wörter extrahieren
                                $plainTextDescription = strip_tags($description);
                                $descriptionWords = explode(' ', $plainTextDescription);

                                if (count($descriptionWords) > $maxWords): 
                                    $shortDescription = implode(' ', array_slice($descriptionWords, 0, $maxWords));
                                ?>
                                    <!-- Kurzbeschreibung -->
                                    <span class="short-description"><?= htmlspecialchars($shortDescription); ?>...</span>

                                    <!-- Vollständige Beschreibung -->
                                    <span class="full-description is-hidden"><?= nl2br(htmlspecialchars($plainTextDescription)); ?></span>

                                    <!-- Toggle-Button -->
                                    <button class="toggle-description button is-small" style="background: rgba(31, 31, 31, 0.7); backdrop-filter: blur(8px); border-radius: 16px;">
                                        <?= __('More'); ?>
                                    </button>
                                <?php else: ?>
                                    <?= nl2br(htmlspecialchars($plainTextDescription)); ?>
                                <?php endif; ?>
                            </p>



                            <?php if ($progressPercent > 0): ?>
                                <div class="progress-container">
                                    <div class="progress-bar" 
                                        data-episode-guid="<?= $episodeGuid; ?>" 
                                        style="width: <?= $progressPercent; ?>%;"></div>
                                </div>
                            <?php else: ?>
                                <p class="has-text-grey-light mt-3"><i class="ti ti-info-square-rounded-filled" style="color: #fa7109;"></i> <b><?= __('This episode has not yet been listened to.'); ?></b></p>
                            <?php endif; ?>
                        </div>

                        <!-- Veröffentlichungsdatum und Länge -->
                        <div class="column is-narrow has-text-right">
                            <p class="has-text-grey"><?= $publishDate; ?></p>
                            <p class="has-text-grey-light"><?= gmdate("H:i:s", $episodeLength); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="has-text-white"><?= __('No episodes available.'); ?></p>
        <?php endif; ?>
    </div>
</div>

</div>
<!-- Bottom-Player -->
<div id="bottom-player" class="bottom-player" style="position: fixed; bottom: 0; width: 100%; background-color: rgba(46, 46, 46, 0.8); padding: 0; display: flex; flex-direction: column; align-items: center; backdrop-filter: blur(10px); border-top: 1px solid #666;">
    <input id="progress-bar" type="range" min="0" max="100" value="0" step="0.1" style="width: 100%; height: 5px; appearance: none; background: linear-gradient(to right, #fa7109 0%, #fa7109 var(--progress), #666 var(--progress), #666 100%); margin: 0;">
    <div style="width: 100%; display: flex; align-items: center; padding: 1rem;">
        <img id="player-thumbnail" src="src/img/no_image.jpg" alt="Thumbnail" style="width: 50px; height: 50px; border-radius: 5px; margin-right: 1rem;">
        <div style="flex-grow: 1;">
            <h4 id="player-title" class="has-text-white" style="margin: 0;"><?php echo __(key: 'No episode playing'); ?></h4>
            <p id="player-subtitle" class="has-text-grey-light" style="margin: 0; font-size: 0.9rem;">---</p>
        </div>
        <div style="display: flex; align-items: center;">
            <input id="volume-control" type="range" min="0" max="1" step="0.1" value="1" style="background-color: #fa7109; margin-right: 1rem;">
            <button id="player-play-button" class="button is-primary is-rounded" style="margin-left: 0;">
                <span class="icon"><i class="ti ti-player-play-filled"></i></span>
            </button>
        </div>
    </div>
</div>

<!-- Teilen-Modal -->
<div id="sharePodcastModal" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="box modal-box-custom">
            <!-- X-Button oben rechts -->
            <button class="modal-close-custom" aria-label="close" id="closeModalButton">&times;</button>
            <h3 id="modalPodcastTitle" class="title is-4"></h3>
            <p class="mb-2"><b><?= __('Copy the link to share the podcast:'); ?></b></p>
            <input type="text" id="podcastLinkInput" readonly class="input is-light">
            <button class="button is-primary mt-4" onclick="copyToClipboardPodcast()"><?php echo __(key: 'Copy link'); ?></button>
        </div>
    </div>
</div>


<script>
    // Open Modal
    document.querySelectorAll('.share-button').forEach(button => {
        button.addEventListener('click', () => {
            const podcastId = button.getAttribute('data-id');
            const podcastTitle = button.getAttribute('data-title');
            const podcastLink = `https://open.stuneo.com/podcast/${podcastId}`;

            // Set Modal Content
            document.getElementById('modalPodcastTitle').textContent = `${podcastTitle}`;
            document.getElementById('podcastLinkInput').value = podcastLink;

            // Show Modal
            document.getElementById('sharePodcastModal').style.display = 'block';
        });
    });

    // Close Modal
    document.getElementById('closeModalButton').addEventListener('click', () => {
        document.getElementById('sharePodcastModal').style.display = 'none';
    });

    // Close Modal on Outside Click
    window.addEventListener('click', (event) => {
        const modal = document.getElementById('sharePodcastModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Copy to Clipboard Function
    function copyToClipboardPodcast() {
        const linkInput = document.getElementById('podcastLinkInput');
        linkInput.select();
        linkInput.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand('copy');

        // Optionally, provide feedback to the user
        alert('Link copied to clipboard!');
    }

    document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toggle-description').forEach(button => {
        button.addEventListener('click', () => {
            const parent = button.parentElement;
            const shortDescription = parent.querySelector('.short-description');
            const fullDescription = parent.querySelector('.full-description');
            
            if (shortDescription.classList.contains('is-hidden')) {
                shortDescription.classList.remove('is-hidden');
                fullDescription.classList.add('is-hidden');
                button.textContent = '<?= __('More'); ?>'; // Rückübersetzen
            } else {
                shortDescription.classList.add('is-hidden');
                fullDescription.classList.remove('is-hidden');
                button.textContent = '<?= __('Less'); ?>'; // Übersetzter Text für "Weniger"
            }
        });
    });
});


</script>
<script src="src/js/podcast_progress_sync.js?v=<?= time(); ?>"></script>

<script src="src/js/podcast_player.js?v=<?= time(); ?>"></script>
<script src="src/js/footer.js?v=<?= time(); ?>"></script>
<?php
include "../security/config.php";
include "../security/project-security.php";
?>
</body>
</html>
