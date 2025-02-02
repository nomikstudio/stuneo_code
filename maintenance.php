<?php
// Datenbankverbindung sicherstellen
include_once __DIR__ . '/includes/db.php';

// Hostname abrufen
$host = $_SERVER['HTTP_HOST'];

// Einstellungen aus der Datenbank abrufen
$query = $conn->query("SELECT * FROM site_settings LIMIT 1");
$settings = $query->fetch(PDO::FETCH_ASSOC);

// Überprüfen, ob der Wartungsmodus aktiv ist
$isMaintenance = false;

if (strpos($host, 'help.stuneo.com') !== false && $settings['help_maintenance_mode']) {
    $isMaintenance = true;
} elseif (strpos($host, 'owner.stuneo.com') !== false && $settings['owner_maintenance_mode']) {
    $isMaintenance = true;
} elseif (strpos($host, 'open.stuneo.com') !== false && $settings['open_maintenance_mode']) {
    $isMaintenance = true;
} elseif (strpos($host, 'localhost') !== false && $settings['open_maintenance_mode']) {
    // Lokale Tests für die "open.stuneo.com"-Subdomain
    $isMaintenance = true;
}

// Weiterleitung, wenn kein Wartungsmodus aktiv ist
if (!$isMaintenance) {
    header("Location: /radioapp/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icon.png" type="image/png">

    <title>We'll be back soon!</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a1a, #0d0d0d);
            color: white;
            overflow: hidden;
        }
        .container {
            text-align: center;
            background: rgba(31, 31, 31, 0.8);
            backdrop-filter: blur(10px);
            padding: 40px 20px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 600px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }
        .container img {
            max-width: 120px;
            margin-bottom: 20px;
        }
        .container h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
            color: white;
        }
        .container p {
            font-size: 16px;
            font-weight: 400;
            margin-bottom: 24px;
            color: #cccccc;
        }
        .container a {
            display: inline-block;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            color: white;
            background: #ff7e5f;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .container a:hover {
            background: #feb47b;
        }
        footer {
            position: absolute;
            bottom: 20px;
            text-align: center;
            width: 100%;
            font-size: 12px;
            color: #777;
        }
        footer a {
            color: #fffae0;
            text-decoration: none;
            font-weight: 600;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://stuneo.com/wp-content/uploads/2024/12/stuneo_logo_light.svg" alt="Stuneo Logo">
        <h1>We'll be back soon!</h1>
        <p>Our site is currently undergoing scheduled maintenance.<br>We appreciate your patience and understanding.</p>
    </div>
    <footer>
        <p>Need help? <a href="mailto:help@stuneo.com">Email us</a></p>
    </footer>
</body>
</html>
