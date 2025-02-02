<?php
session_start();

include 'includes/db.php';

// Prüfen, ob der Benutzer angemeldet ist
$is_logged_in = isset($_SESSION['user_data']);
$user_id = $is_logged_in ? $_SESSION['user_data']['user_id'] : null;

// Favoritenarray initialisieren
$favoritenArray = [];
if ($is_logged_in && $user_id) {
    // Favoriten für den Benutzer abrufen
    $stmt = $conn->prepare("SELECT station_id FROM favorites WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $favoritenArray = $stmt->fetchAll(PDO::FETCH_COLUMN); // Liste mit Favoriten-IDs
}

// Alle Station-Owner-Daten abrufen und in einem Array speichern
$owner_station = [];
$stmt = $conn->query("
    SELECT so.station_id, o.name, o.slug 
    FROM owner_station so
    JOIN radio_owners o ON so.owner_id = o.owner_id
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $owner_station[$row['station_id']] = [
        'name' => $row['name'],
        'slug' => $row['slug'] // Stellen Sie sicher, dass der Slug hier verfügbar ist
    ];
}


include 'init.php';

$genre_id = isset($_GET['genre_id']) ? intval($_GET['genre_id']) : 0;

// Abfrage erstellen und nur genehmigte Sender anzeigen
$sql = "SELECT * FROM stations WHERE status = 'approved'";
if ($genre_id > 0) {
    $sql .= " AND genre_id = :genre_id";
}

$stmt = $conn->prepare($sql);
if ($genre_id > 0) {
    $stmt->execute(['genre_id' => $genre_id]);
} else {
    $stmt->execute();
}
$stations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Favoriten des Benutzers abrufen
$user_id = $_SESSION['user_data']['user_id'];
$fav_stmt = $conn->prepare("SELECT station_id FROM favorites WHERE user_id = :user_id");
$fav_stmt->execute(['user_id' => $user_id]);
$favoritesArray = $fav_stmt->fetchAll(PDO::FETCH_COLUMN);

// Stationen und Favoriten anzeigen
if (count($stations) > 0) {
    foreach ($stations as $station) {
        $station_id = htmlspecialchars($station['station_id']);
        $station_name = htmlspecialchars($station['name']);
        $station_logo_url = htmlspecialchars($station['logo_url']);
        $station_description = htmlspecialchars($station['description']);
        $stream_url = htmlspecialchars($station['stream_url']); // Stream-URL hinzufügen
        $isFavorite = in_array($station_id, $favoritesArray); // Überprüfung, ob die Station in den Favoriten ist

        // Owner-Daten aus dem $owner_station-Array abrufen
        $station_owner_name = isset($owner_station[$station_id]['name']) ? htmlspecialchars($owner_station[$station_id]['name']) : 'Unknown Owner';
        $station_owner_slug = isset($owner_station[$station_id]['slug']) ? htmlspecialchars($owner_station[$station_id]['slug']) : '';
        $station_owner_url = $station_owner_slug ? 'owner/' . $station_owner_slug : '#';

        echo '<div class="column is-one-quarter">';
        echo '    <div class="station-card">';
        echo '        <div class="station-logo" style="background-image: url(\'' . $station_logo_url . '\');">';
        echo '        </div>';
        echo '        <div class="station-card-content">';
        echo '            <h3 class="title is-5">' . $station_name . '</h3>';
        echo '            <p class="subtitle is-6">' . $station_description . '</p>';
        // Owner-Informationen und Buttons im Footer
        echo '            <div class="station-footer">';
        echo '                <div class="owner-info">';
        echo '                    <a href="' . $station_owner_url . '" class="owner-link"><i class="ri-user-fill mr-1"></i>' . $station_owner_name . '</a>';
        echo '                </div>';
        echo '                <div class="station-buttons">';
        echo '                    <button class="play-button" id="playButton-' . $station_id . '" onclick="playStation(\'' . $station_id . '\', \'' . $station_name . '\', \'' . $stream_url . '\', \'' . $station_logo_url . '\', \'' . $station_owner_name . '\', \'' . $station_owner_slug . '\')" data-station-id="' . $station_id . '">';
        echo '                          <i class="play-icon play-icon-' . $station_id . ' ti ti-player-play-filled"></i>';
        echo '                          <i class="pause-icon pause-icon-' . $station_id . ' ti ti-player-pause-filled" style="display: none;"></i>';
        echo '                    </button>';
        echo '                    <button class="favorite-button" id="favoriteButton-' . $station_id . '" onclick="toggleFavorite(\'' . $station_id . '\')">';
        echo '                        <i class="ri-heart-' . ($isFavorite ? 'fill' : 'line') . '" id="favoriteIcon-' . $station_id . '"></i>';
        echo '                    </button>';
        echo '                </div>';
        echo '            </div>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
        } 
} else {
    echo '<p class="column is-full has-text-white mt-3">' . __("No stations available in this genre.") . '</p>';
}
?>