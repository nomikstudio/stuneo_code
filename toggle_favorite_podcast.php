<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_data']['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_data']['user_id'];

try {
    $data = json_decode(file_get_contents("php://input"), true);
    $podcast_id = isset($data['podcast_id']) ? (int)$data['podcast_id'] : null;

    if (!$podcast_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid podcast ID']);
        exit;
    }

    // Check if the podcast is already a favorite
    $stmt = $conn->prepare("SELECT COUNT(*) FROM favorites_podcast WHERE user_id = :user_id AND podcast_id = :podcast_id");
    $stmt->execute(['user_id' => $user_id, 'podcast_id' => $podcast_id]);
    $isFavorite = $stmt->fetchColumn();

    if ($isFavorite) {
        // Remove favorite
        $stmt = $conn->prepare("DELETE FROM favorites_podcast WHERE user_id = :user_id AND podcast_id = :podcast_id");
        $stmt->execute(['user_id' => $user_id, 'podcast_id' => $podcast_id]);
        echo json_encode(['status' => 'removed']);
    } else {
        // Add favorite
        $stmt = $conn->prepare("INSERT INTO favorites_podcast (user_id, podcast_id) VALUES (:user_id, :podcast_id)");
        $stmt->execute(['user_id' => $user_id, 'podcast_id' => $podcast_id]);
        echo json_encode(['status' => 'added']);
    }
} catch (Exception $e) {
    // Log the error
    error_log("Error in toggle_favorite_podcast.php: " . $e->getMessage());

    // Return a generic error
    echo json_encode(['status' => 'error', 'message' => 'Internal Server Error']);
    exit;
}
