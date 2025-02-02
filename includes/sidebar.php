<?php
// Version aus der Tabelle site_settings abrufen
$version = "No version"; // Standardwert falls nicht gefunden
$stmt = $conn->prepare("SELECT version FROM site_settings");
$stmt->execute();
$versionRow = $stmt->fetch(PDO::FETCH_ASSOC);

if ($versionRow) {
    $version = $versionRow['version'];
}
// Version und Benutzername laden
$username = isset($_SESSION['user_data']['username']) ? htmlspecialchars($_SESSION['user_data']['username']) : 'My account';
$country = isset($_SESSION['user_data']['country']) ? htmlspecialchars($_SESSION['user_data']['country']) : 'My country';
?>

<!-- Seitenleiste für Desktop -->
<aside class="menu p-4 is-hidden-touch" style="display: flex; flex-direction: column; height: 100vh;">
    <!-- Oberer Teil der Sidebar mit Navigation -->
    <div>
        <h2 class="title app has-text-white">
            <img src="src/img/stuneo_logo_light.svg" width="150px" alt="stuneo" /> <span class="tag is-light has-text-black" style="font-family: 'Inter', sans-serif; text-transform: uppercase; font-size: 7px;">BETA 2</span>

        </h2>
        <?php if ($is_logged_in): ?>
        <p class="menu-label has-text-grey-light">Navigation</p>
        <ul class="menu-list">
            <li class="mt-1 mb-1"><a class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'is-active' : 'has-text-grey-light' ?>" href="index">
                <i class="<?= ($page == 'Home') ? 'ri-home-6-fill ri-lg mr-1' : 'ri-home-6-line ri-lg mr-1' ?>"></i> <?php echo __('Home'); ?></a>
            </li>
            <li class="mt-1 mb-1"><a class="<?= basename($_SERVER['PHP_SELF']) == 'discover.php' ? 'is-active' : 'has-text-grey-light' ?>" href="discover">
                <i class="<?= ($page == 'Discover') ? 'ri-compass-discover-fill ri-lg mr-1' : 'ri-compass-discover-line ri-lg mr-1' ?>"></i> <?php echo __('Discover'); ?></a>
            </li>
            <li class="mt-1 mb-1"><a class="<?= basename($_SERVER['PHP_SELF']) == 'genres.php' ? 'is-active' : 'has-text-grey-light' ?>" href="genres">
                <i class="<?= ($page == 'Genres') ? 'ri-music-2-fill ri-lg mr-1' : 'ri-music-2-line ri-lg mr-1' ?>"></i> <?php echo __('Genres'); ?></a>
            </li>
            <li class="mt-1 mb-1"><a class="<?= basename($_SERVER['PHP_SELF']) == 'new-stations.php' ? 'is-active' : 'has-text-grey-light' ?>" href="new-stations">
                <i class="<?= ($page == 'New Stations') ? 'ri-music-ai-fill ri-lg mr-1' : 'ri-music-ai-line ri-lg mr-1' ?>"></i> <?php echo __('New Stations'); ?></a>
            </li>
            <li class="mt-1 mb-1"><a class="<?= basename($_SERVER['PHP_SELF']) == 'my-country-stations.php' ? 'is-active' : 'has-text-grey-light' ?>" href="my-country-stations">
                <i class="<?= ($page == 'My country stations') ? 'ri-earth-fill ri-lg mr-1' : 'ri-earth-line ri-lg mr-1' ?>"></i> <?php echo __('Stations in'); ?> <?= $country ?></a>
            </li>
            <li class="mt-1 mb-1"><a class="<?= basename($_SERVER['PHP_SELF']) == 'podcasts.php' ? 'is-active' : 'has-text-grey-light' ?>" href="podcasts">
                <i class="<?= ($page == 'Podcasts') ? 'ri-base-station-fill ri-lg mr-1' : 'ri-base-station-line ri-lg mr-1' ?>"></i> <?php echo __('Podcasts'); ?></a>
            </li>            

            <li class="mt-1 mb-1"><a class="<?= basename($_SERVER['PHP_SELF']) == 'favorites.php' ? 'is-active' : 'has-text-grey-light' ?>" href="favorites">
                <i class="<?= ($page == 'Favorites') ? 'ri-heart-fill ri-lg mr-1' : 'ri-heart-line ri-lg mr-1' ?>"></i> <?php echo __('Favorites'); ?></a>
            </li>
        </ul>
        <?php else: ?>
        <p class="menu-label has-text-grey-light"><?php echo __('Login'); ?> / <?php echo __('Register'); ?></p>
        <!-- Button für nicht angemeldete Benutzer -->
        <div class="buttons mt-4">
                <a href="login" class="button btn-login"><?php echo __('Login'); ?></a>
                <a href="register" class="button"><?php echo __('Register'); ?></a>
         </div>
         <small class="has-text-grey-light"><?php echo __('To use all functions, please log in or register for free.'); ?></small>
        <?php endif; ?>
    </div>

    <?php if ($is_logged_in): ?>
    <!-- Schnellzugriff auf Favoriten -->
    <div class="favorites-quick-access mt-4" style="max-height: 200px; overflow-y: auto; padding-right: 5px;">
    <p class="menu-label has-text-grey-light"><?php echo __('Quick Access'); ?></p>
        <ul class="menu-list">
            <?php 
            // Datenbankabfrage für Favoriten ausführen
            $stmt = $conn->prepare("
                SELECT logo_url, name, description, station_id, stream_url 
                FROM stations 
                WHERE station_id IN (SELECT station_id FROM favorites WHERE user_id = :user_id) 
                AND status = 'approved' 
                LIMIT 4
            ");
            $stmt->execute(['user_id' => $_SESSION['user_data']['user_id']]);
            
            // Schleife durch die Ergebnisse
            while ($station = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Werte sicher extrahieren
                $stationLogo = htmlspecialchars($station['logo_url'] ?? 'path/to/default/logo.png');
                $stationName = htmlspecialchars($station['name'] ?? 'Unknown Station');
                $stationId = htmlspecialchars($station['station_id']);
                $stationStream = htmlspecialchars($station['stream_url']);
            ?>
            <li class="favorite-item">
                <div class="favorite-card" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-bottom: 1px solid #5e5e5e;">
                    <div style="display: flex; align-items: center;">
                        <img src="<?= $stationLogo ?>" alt="<?= $stationName ?> Logo" style="width: 50px; height: 50px; border-radius: 8px; margin-right: 10px;">
                        <div>
                            <p class="title is-6 has-text-white mb-1"><?= $stationName ?></p>
                        </div>
                    </div>
                    <button class="play-button" style="background-color: #ffffff; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;" 
                    onclick="playStation('<?= $stationId ?>', '<?= $stationName ?>', '<?= $stationStream ?>', '<?= $stationLogo ?>')">
                        <i class="play-icon play-icon-<?= $stationId; ?> ti ti-player-play-filled" style="font-size: 1.5rem; color: #292b2f;"></i>
                        <i class="pause-icon pause-icon-<?= $stationId; ?> ti ti-player-pause-filled" style="display: none; font-size: 1.5rem; color: #292b2f;"></i>
                    </button>
                </div>
            </li>
            <?php } ?>
        </ul>
    </div>
    <?php else: ?>
    <?php endif; ?>



    <?php if ($is_logged_in): ?>
    <!-- Unterer Teil der Sidebar mit Account, Version und Teilen-Button -->
    <div class="menu-bottom mt-auto" style="padding-top: 20px;">
        <p class="menu-label has-text-grey-light"><?php echo __(key: 'Account'); ?></p>
        <ul class="menu-list">
            <li><a class="<?= basename($_SERVER['PHP_SELF']) == 'account.php' ? 'is-active' : 'has-text-grey-light' ?>" href="account">
                <i class="<?= ($page == 'Account') ? 'ri-user-fill ri-lg mr-1' : 'ri-user-line ri-lg mr-1' ?>"></i> <?= $username ?></a>
            </li>
            <li class="mt-1 mb-1"><a class="<?= basename($_SERVER['PHP_SELF']) == 'add-radio.php' ? 'is-active' : 'has-text-grey-light' ?>" href="add-radio">
                <i class="<?= ($page == 'Add radio') ? 'ri-radio-2-fill ri-lg mr-1' : 'ri-radio-2-line ri-lg mr-1' ?>"></i> <?php echo __(key: 'Add radio'); ?></a>
            </li>
        </ul>
    </div>
    <?php else: ?>
    <?php endif; ?>
</aside>






<script src="src/js/sidebar.js?v=<?= time(); ?>"></script>
<link rel="stylesheet" href="src/css/sidebar.css?v=<?= time(); ?>">