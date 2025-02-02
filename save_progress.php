<?php
session_start();
if (empty($_POST)) {
    parse_str(file_get_contents('php://input'), $_POST);
}

include 'includes/db.php'; // Datenbankverbindung

// POST-Daten validieren und debuggen
$user_id = $_SESSION['user_data']['user_id'] ?? null;
$podcast_id = $_POST['podcast_id'] ?? null;
$episode_guid = $_POST['episode_guid'] ?? null;
$current_time = $_POST['current_time'] ?? null;

file_put_contents('debug.log', print_r([
    ':user_id' => $user_id,
    ':podcast_id' => $podcast_id,
    ':episode_guid' => $episode_guid,
    ':current_time_user' => (int)$current_time,
    ':update_current_time_user' => (int)$current_time,
], true), FILE_APPEND);


// Validierung
if (!$user_id || !$podcast_id || !$episode_guid || !is_numeric($current_time)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Invalid input data.',
        'details' => [
            'user_id' => $user_id,
            'podcast_id' => $podcast_id,
            'episode_guid' => $episode_guid,
            'current_time' => $current_time,
        ],
    ]);
    exit;
}

// Fortschritt in die Datenbank speichern
try {
    $stmt = $conn->prepare("
    INSERT INTO user_podcast_progress (user_id, podcast_id, episode_guid, current_time_user, last_updated)
    VALUES (:user_id, :podcast_id, :episode_guid, :current_time_user, NOW())
    ON DUPLICATE KEY UPDATE current_time_user = :update_current_time_user, last_updated = NOW()
");
$stmt->execute([
    ':user_id' => $user_id,
    ':podcast_id' => $podcast_id,
    ':episode_guid' => $episode_guid,
    ':current_time_user' => (int)$current_time,
    ':update_current_time_user' => (int)$current_time,
]);


    echo json_encode(['success' => true, 'message' => 'Progress saved successfully.']);
} catch (PDOException $e) {
    file_put_contents('debug.log', "Database error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
