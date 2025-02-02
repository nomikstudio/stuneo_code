<?php 
$page = "Shared Favorites";
include_once('includes/header.php');
include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

// Token aus der URL abrufen
$token = $_GET['token'] ?? null;

if (!$token) {
    http_response_code(404);
    echo "<p class='has-text-white'>".__('List not found.')."</p>";
    include_once('includes/footer.php');
    exit();
}

// Benutzer-ID aus dem Token abrufen
$stmt = $conn->prepare("SELECT user_id FROM favorites_tokens WHERE share_token = :token");
$stmt->execute(['token' => $token]);
$user_id = $stmt->fetchColumn();

if (!$user_id) {
    http_response_code(404);
    echo "<div class='main-content'>
    <i class='ri-delete-bin-2-fill ri-3x has-text-white'></i>
    <p class='has-text-white mt-3'><b>".__('This shared list is no longer available. It may have been deleted or moved.')."</b></p>
    </div>";
    include_once('includes/footer.php');
    exit();
}

// Benutzerinformationen abrufen
$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$username = $stmt->fetchColumn();

// Genehmigte Favoriten des Benutzers abrufen
$stmt = $conn->prepare("SELECT * FROM stations WHERE station_id IN (SELECT station_id FROM favorites WHERE user_id = :user_id) AND status = 'approved'");
$stmt->execute(['user_id' => $user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Favoriten-Array für die Anzeige des gefüllten Herzsymbols
$favoritenArray = array_column($favorites, 'station_id');
?>

<!-- Hauptbereich -->
<div class="main-content">
    <header class="level mb-6">
        <div class="level-left">
            <h3 class="has-text-white" style="font-size: 24px; font-weight: 800;">
                <?php echo sprintf(__('Favorites shared by %s'), htmlspecialchars($username ?? __('Unknown User'))); ?>
            </h3>
        </div>
    </header>

    <!-- Alle Favoriten anzeigen -->
    <div class="columns is-multiline">
        <?php if (count($favorites) > 0): ?>
            <?php foreach ($favorites as $station): ?>
                <?php include 'station_card.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="column is-full has-text-white" style="font-size: 20px;"><?php echo __('No stations in this list.'); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php include_once('includes/footer.php'); ?>
