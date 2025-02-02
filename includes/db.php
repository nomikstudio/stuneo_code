<?php
// Zeitzone in PHP setzen
date_default_timezone_set('Europe/Berlin'); // Zeitzone für MEZ (GMT+1)

// Datenbankverbindungsdetails
$host = 'localhost';
$db   = 'radio_app'; // Dein Datenbankname
$user = 'root'; // Dein Datenbank-Benutzername
$pass = ''; // Dein Datenbank-Passwort
$charset = 'utf8mb4';

// DSN für die PDO-Verbindung
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO-Optionen
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // PDO-Verbindung erstellen
    $conn = new PDO($dsn, $user, $pass, $options);

    // Zeitzone in Stunden berechnen (1 für Winterzeit, 2 für Sommerzeit)
    $hourOffset = date('I') ? 2 : 1; // 2 Stunden im Sommer, 1 Stunde im Winter
    $timezoneOffset = sprintf('+01:00', $hourOffset); // Format z. B. +01:00 oder +02:00

    // Zeitzone für die aktuelle Sitzung in MySQL setzen
    $conn->exec("SET time_zone = '$timezoneOffset';");

} catch (\PDOException $e) {
    // Fehlerbehandlung: Fehler ausgeben und Skript beenden
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}
