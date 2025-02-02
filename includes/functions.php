<?php
// Funktion zur Überprüfung des Plans (z.B. in includes/functions.php)
function checkUserPlan($user_id, $conn) {
    // Abrufen der stripe_customer_id aus der Datenbank
    $stmt = $conn->prepare("SELECT stripe_customer_id, plan_id FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Wenn der Benutzer eine stripe_customer_id hat, die auf "Stuneo+" zeigt, dann zurückgeben
        if ($user['plan_id'] == '5') {
            return true; // Benutzer hat Stuneo+
        } else {
            return false; // Benutzer hat einen anderen Plan
        }
    }

    return false; // Kein Benutzer gefunden oder keine stripe_customer_id vorhanden
}
