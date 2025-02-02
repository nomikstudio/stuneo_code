<?php
header('Content-Type: application/json');
include 'includes/db.php';

// Sendername aus der Anfrage erhalten
$stationName = $_GET['station'] ?? null;

// Prüfen, ob ein Sendername angegeben wurde
if (!$stationName) {
    echo json_encode(['error' => 'Station name is required']);
    exit();
}

// Datenbankabfrage zur Ermittlung der API-URL des Senders
$stmt = $conn->prepare("SELECT api_url FROM stations WHERE name = :stationName LIMIT 1");
$stmt->execute(['stationName' => $stationName]);
$apiUrl = $stmt->fetchColumn();

// Prüfen, ob eine API-URL vorhanden ist
if (!$apiUrl) {
    echo json_encode(['error' => 'API URL not found for this station']);
    exit();
}

try {
    // JSON-Daten von der API abrufen
    $response = file_get_contents($apiUrl);
    if ($response === false) {
        throw new Exception("Unable to fetch JSON data");
    }

    // JSON-Daten in ein PHP-Array umwandeln
    $data = json_decode($response, true);
    if (!$data) {
        throw new Exception("Error parsing JSON data");
    }

    // Songtitel und Künstler aus den JSON-Daten extrahieren
    $songTitle = $data['song'] ?? null;
    $artist = $data['artist'] ?? null;

    // Wenn kein Songtitel oder Künstler vorhanden ist, auf "LIVE" setzen
    if (!$songTitle || !$artist || $songTitle === "Unknown Song" || $artist === "Unknown Artist") {
        $songTitle = '<i class="ri-broadcast-line ri-lg"></i> LIVE';
        $artist = '';
    }

    // JSON-Ausgabe des Songtitels und Künstlers
    echo json_encode([
        'station' => $stationName,
        'songTitle' => $songTitle,
        'artist' => $artist
    ]);

} catch (Exception $e) {
    // Fehlermeldung im JSON-Format zurückgeben
    echo json_encode(['error' => $e->getMessage()]);
}
