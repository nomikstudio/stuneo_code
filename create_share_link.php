<?php
session_start();

include 'access_control.php';
include 'includes/db.php'; // Verbindungsdatei hinzufügen
header('Content-Type: application/json');


// Überprüfen, ob die Benutzer-Session korrekt gesetzt ist
if (!isset($_SESSION['user_data']['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated.']);
    exit();
}

// Benutzer-ID aus der Session holen
$user_id = $_SESSION['user_data']['user_id'];

try {
    // Überprüfen, ob bereits ein Token existiert
    $stmt = $conn->prepare("SELECT share_token FROM favorites_tokens WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);

    if ($stmt->fetchColumn()) {
        echo json_encode(['status' => 'error', 'message' => 'Share link already exists.']);
        exit();
    }

    // Neuen Token erstellen
    $token = bin2hex(random_bytes(16));
    $insertStmt = $conn->prepare("INSERT INTO favorites_tokens (user_id, share_token) VALUES (:user_id, :share_token)");
    $insertStmt->execute(['user_id' => $user_id, 'share_token' => $token]);

    // Erfolgsmeldung zurückgeben
    echo json_encode(['status' => 'success', 'share_token' => $token]);
} catch (PDOException $e) {
    // Fehlerhandling
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
