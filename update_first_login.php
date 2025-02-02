<?php
session_start();
require 'includes/db.php';

header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON-Daten aus dem Request einlesen
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['user_id'])) {
        $user_id = (int)$data['user_id'];

        try {
            $stmt = $conn->prepare("UPDATE users SET first_login = 0 WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);

            if ($stmt->rowCount() > 0) {
                $response['success'] = true;
            } else {
                $response['error'] = 'Keine Aktualisierung vorgenommen.';
            }
        } catch (PDOException $e) {
            $response['error'] = 'Fehler beim Aktualisieren der Datenbank: ' . $e->getMessage();
        }
    } else {
        $response['error'] = 'Ungültige Benutzerdaten.';
    }
}

echo json_encode($response);
?>
