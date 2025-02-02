<?php 
$page = "New Stations";
include 'access_control.php';
include_once('includes/header.php');
include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

// Abrufen neuer und genehmigter Stationen aus der Datenbank
$stmt = $conn->prepare("SELECT * FROM stations WHERE status = 'approved' ORDER BY created_at DESC LIMIT 10");
$stmt->execute();
$newStations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Abrufen der Favoriten des Benutzers
$user_id = $_SESSION['user_data']['user_id'];
$fav_stmt = $conn->prepare("SELECT station_id FROM favorites WHERE user_id = :user_id");
$fav_stmt->execute(['user_id' => $user_id]);
$favoritesArray = $fav_stmt->fetchAll(PDO::FETCH_COLUMN); // Liste mit Favoriten-IDs
?>

<!-- Hauptbereich -->
<div class="main-content">
    <!-- Header mit Titel und Zurück-Button -->
    <header class="level mb-6">
        <div class="level-left">
            <h3 class="has-text-white" style="font-size: 24px; font-weight: 800;"><?php echo __('New Stations'); ?></h3>
        </div>
        <div class="level-right hide-on-mobile">
            <?php include_once('includes/account_button.php'); ?>
        </div>
    </header>

    <!-- Anzeige neuer Stationen -->
    <div class="columns is-multiline">
        <?php if (count($newStations) > 0): ?>
            <?php foreach ($newStations as $station): ?>
                <?php
                $station_id = htmlspecialchars($station['station_id']);
                $isFavorite = in_array($station_id, $favoritesArray);
                
                // Owner-Daten aus dem $owner_station-Array abrufen
                $station_owner_name = isset($owner_station[$station_id]['name']) ? htmlspecialchars($owner_station[$station_id]['name']) : 'Unknown';
                $station_owner_slug = isset($owner_station[$station_id]['slug']) ? htmlspecialchars($owner_station[$station_id]['slug']) : '';
                $station_owner_url = $station_owner_slug ? "owner/" . $station_owner_slug : '#';
                ?>
                
                <div class="column is-one-quarter">
                    <div class="station-card">
                        <!-- Thumbnail -->
                        <div class="station-logo" style="background-image: url('<?= htmlspecialchars($station['logo_url']) ?: 'src/img/no_image.jpg'; ?>');">
                        </div>
                        
                        <!-- Card Content -->
                        <div class="station-card-content">
                            <h3 class="title is-5"><?= htmlspecialchars($station['name']); ?></h3>
                            <p class="subtitle is-6"><?= htmlspecialchars($station['description']); ?></p>
                            
                            <!-- Footer: Owner and Buttons -->
                            <div class="station-footer">
                                <!-- Owner Information -->
                                <div class="owner-info">
                                    <a href="<?= $station_owner_url; ?>" class="owner-link">
                                        <i class="ri-user-fill mr-1"></i><?= $station_owner_name; ?>
                                    </a>
                                </div>
                                <!-- Buttons -->
                                <div class="station-buttons">
                                    <button class="play-button" id="playButton-<?= $station_id; ?>" data-station-id="<?= htmlspecialchars($station_id); ?>"
                                            onclick="playStation(
                                                '<?= $station_id; ?>', 
                                                '<?= htmlspecialchars($station['name']); ?>', 
                                                '<?= htmlspecialchars($station['stream_url']); ?>', 
                                                '<?= htmlspecialchars($station['logo_url']); ?>', 
                                                '<?= $station_owner_name; ?>', 
                                                '<?= $station_owner_slug; ?>'
                                            )">
                                        <i class="play-icon play-icon-<?= $station_id; ?> ti ti-player-play-filled"></i>
                                        <i class="pause-icon pause-icon-<?= $station_id; ?> ti ti-player-pause-filled" style="display: none;"></i>
                                    </button>
                                    <button class="favorite-button" id="favoriteButton-<?= $station_id; ?>" 
                                            onclick="toggleFavorite('<?= $station_id; ?>')">
                                        <i class="ri-heart-<?= $isFavorite ? 'fill' : 'line'; ?>" id="favoriteIcon-<?= $station_id; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="column is-full has-text-white">No new stations available.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once('includes/footer.php'); ?>