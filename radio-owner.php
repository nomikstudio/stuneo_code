<?php
// Seite und Includes
$page = "Radio Owner";
include 'access_control.php';
include_once('includes/header.php');
include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

// Datenbankverbindung und Radioinhaber-Slug abfragen
$slug = $_GET['slug'] ?? null;
include 'includes/db.php';

// Überprüfen, ob ein gültiger Slug vorhanden ist
if (!$slug || $slug === '#') {
    echo "<div class='main-content'>
    <div class='info-box has-text-white'>
            <h1 style='font-size:30px;'><?php echo __('Invalid Radio Slug'); ?></h1>
            <p><?php echo __('Please provide a valid radio station to view the owner information.'); ?></p>
          </div></div>";
          include_once('includes/player.php');
          include_once('includes/bottom-bar.php');
          include_once('includes/footer.php');
    exit;
}

$stmt = $conn->prepare("SELECT owner_id, name, email, phone, logo, description, website_url, facebook_url, twitter_url, instagram_url, linkedin_url FROM radio_owners WHERE slug = :slug");
$stmt->execute(['slug' => $slug]);
$owner_data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($owner_data) {
    $owner_id = $owner_data['owner_id'];

    // Stations des Radioinhabers abrufen
    $stationStmt = $conn->prepare("
        SELECT stations.station_id, stations.name, stations.description, stations.logo_url, stations.stream_url 
        FROM stations
        JOIN owner_station ON stations.station_id = owner_station.station_id
        WHERE owner_station.owner_id = :owner_id
    ");
    $stationStmt->execute(['owner_id' => $owner_id]);
    $stations = $stationStmt->fetchAll(PDO::FETCH_ASSOC);

    // Podcasts des Radioinhabers abrufen
    $podcastStmt = $conn->prepare("
        SELECT p.podcast_id, p.title, p.description, p.image, c.name AS category_name 
        FROM podcasts p
        JOIN owner_podcasts op ON p.podcast_id = op.podcast_id
        LEFT JOIN podcast_categories c ON p.category_id = c.category_id
        WHERE op.owner_id = :owner_id
    ");
    $podcastStmt->execute(['owner_id' => $owner_id]);
    $podcasts = $podcastStmt->fetchAll(PDO::FETCH_ASSOC);

    // Hole die favorisierten Podcasts des Benutzers
    $stmt = $conn->prepare("SELECT podcast_id FROM favorites_podcast WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $_SESSION['user_data']['user_id']]);
    $favoritePodcastsArray = $stmt->fetchAll(PDO::FETCH_COLUMN);

} else {
    // Kein Radioinhaber gefunden, Zeige eine einfache Nachricht
    echo "<div class='main-content'>
            <div class='info-box has-text-white'>
                <h1 style='font-size:30px;'><?php echo __('Radio Owner Not Found'); ?></h1>
                <p><?php echo __('No information is available for this radio station.'); ?></p>
            </div>
          </div>";
          include_once('includes/player.php');
          include_once('includes/bottom-bar.php');
          include_once('includes/footer.php');
    exit;
}

?>

<link rel="stylesheet" href="src/css/owner.css">

<div class="main-content">
    <!-- Header mit Titel und Zurück-Button -->
    <header class="level mb-6">
        <div class="level-left">
        </div>
        <div class="level-right hide-on-mobile">
            <?php include_once('includes/account_button.php'); ?>
        </div>
    </header>
    <!-- Anleitungsbox -->
    <div class="owner is-light mb-5">
        <h4 class="has-text-white" style="font-size: 40px; font-weight: 800; margin-bottom: 10px;">
        <?php echo htmlspecialchars($owner_data['name']); ?>
        </h4>
        <p style="font-size: 16px; line-height: 1.5; color: #ddd; margin-bottom: 15px;">
        <?php echo htmlspecialchars($owner_data['description']); ?>
        </p>
        <div class="social-links">
            <?php if (!empty($owner_data['website_url'])): ?>
                <a href="<?= htmlspecialchars($owner_data['website_url']); ?>" target="_blank" class="social-link" title="Website">
                    <i class="ri-global-line"></i>
                </a>
            <?php endif; ?>
            <?php if (!empty($owner_data['facebook_url'])): ?>
                <a href="<?= htmlspecialchars($owner_data['facebook_url']); ?>" target="_blank" class="social-link" title="Facebook">
                    <i class="ri-facebook-fill"></i>
                </a>
            <?php endif; ?>
            <?php if (!empty($owner_data['twitter_url'])): ?>
                <a href="<?= htmlspecialchars($owner_data['twitter_url']); ?>" target="_blank" class="social-link" title="Twitter">
                    <i class="ri-twitter-fill"></i>
                </a>
            <?php endif; ?>
            <?php if (!empty($owner_data['instagram_url'])): ?>
                <a href="<?= htmlspecialchars($owner_data['instagram_url']); ?>" target="_blank" class="social-link" title="Instagram">
                    <i class="ri-instagram-fill"></i>
                </a>
            <?php endif; ?>
            <?php if (!empty($owner_data['linkedin_url'])): ?>
                <a href="<?= htmlspecialchars($owner_data['linkedin_url']); ?>" target="_blank" class="social-link" title="LinkedIn">
                    <i class="ri-linkedin-fill"></i>
                </a>
            <?php endif; ?>
        </div>

        <div class="card-logo">
            <img src="<?php echo htmlspecialchars($owner_data['logo']); ?>" alt="Radio Logo" width="150" height="150">
        </div>
    </div>

    <!-- Stations des Radioinhabers -->
    <div class="favorites-header">
        <h3 class="has-text-white mb-3" style="font-size: 24px; font-weight: 800;"><?php echo htmlspecialchars($owner_data['name']); ?>'s <?php echo __('Stations'); ?></h3>
    </div>
    <div class="columns is-multiline">
        <?php if (count($stations) > 0): ?>
            <?php foreach ($stations as $station): ?>
                <?php 
                    // Station Card einbinden
                    include 'station_card.php';
                ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="column is-full has-text-white"><?php echo __('No stations available for this owner.'); ?></p>
        <?php endif; ?>
    </div>

    <!-- Podcasts des Radioinhabers -->
    <div class="favorites-header">
        <h3 class="has-text-white mb-3" style="font-size: 24px; font-weight: 800;"><?php echo htmlspecialchars($owner_data['name']); ?>'s <?php echo __('Podcasts'); ?></h3>
    </div>
    <div class="columns is-multiline">
        <?php if (count($podcasts) > 0): ?>
            <?php foreach ($podcasts as $podcast): ?>
            <?php 
                    // Station Card einbinden
                    include 'podcast_card_fav.php';
            ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="column is-full has-text-white"><?php echo __('No podcasts available for this owner.'); ?></p>
        <?php endif; ?>
    </div>

</div>
<script>
    // Hover-Animation für Karten
    document.querySelectorAll('.podcast-card').forEach(card => {
        const img = card.querySelector('img');
        card.addEventListener('mouseenter', () => {
            img.style.transform = 'scale(1.1)';
            card.style.transform = 'translateY(-5px)';
        });
        card.addEventListener('mouseleave', () => {
            img.style.transform = 'scale(1)';
            card.style.transform = 'translateY(0)';
        });
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const favoriteButtons = document.querySelectorAll(".favorite-btn");

    favoriteButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const podcastId = this.getAttribute("data-podcast-id");
            const heartIcon = document.getElementById(`heart-icon-${podcastId}`);

            // AJAX-Request, um den Podcast zu Favoriten hinzuzufügen oder zu entfernen
            fetch("toggle_favorite_podcast.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ podcast_id: podcastId }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === "added") {
                        heartIcon.classList.remove("ri-heart-line");
                        heartIcon.classList.add("ri-heart-fill");
                        heartIcon.style.color = "white";
                    } else if (data.status === "removed") {
                        heartIcon.classList.remove("ri-heart-fill");
                        heartIcon.classList.add("ri-heart-line");
                        heartIcon.style.color = "inherit";
                    } else {
                        console.error("Unexpected response:", data);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        });
    });
});
</script>
<?php include_once('includes/footer.php'); ?>
