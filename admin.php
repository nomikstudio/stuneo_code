<?php
// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "radio_app";

// Passwort verschlüsseln (bcrypt)
$admin_password = password_hash("1234", PASSWORD_BCRYPT);

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Admin hinzufügen
    $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'username' => 'dominik',
        'email' => 'hintringerdominik@gmail.com',
        'password' => $admin_password
    ]);
    
    echo "Admin erfolgreich hinzugefügt!";
} catch(PDOException $e) {
    echo "Fehler: " . $e->getMessage();
}

$conn = null;
?>
