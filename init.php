<?php
session_start();
include 'includes/db.php';


// Standard-Sprachcode
$system_language = 'en_US';

// Sprachcode aus der Datenbank holen, falls der Benutzer eingeloggt ist
if (isset($_SESSION['user_data']['user_id'])) {
    $user_id = $_SESSION['user_data']['user_id'];
    $stmt = $conn->prepare("SELECT system_language FROM users WHERE user_id = :user_id LIMIT 1");
    $stmt->execute(['user_id' => $user_id]);
    $system_language = $stmt->fetchColumn() ?: 'en_US'; // Standard auf Englisch setzen, falls leer
}

// Funktion zum Laden der Sprachdateien
function load_language($system_language) {
    $path = __DIR__ . "/languages/{$system_language}/";
    $translations = [];
    
    foreach (glob($path . "*.php") as $file) {
        $translations = array_merge($translations, include($file));
    }
    
    return $translations;
}

// Sprachdateien laden
$translations = load_language($system_language);

// Funktion zur Übersetzung
function __($key) {
    global $translations;
    return $translations[$key] ?? $key;
}