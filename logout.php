<?php
session_start();
include 'includes/db.php'; 

if (isset($_SESSION['user_data'])) {
    $userId = $_SESSION['user_data']['user_id'];

    // Remember Token löschen
    $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);

    // Remember Me-Cookie löschen
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    }

    // Session löschen
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    // Weiterleitung zur Login-Seite mit dem ?logout=success Parameter
    header("Location: login?logout=success");
    exit;
} else {
    header("Location: login.php");
    exit;
}
