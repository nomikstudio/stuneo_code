<?php
// featured_stations.php

// Sicherheitsheader setzen
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Anpassen, um auf spezifische Domains zu beschränken
header('Access-Control-Allow-Methods: GET');

// API-Key-Authentifizierung
$requiredApiKey = '111-222-333-444'; // Ersetze dies mit deinem API-Key
$providedApiKey = $_GET['api_key'] ?? '';

if ($providedApiKey !== $requiredApiKey) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Ungültiger API-Schlüssel.'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Datenbankverbindung einbinden
require_once '../includes/db.php';

try {
    // Sichere SQL-Abfrage vorbereiten
    $query = "SELECT station_id, name, stream_url, description, country, logo_url, language, genre_id 
              FROM stations 
              WHERE is_featured = 1 AND status = 'approved'";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Ergebnisse abrufen
    $stations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON-Antwort zurückgeben
    echo json_encode([
        'success' => true,
        'data' => $stations
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // Fehlerbehandlung
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ein Fehler ist aufgetreten: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} finally {
    // Verbindung schließen
    if ($conn) {
        $conn = null;
    }
}
