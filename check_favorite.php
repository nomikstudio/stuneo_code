<?php
session_start();
include 'includes/db.php';

// Benutzer-ID und station_id aus der Anfrage abrufen
$user_id = $_SESSION['user_data']['user_id'] ?? null;
$station_id = $_GET['station_id'] ?? null;

// Sicherheitsprüfung: Nur fortfahren, wenn user_id und station_id vorhanden sind
if ($user_id && $station_id) {
    // Favoritenstatus für die gegebene Station und den Benutzer abfragen
    $stmt = $conn->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND station_id = :station_id");
    $stmt->execute(['user_id' => $user_id, 'station_id' => $station_id]);
    $isFavorite = $stmt->fetchColumn() > 0; // Wenn COUNT > 0, dann ist es ein Favorit

    // JSON-Antwort mit dem Favoritenstatus zurückgeben
    header('Content-Type: application/json');
    echo json_encode(['isFavorite' => $isFavorite]);
} else {
    // Fehlerhafte Anfrage: Rückgabe eines Fehlerstatus
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Ungültige Anfrage']);
}
