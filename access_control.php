<?php
session_start();

// Zugriff auf die aktuelle Seite prüfen
$current_page = basename($_SERVER['PHP_SELF']);

// Erlaubt ist nur die index.php für nicht angemeldete Benutzer
if (!isset($_SESSION['user_data']) && $current_page !== 'index.php') {
    header("Location: ../index");
    exit();
}
?>