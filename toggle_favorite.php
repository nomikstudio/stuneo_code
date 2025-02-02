<?php
session_start();
include 'includes/db.php'; // Datenbankverbindung

$response = ['success' => false, 'message' => 'Ungültige Anfrage oder nicht eingeloggt.'];

// Überprüfen, ob der Benutzer eingeloggt ist und die Anfrage gültig ist
if (isset($_SESSION['user_data']['user_id']) && isset($_GET['station_id']) && isset($_GET['action'])) {
    $station_id = intval($_GET['station_id']);
    $user_id = intval($_SESSION['user_data']['user_id']); // Benutzer-ID aus Session

    if ($_GET['action'] === 'add') {
        // Füge den Favoriten zur Datenbank hinzu, falls er noch nicht existiert
        $stmt = $conn->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND station_id = :station_id");
        $stmt->execute(['user_id' => $user_id, 'station_id' => $station_id]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            $stmt = $conn->prepare("INSERT INTO favorites (user_id, station_id) VALUES (:user_id, :station_id)");
            $stmt->execute(['user_id' => $user_id, 'station_id' => $station_id]);
            $response = ['success' => true, 'message' => 'Sender zu Favoriten hinzugefügt.'];
        } else {
            $response = ['success' => false, 'message' => 'Dieser Sender ist bereits in den Favoriten.'];
        }
    } elseif ($_GET['action'] === 'remove') {
        // Entferne den Favoriten aus der Datenbank
        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = :user_id AND station_id = :station_id");
        $stmt->execute(['user_id' => $user_id, 'station_id' => $station_id]);
        $response = ['success' => true, 'message' => 'Sender aus den Favoriten entfernt.'];
    }
}

// JSON-Antwort senden
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
