<?php
session_start();
include 'includes/db.php'; // Datenbankverbindung

// Eingabeparameter korrekt abrufen
$podcast_id = $_GET['podcast_id'] ?? null;
$episode_guid = trim($_GET['episode_guid'] ?? ''); // Leerzeichen entfernen
$user_id = $_GET['user_id'] ?? null;

// Debugging: Eingehende Parameter überprüfen
file_put_contents('progress_debug.log', print_r([
    'podcast_id' => $podcast_id,
    'episode_guid' => $episode_guid,
    'user_id' => $user_id,
], true), FILE_APPEND);

// Validierung der Eingabewerte
if (!$podcast_id || !$episode_guid || !$user_id) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

// Fortsetzen, wenn alle Parameter vorhanden sind
try {
    $stmt = $conn->prepare("
        SELECT current_time_user 
        FROM user_podcast_progress 
        WHERE podcast_id = :podcast_id AND episode_guid = :episode_guid AND user_id = :user_id
    ");
    $stmt->execute([
        ':podcast_id' => $podcast_id,
        ':episode_guid' => $episode_guid,
        ':user_id' => $user_id
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(['current_time' => (float)$result['current_time_user']]);
    } else {
        echo json_encode(['current_time' => 0]);
    }
} catch (PDOException $e) {
    // Fehlerhandling
    file_put_contents('progress_debug.log', "SQL Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['error' => 'Database error']);
    exit;
}
?>
