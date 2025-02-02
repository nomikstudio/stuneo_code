<?php
session_start();

require 'includes/db.php'; // Verbindung zu $conn herstellen

// Überprüfen, ob die Verbindung korrekt ist
if (!$conn instanceof PDO) {
    http_response_code(500);
    exit();
}

header('Content-Type: application/json'); // JSON-Antwort sicherstellen

// Station ID aus GET-Parameter abrufen
$station_id = $_GET['station_id'] ?? null;

// Validierung der station_id
if (!$station_id || !is_numeric($station_id)) {
    http_response_code(400);
    exit();
}

// Überprüfen, ob der Benutzer angemeldet ist
$user_id = $_SESSION['user_data']['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    exit();
}

// Datenbankeintrag
try {
    $query = "INSERT INTO user_history (user_id, station_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id, $station_id]);

    echo json_encode(['status' => 'success', 'message' => 'Play event tracked']);
} catch (PDOException $e) {
    http_response_code(500);
    exit();
}
