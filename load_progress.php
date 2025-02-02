<?php
// Header für JSON-Antwort
header('Content-Type: application/json');

// Datenbankverbindung einbinden
include 'includes/db.php';

// Episode GUID abrufen
$episode_guid = $_GET['episode_guid'] ?? null;

if (!$episode_guid) {
    echo json_encode(['success' => false, 'message' => 'Missing episode GUID.']);
    exit;
}

try {
    // Fortschritt für die Episode abrufen
    $stmt = $conn->prepare("SELECT current_time_user FROM user_podcast_progress WHERE episode_guid = :episode_guid");
    $stmt->execute(['episode_guid' => $episode_guid]);
    $progress = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($progress) {
        echo json_encode([
            'success' => true,
            'current_time_user' => $progress['current_time_user']
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'current_time_user' => 0 // Standardwert, wenn kein Fortschritt vorhanden ist
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
