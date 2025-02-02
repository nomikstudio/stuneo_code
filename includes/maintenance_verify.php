<?php
// Sicherstellen, dass keine unnötigen Datenbankabfragen durchgeführt werden, wenn eine Verbindung bereits existiert
if (!isset($conn)) {
    include_once __DIR__ . '/db.php'; // Verbindungsaufnahme nur falls erforderlich
}

// Subdomain überprüfen und Wartungsmodus setzen
$host = $_SERVER['HTTP_HOST'];
$isMaintenance = false;

// Subdomains und deren Wartungsmodus prüfen
if (strpos($host, 'help.stuneo.com') !== false && isset($settings['help_maintenance_mode']) && $settings['help_maintenance_mode']) {
    $isMaintenance = true;
} elseif (strpos($host, 'owner.stuneo.com') !== false && isset($settings['owner_maintenance_mode']) && $settings['owner_maintenance_mode']) {
    $isMaintenance = true;
} elseif (strpos($host, 'open.stuneo.com') !== false && isset($settings['open_maintenance_mode']) && $settings['open_maintenance_mode']) {
    $isMaintenance = true;
}

// Lokale Tests: Der Wartungsmodus soll nur für Subdomains aktiviert werden, aber nicht für localhost
if (strpos($host, 'localhost') === false && isset($settings['open_maintenance_mode']) && $settings['open_maintenance_mode']) {
    $isMaintenance = true;
}

// Wenn Wartungsmodus aktiv ist, zur Maintenance-Seite weiterleiten
if ($isMaintenance) {
    header("Location: /radioapp/maintenance");
    exit();
}
?>
