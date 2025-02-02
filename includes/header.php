<?php
// Dynamische Basis-URL-Funktion
function getBaseURL() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    return $protocol . '://' . $host . $baseDir . '/';
}

// CORS Header setzen
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');


// Session starten
session_start();
include 'includes/db.php';
include 'includes/functions.php';
include_once 'includes/maintenance_verify.php'; // Füge dies oben im Header ein, bevor der Rest der Seite geladen wird.

// Dynamisches Base-Tag
$baseURL = getBaseURL();

// Prüfen, ob der Benutzer angemeldet ist
$is_logged_in = isset($_SESSION['user_data']);
$user_id = $is_logged_in ? $_SESSION['user_data']['user_id'] : null;

$showModal = false;
$modalId = ''; // Standardmäßig leer

// Prüfen, ob der Benutzer eingeloggt ist
if ($is_logged_in && $user_id) {
    // First_login-Status aus der Datenbank abrufen
    $stmt = $conn->prepare("SELECT first_login FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $firstLogin = $stmt->fetchColumn();

    if ($firstLogin == 1) {
        $showModal = true;
        $modalId = "welcomeModal_" . $user_id; // Eindeutige ID für das Modal basierend auf der Benutzer-ID
    }
}

// Überprüfen, ob die App in Electron läuft
$is_electron = isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'Electron') !== false;

// Benutzer zu Login weiterleiten, wenn nicht angemeldet
if ($is_electron && !$is_logged_in) {
    header("Location: login");
    exit;
}

// Favoriten abrufen, falls der Benutzer angemeldet ist
$favoritenArray = [];
if ($is_logged_in && $user_id) {
// Richtiges Abrufen der Ergebnisse:
$stmt = $conn->prepare("SELECT station_id FROM favorites WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$favoritenArray = $stmt->fetchAll(PDO::FETCH_COLUMN); // Ergebnismenge wird als Array gespeichert

}

// Station-Owner-Daten abrufen
$owner_station = [];
$stmt = $conn->query("
    SELECT so.station_id, o.name, o.slug 
    FROM owner_station so
    JOIN radio_owners o ON so.owner_id = o.owner_id
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $owner_station[$row['station_id']] = [
        'name' => $row['name'],
        'slug' => $row['slug']
    ];
}

// Standardwerte für Download-Link und -Text
$downloadLink = "#";
$downloadText = $translations['Download for'] ?? "Download for";

// Betriebssystem aus POST-Daten prüfen und Download-Link setzen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['client_os'])) {
    $os = $_POST['client_os'];

    // Unterstützte Betriebssysteme und ihre Zuordnung
    $osMapping = [
        'Windows' => 'Windows',
        'Darwin' => 'Mac',
        'Mac' => 'Mac',
        'Linux' => 'Linux',
    ];

    // OS-Zuordnung prüfen und SQL-Abfrage ausführen
    if (array_key_exists($os, $osMapping)) {
        $osName = $osMapping[$os];
        $stmt = $conn->prepare("SELECT link FROM download_links WHERE os = :os");
        $stmt->execute(['os' => $osName]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $downloadLink = $row['link'];
            $downloadText .= " " . $osName;
        }
    }

    // JSON-Antwort zurückgeben
    echo json_encode([
        'downloadLink' => $downloadLink,
        'downloadText' => $downloadText
    ]);
    exit;
}

include 'init.php';

?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if(isset($page)){echo ($page) . " - stuneo";}else{echo "stuneo - the sound of now";} ?></title>
    <base href="<?= $baseURL; ?>">

    <link rel="icon" href="icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/tabler-icons.min.css" rel="stylesheet">
    
    <!-- Buttered Toasts CSS -->
    <link rel="stylesheet" href="src/style.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="src/custom_toast.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.core.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.theme.min.css">
    <link rel="stylesheet" href="cookies/cookie.css?v=<?= time(); ?>">

    <script>
        const userId = <?= json_encode($_SESSION['user_data']['user_id'] ?? null); ?>;
    </script>

    <!-- Im Kopfbereich von header.php -->
    <script src="src/js/custom_toast.js?v=<?= time(); ?>"></script>    
    <script src="src/js/electron.js?v=<?= time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

</head>
<body data-theme="dark">
    <!-- Loader-Overlay -->
    <div id="loader-overlay" class="loader-overlay">
        <div class="loader"></div>
    </div>
    <div id="toast-container"></div>

