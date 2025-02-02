<?php 
// Starte die Ausgabe-Pufferung
ob_start(); 

$page = "Podcasts";
include 'access_control.php';
include_once('includes/header.php');
include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

// Benutzer-ID aus der Session
$user_id = $_SESSION['user_data']['user_id'];

// Filter abrufen
$filterLanguage = $_GET['language'] ?? null;

// Basis-SQL für genehmigte Podcasts
$sql = "
    SELECT p.*, c.name 
    FROM podcasts p
    LEFT JOIN podcast_categories c ON p.category_id = c.category_id
    WHERE p.status = 'approved'
";
$params = [];

// Filter für Sprache hinzufügen (optional)
if ($filterLanguage) {
    $sql .= " AND language = :language";
    $params['language'] = $filterLanguage;
}

// Podcasts alphabetisch sortieren
$sql .= " ORDER BY title ASC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$podcasts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hole die favorisierten Podcasts des Benutzers
$stmt = $conn->prepare("SELECT podcast_id FROM favorites_podcast WHERE user_id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_data']['user_id']]);
$favoritePodcastsArray = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>



<!-- Hauptbereich -->
<div class="main-content">
    <!-- Header mit Titel -->
    <header class="level mb-6">
        <div class="level-left">
            <h3 class="has-text-white" style="font-size: 24px; font-weight: 800;"><?php echo __('Podcasts'); ?></h3>
        </div>
        <div class="level-right hide-on-mobile">
            <?php include_once('includes/account_button.php'); ?>
        </div>
    </header>


<!-- Podcasts anzeigen -->
<div class="columns is-multiline" style="gap: 0.5rem;">
<?php if (count($podcasts) > 0): ?>
    <?php foreach ($podcasts as $podcast): ?>
        <?php
        $podcast_id = htmlspecialchars($podcast['podcast_id']);
        $podcast_title = htmlspecialchars_decode($podcast['title'] ?? 'Unknown Podcast');
        $podcast_description = htmlspecialchars_decode($podcast['description'] ?? 'No description available.');
        $podcast_image = htmlspecialchars($podcast['image'] ?? 'src/img/no_image.jpg'); // Standardbild
        $name = htmlspecialchars($podcast['name'] ?? 'Uncategorized'); // Kategorie
        $is_adult = !empty($podcast['is_adult']) && $podcast['is_adult'] == 1; // Check if podcast is adult content
        ?>

        <!-- Podcast-Karte -->
        <div class="column is-full-mobile is-one-quarter-desktop" style="display: flex;">
            <div class="podcast-card" style="
                position: relative; 
                background-color: rgba(31, 31, 31, 0.7); 
                backdrop-filter: blur(8px);
                border: 1px solid #333333; 
                border-radius: 12px; 
                overflow: hidden; 
                transition: transform 0.2s ease;
                display: flex;
                flex-direction: column;
                height: var(--card-height, 350px); /* Einheitliche Höhe */
                width: 100%;
            ">

                <?php if ($is_adult): ?>
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

                <!-- Bild -->
                <figure class="image" style="
                    height: 200px; 
                    overflow: hidden; 
                    margin: 0;">
                    <img src="<?= $podcast_image; ?>" alt="<?= $podcast_title; ?>" style="
                        object-fit: cover; 
                        width: 100%; 
                        height: 100%; 
                        transition: transform 0.3s ease;">
                </figure>

                <!-- Inhalt -->
                <div class="card-content" style="padding: 1rem; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <!-- Titel und Favoriten-Button -->
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h4 style="font-size: 16px; color: #fff; font-weight: bold; margin-bottom: 0.5rem;">
                                <?= htmlspecialchars($podcast_title); ?>
                            </h4>
                            <!-- Favoriten-Button -->
                            <button 
                                class="btn btn-outline-light btn-sm favorite-btn" 
                                data-podcast-id="<?= htmlspecialchars($podcast_id); ?>" 
                                style="border: none; background: none; padding: 0; cursor: pointer;">
                                <i 
                                    class="<?= in_array($podcast_id, $favoritePodcastsArray) ? 'ri-heart-fill ri-lg' : 'ri-heart-line ri-lg'; ?>" 
                                    id="heart-icon-<?= htmlspecialchars($podcast_id); ?>" 
                                    style="<?= in_array($podcast_id, $favoritePodcastsArray) ? 'color: white;' : ''; ?>;">
                                </i>
                            </button>
                        </div>

                        <!-- Kategorie-Badge -->
                        <span style="
                            display: inline-block;
                            font-size: 12px;
                            color: #ccc;
                            border: 1px solid #555;
                            padding: 0.2rem 0.5rem;
                            border-radius: 12px;
                            background: rgb(43, 43, 43);
                            margin-bottom: 1rem;">
                            <?= $name; ?>
                        </span>

                        <p style="
                            font-size: 13px; 
                            color: #b0b0b0; 
                            line-height: 1.4; 
                            margin-bottom: 1rem; 
                            max-height: 40px; 
                            overflow: hidden; 
                            text-overflow: ellipsis;">
                            <?= $podcast_description; ?>
                        </p>
                    </div>
                    <!-- Buttons -->
                    <div class="buttons" style="
                        display: flex; 
                        justify-content: space-between; 
                        align-items: center;">
                        <!-- Play Button -->
                        <a href="podcast/<?= $podcast_id; ?>" class="button is-small" style="
                            background-color: #ff6f01; 
                            color: #fff; 
                            font-size: 14px; 
                            border-radius: 20px; 
                            padding: 0.5rem 1rem;">
                            <i class="ti ti-player-play-filled" style="margin-right: 0.5rem;"></i>
                            <?= __('Play') ?>
                        </a>
                        <!-- Share Button -->
                        <button class="button is-small is-light share-button" data-id="<?= $podcast_id; ?>" data-title="<?= $podcast_title; ?>" style="
                            border: 1px solid rgba(255, 255, 255, 0.2); 
                            color: #fff; 
                            background-color: transparent; 
                            font-size: 14px; 
                            padding: 0.5rem 1rem; 
                            border-radius: 20px;">
                            <i class="ti ti-share" style="margin-right: 0.5rem;"></i>
                            <?= __('Share') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="column is-full has-text-white"><?= __('No podcasts available to discover.'); ?></p>
<?php endif; ?>
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
                        // Hover-Animation für Karten
                    document.querySelectorAll('.podcast-card').forEach(card => {
                        const img = card.querySelector('img');
                        card.addEventListener('mouseenter', () => {
                            img.style.transform = 'scale(1.1)';
                            card.style.transform = 'translateY(-5px)';
                        });
                        card.addEventListener('mouseleave', () => {
                            img.style.transform = 'scale(1)';
                            card.style.transform = 'translateY(0)';
                        });
                    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const favoriteButtons = document.querySelectorAll(".favorite-btn");

    favoriteButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const podcastId = this.getAttribute("data-podcast-id");
            const heartIcon = document.getElementById(`heart-icon-${podcastId}`);

            fetch("toggle_favorite_podcast.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ podcast_id: podcastId }),
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.status === "added") {
                        heartIcon.classList.remove("ri-heart-line");
                        heartIcon.classList.add("ri-heart-fill");
                        heartIcon.style.color = "white";
                    } else if (data.status === "removed") {
                        heartIcon.classList.remove("ri-heart-fill");
                        heartIcon.classList.add("ri-heart-line");
                        heartIcon.style.color = "inherit";
                    } else {
                        console.error("Unexpected response:", data);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        });
    });
});

</script>


<?php include_once('includes/footer.php'); ?>
