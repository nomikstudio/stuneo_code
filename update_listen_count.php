<?php
include 'includes/db.php';
session_start();

header('Content-Type: application/json');

// Prüfen, ob station_id und user_id vorhanden sind
if (!isset($_GET['station_id']) || empty($_GET['station_id']) || !isset($_SESSION['user_data']['user_id']) || empty($_SESSION['user_data']['user_id'])) {
    echo json_encode(['error' => 'Valid Station ID and User ID are required']);
    exit();
}

$station_id = $_GET['station_id'];
$user_id = $_SESSION['user_data']['user_id'];

// Überprüfen, ob der Benutzer die Station bereits gehört hat (nur einmal pro Benutzer und Station)
$stmt = $conn->prepare("SELECT COUNT(*) FROM user_listens WHERE user_id = :user_id AND station_id = :station_id");
$stmt->execute(['user_id' => $user_id, 'station_id' => $station_id]);
$hasListened = $stmt->fetchColumn();

if ($hasListened > 0) {
    echo json_encode(['message' => 'Listen count not updated, already exists']);
    exit();
}

// Neue Zeile in user_listens hinzufügen, um den Aufruf zu registrieren
$stmt = $conn->prepare("INSERT INTO user_listens (user_id, station_id) VALUES (:user_id, :station_id)");
$stmt->execute(['user_id' => $user_id, 'station_id' => $station_id]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => 'Listen count updated']);
} else {
    echo json_encode(['error' => 'Failed to update listen count']);
}
