<?php
session_start();

include 'access_control.php';
include 'includes/db.php'; // Verbindungsdatei hinzufügen

header('Content-Type: application/json');

// Benutzer-ID aus der Session
$user_id = $_SESSION['user_data']['user_id'];

// Token löschen
$stmt = $conn->prepare("DELETE FROM favorites_tokens WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);

echo json_encode(['status' => 'success']);
