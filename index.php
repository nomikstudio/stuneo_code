<?php
session_start();
$page = "Home";
$is_logged_in = isset($_SESSION['user_id']); // Überprüfen, ob der Benutzer angemeldet ist

// Sprache bestimmen
if ($is_logged_in) {
    // Wenn der Benutzer angemeldet ist, verwende die Benutzersprache
    $language = $_SESSION['user_data']['system_language'] ?? 'en';
    include_once('includes/header.php');
} else {
    include_once('includes/header.php');
    // Wenn der Benutzer nicht eingeloggt ist, verwende die Browser-Sprache
    $browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $supportedLanguages = ['de', 'en'];
    $language = in_array($browserLanguage, $supportedLanguages) ? $browserLanguage : 'en';
}

// Sprachdatei laden
$translations = [];
if ($language === 'de') {
    $translations = include 'languages/de_DE/main.php';
} else {
    $translations = include 'languages/en_US/main.php';
}

include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

// Favoritenstationen abrufen, nur freigegebene Sender anzeigen
$stmt = $conn->prepare("SELECT * FROM stations WHERE station_id IN (SELECT station_id FROM favorites WHERE user_id = :user_id) AND status = 'approved' LIMIT 4");
$stmt->execute(['user_id' => $_SESSION['user_data']['user_id']]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Neueste Stationen abrufen, nur freigegebene Sender anzeigen
$stmt = $conn->prepare("SELECT * FROM stations WHERE status = 'approved' ORDER BY created_at DESC LIMIT 8");
$stmt->execute();
$newStations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Meistgehörte Stationen abrufen basierend auf eindeutigen Benutzern aus user_listens, nur freigegebene Sender mit mindestens einem Aufruf
$stmt = $conn->prepare("
    SELECT s.*, COUNT(ul.user_id) AS unique_listen_count
    FROM stations s
    JOIN user_listens ul ON s.station_id = ul.station_id
    WHERE s.status = 'approved'
    GROUP BY s.station_id
    ORDER BY unique_listen_count DESC
    LIMIT 4
");
$stmt->execute();
$mostListenedStations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hervorgehobene Stationen abrufen, nur freigegebene und hervorgehobene Sender anzeigen
$stmt = $conn->prepare("SELECT * FROM stations WHERE is_featured = 1 AND status = 'approved' LIMIT 5");
$stmt->execute();
$featuredStations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Benutzer-Sprache abrufen
$user_language_code = $_SESSION['user_data']['language'] ?? 'en';

// Entsprechenden language_name aus der Tabelle languages finden
$stmt = $conn->prepare("SELECT language_name FROM languages WHERE language_code = :language_code");
$stmt->execute(['language_code' => $user_language_code]);
$language_name = $stmt->fetchColumn();

// Wenn language_name gefunden wurde, die Stations nach dem language_name filtern, nur freigegebene Sender anzeigen
if ($language_name) {
    $stmt = $conn->prepare("SELECT * FROM stations WHERE language = :language_name AND status = 'approved'");
    $stmt->execute(['language_name' => $language_name]);
    $languageStations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $languageStations = [];
}

// Empfohlene Stationen abrufen
// Empfohlene Stationen abrufen
if ($is_logged_in) {
    // Genre-IDs, Sprache und Land der Favoriten des Benutzers abrufen
    $stmt = $conn->prepare("
        SELECT DISTINCT s.genre_id, s.language, s.country
        FROM favorites f
        JOIN stations s ON f.station_id = s.station_id
        WHERE f.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $_SESSION['user_data']['user_id']]);
    $favoriteData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Benutzerpräferenzen sammeln
    $favoriteGenres = array_column($favoriteData, 'genre_id');
    $favoriteLanguage = $_SESSION['user_data']['language'] ?? 'en';

    // Benutzerland (bereits übersetzt) wird genutzt
    // $favoriteCountry kommt aus dem Mapping (siehe oben)

    // Häufig gehörte Stationen basierend auf `user_history` abrufen
    $stmt = $conn->prepare("
        SELECT s.station_id
        FROM user_history uh
        JOIN stations s ON uh.station_id = s.station_id
        WHERE uh.user_id = :user_id
        GROUP BY s.station_id
        ORDER BY COUNT(uh.user_id) DESC
        LIMIT 5
    ");
    $stmt->execute(['user_id' => $_SESSION['user_data']['user_id']]);
    $frequentlyListenedStations = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Empfohlene Stationen basierend auf Benutzerpräferenzen abrufen
    if (!empty($favoriteGenres) || !empty($frequentlyListenedStations)) {
        $conditions = [];
        $params = [];

        // Bedingung für Genre-IDs hinzufügen
        if (!empty($favoriteGenres)) {
            $placeholdersGenres = implode(',', array_fill(0, count($favoriteGenres), '?'));
            $conditions[] = "s.genre_id IN ($placeholdersGenres)";
            $params = array_merge($params, $favoriteGenres);
        }

        // Bedingung für häufig gehörte Stationen hinzufügen
        if (!empty($frequentlyListenedStations)) {
            $placeholdersStations = implode(',', array_fill(0, count($frequentlyListenedStations), '?'));
            $conditions[] = "s.station_id IN ($placeholdersStations)";
            $params = array_merge($params, $frequentlyListenedStations);
        }

        // Bedingung für Sprache hinzufügen
        $conditions[] = "s.language = ?";
        $params[] = $favoriteLanguage;

        // Bedingung für Land hinzufügen (optional)
        if (!empty($favoriteCountry)) {
            $conditions[] = "s.country = ?";
            $params[] = $favoriteCountry;
        }

        // Stationen aus den Favoriten ausschließen
        $conditions[] = "s.station_id NOT IN (SELECT station_id FROM favorites WHERE user_id = ?)";
        $params[] = $_SESSION['user_data']['user_id'];

        // Finaler Query
        $query = "
            SELECT s.*, rg.genre_name
            FROM stations s
            JOIN radio_genres rg ON s.genre_id = rg.genre_id
            WHERE (" . implode(' OR ', $conditions) . ")
              AND s.status = 'approved'
            GROUP BY s.station_id
            ORDER BY RAND()
            LIMIT 1
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $recommendedStations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Fallback: Zeige zufällige genehmigte Stationen
        $stmt = $conn->prepare("
            SELECT s.*, rg.genre_name
            FROM stations s
            JOIN radio_genres rg ON s.genre_id = rg.genre_id
            WHERE s.status = 'approved'
            ORDER BY RAND()
            LIMIT 1
        ");
        $stmt->execute();
        $recommendedStations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    // Für nicht eingeloggte Benutzer: Fallback mit zufälligen Stationen
    $stmt = $conn->prepare("
        SELECT s.*, rg.genre_name
        FROM stations s
        JOIN radio_genres rg ON s.genre_id = rg.genre_id
        WHERE s.status = 'approved'
        ORDER BY RAND()
        LIMIT 1
    ");
    $stmt->execute();
    $recommendedStations = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Hottest Podcasts abrufen
$stmt = $conn->prepare("
    SELECT 
        p.podcast_id, 
        p.title,
        p.description, 
        p.image, 
        COUNT(up.user_id) AS user_count
    FROM user_podcast_progress up
    JOIN podcasts p ON up.podcast_id = p.podcast_id
    GROUP BY up.podcast_id
    ORDER BY user_count DESC
    LIMIT 1
");
$stmt->execute();
$hottestPodcast = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Hauptbereich -->
<div class="main-content" id="content">
    <!-- Suchfeld und Benutzerbereich -->
    <header class="level mb-6">
        <div class="level-left">
            <form action="search" method="GET">
                <div class="search-container">
                    <i class="ri-search-line search-icon"></i>
                    <input class="search-input" type="text" name="query" placeholder="<?php echo __('What would you like to hear?'); ?>" required>
                    <button type="submit" class="search-button"><i class="ri-arrow-right-line"></i></button>
                </div>
            </form>
        </div>
        <?php include_once('includes/account_button.php'); ?>
    </header>

    <div class="recommended-section">
        <!-- Recommended Station -->
        <?php foreach ($recommendedStations as $station): ?>
            <?php include 'recomended_station_cards.php'; ?>
        <?php endforeach; ?>
        <!-- Podcast Card -->
        <div class="playlist-card">
            <div 
                class="playlist-image" 
                style="background-image: url('<?= htmlspecialchars($hottestPodcast['image'] ?? 'src/img/no_image.jpg'); ?>');">
            </div>
            <div class="playlist-content">
                <?php if ($hottestPodcast): ?>
                    <!-- Hottest Podcast -->
                    <p class="playlist-subtitle"><?= __('Most listened podcast'); ?></p>
                    <h3 class="playlist-header"><?= htmlspecialchars_decode($hottestPodcast['title']); ?></h3>
                    <p class="playlist-description">
                        <?php 
                            $maxWords = 10; 
                            $descriptionWords = explode(' ', strip_tags($hottestPodcast['description']));
                            if (count($descriptionWords) > $maxWords): 
                                echo htmlspecialchars(implode(' ', array_slice($descriptionWords, 0, $maxWords))) . '...';
                            else: 
                                echo htmlspecialchars_decode($hottestPodcast['description']);
                            endif; 
                        ?>
                    </p>
                    <a href="podcast/<?= htmlspecialchars($hottestPodcast['podcast_id']); ?>" class="playlist-button">
                        <i class="ti ti-player-play-filled"></i> <?= __('Play'); ?>
                    </a>
                <?php else: ?>
                    <!-- Fallback falls kein Podcast vorhanden -->
                    <h3 class="playlist-header"><?= __('No data available'); ?></h3>
                    <p class="playlist-subtitle"><?= __('Start listening to create data'); ?></p>
                <?php endif; ?>
            </div>
        </div>


    </div>





    <!-- Abschnitt: My Favorites -->
    <div class="favorites-header">
        <h3 class="has-text-white mb-3" style="font-size: 24px; font-weight: 800;"><i class="ri-poker-hearts-fill"></i> <?php echo __('My Favorites'); ?></h3>
        <a href="favorites" class="all-favorites-link"><?php echo __('All favorites'); ?></a>
    </div>
        <?php if ($is_logged_in): ?>
            <div class="columns is-multiline">
                <?php if (count($favorites) > 0): ?>
                    <?php foreach (array_slice($favorites, 0, 4) as $station): ?>
                        <?php include 'station_card.php'; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="column is-full has-text-white"><?php echo __('You have no favorite stations.'); ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
        <!-- Modal trigger für nicht angemeldete Benutzer -->
        <p class="is-full mb-4">
            <a href="#" class="open-modal has-text-white"><?php echo __('Login to see your favorites'); ?></a>
        </p>
        <?php endif; ?>


        <!-- Abschnitt: Featured Stations mit Rahmen -->
        <div style="border: 2px solid grey; border-radius: 10px; padding: 20px; margin-bottom: 40px; margin-top: 40px; background-color: rgba(31, 31, 31, 0.8); backdrop-filter: blur(10px);">
        <div class="favorites-header">
            <h3 class="has-text-white mb-3" style="font-size: 24px; font-weight: 800;"><i class="ri-sparkling-2-fill"></i> <?php echo __('Featured Stations'); ?></h3>
        </div>
        <div class="columns is-multiline">
            <?php if (count($featuredStations) > 0): ?>
                <?php foreach ($featuredStations as $station): ?>
                    <?php include 'station_card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="column is-full has-text-white"><?php echo __('No featured stations available.'); ?></p>
            <?php endif; ?>
        </div>
    </div>


    <!-- Abschnitt: Most Listened Stations -->
    <div class="favorites-header mt-5">
        <h3 class="has-text-white mb-3" style="font-size: 24px; font-weight: 800;"><i class="ri-fire-fill"></i> <?php echo __('Most Listened Stations'); ?></h3>
    </div>
    <div class="columns is-multiline">
        <?php if (count($mostListenedStations) > 0): ?>
            <?php foreach ($mostListenedStations as $station): ?>
                <?php include 'station_card.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="column is-full has-text-white"><?php echo __('No stations available.'); ?></p>
        <?php endif; ?>
    </div>


    <!-- Abschnitt: New Stations -->
    <div class="favorites-header mt-5">
        <h3 class="has-text-white mb-3" style="font-size: 24px; font-weight: 800;"><i class="ri-music-ai-fill"></i> <?php echo __('New Stations'); ?></h3>
        <a href="new-stations" class="all-favorites-link"><?php echo __('All new stations'); ?></a>
    </div>
    <div class="columns is-multiline">
        <?php if (count($newStations) > 0): ?>
            <?php foreach ($newStations as $station): ?>
                <?php include 'station_card.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="column is-full has-text-white"><?php echo __('No new stations available.'); ?></p>
        <?php endif; ?>
    </div>

    <?php if ($is_logged_in): ?>
    <!-- Abschnitt: Stations by Language -->
    <h3 class="has-text-white mb-4 mt-5" style="font-size: 24px; font-weight: 800;"><i class="ri-global-line"></i> <?php echo __('Stations in') . " " .  __($language_name ?? __('selected language')); ?></h3>
    <div class="columns is-multiline">
        <?php if (count($languageStations) > 0): ?>
            <?php foreach ($languageStations as $station): ?>
                <?php include 'station_card.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="column is-full has-text-white"><?php echo __('No stations available in your language.'); ?></p>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <?php endif; ?>



</div>

<link rel="stylesheet" href="src/css/index.css?v=<?= time(); ?>">

<!-- Login-Modal -->
<div id="loginModal" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="box modal-box-custom">
            <!-- X-Button oben rechts -->
            <button class="modal-close-custom" aria-label="close">&times;</button>
            
            <h3 class="title is-4"><?php echo __('Please login'); ?></h3>
            <p><?php echo __('You need to be logged in to use this feature.'); ?></p>
            <div class="buttons mt-4">
                <a href="login" class="button btn-login"><?php echo __('Login'); ?></a>
                <a href="register" class="button"><?php echo __('Register'); ?></a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.querySelector('#loginModal');

        // Prüfen, ob das Modal vorhanden ist
        if (!modal) {
            console.error("Modal #loginModal wurde nicht gefunden.");
            return;
        }

        const closeModalButtons = modal.querySelectorAll('.modal-close, .modal-close-custom, .modal-background');
        const openModalLinks = document.querySelectorAll('.open-modal');

        // Öffnen des Modals bei Klick auf die entsprechenden Links
        openModalLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                modal.classList.add('is-active');
            });
        });

        // Schließen des Modals bei Klick auf die Close-Buttons oder den Hintergrund
        closeModalButtons.forEach(button => {
            button.addEventListener('click', () => {
                modal.classList.remove('is-active');
            });
        });
    });
</script>

<!-- Rückmeldungen -->
<?php if (isset($error) && !empty($error)): ?>
    <script>
        showToast('Error', '<?= addslashes($error) ?>', 'error');
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
    <script>
        showToast('Success', '<?= addslashes($_SESSION['success']) ?>', 'success');
    </script>
    <?php unset($_SESSION['success']); // Löscht die Erfolgs-Session nach dem Toast ?>
<?php endif; ?>

<?php include_once('includes/footer.php'); ?>