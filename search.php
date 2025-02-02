<?php
session_start();
include 'includes/db.php';

// Suchbegriff aus der URL lesen
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

$page = "Search Results";
include 'access_control.php';
include_once('includes/header.php');
include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

// Hole die favorisierten Podcasts des Benutzers
$stmt = $conn->prepare("SELECT podcast_id FROM favorites_podcast WHERE user_id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_data']['user_id']]);
$favoritePodcastsArray = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>

<!-- Hauptbereich -->
<div class="main-content">
    <!-- Benutzerbereich und Zurück-Button -->
    <header class="level mb-6">
        <div class="level-left">
            <h3 class="has-text-white" style="font-size: 24px; font-weight: 800;">
                <?= __('Search results'); ?>
            </h3>
        </div>
        <div class="level-right hide-on-mobile">
            <?php include_once('includes/account_button.php'); ?>
        </div>
    </header>

    <div class="mt-4">
        <?php if (!empty($query)): ?>
            <?php 
            try {
                // Suche in der Stations-Tabelle
                $stmtStations = $conn->prepare("
                    SELECT * FROM stations WHERE name LIKE :query
                ");
                $stmtStations->execute(['query' => '%' . $query . '%']);
                $stations = $stmtStations->fetchAll(PDO::FETCH_ASSOC);

                // Stationen immer anzeigen
                if (!empty($stations)) {
                    echo '<div class="column is-full">
                            <h4 class="has-text-white">' . __("Stations") . '</h4>
                          </div>';
                    foreach ($stations as $station) {
                        include 'station_card.php';
                    }
                }
                    // Suche in der Podcasts-Tabelle mit Kategorie-Name, wenn der Benutzer Stuneo+ ist
                    $stmtPodcasts = $conn->prepare("
                        SELECT p.*, c.name AS category_name 
                        FROM podcasts p
                        LEFT JOIN podcast_categories c ON p.category_id = c.category_id
                        WHERE p.title LIKE :queryTitle OR p.description LIKE :queryDescription
                    ");
                    $stmtPodcasts->execute([
                        'queryTitle' => '%' . $query . '%',
                        'queryDescription' => '%' . $query . '%'
                    ]);
                    $podcasts = $stmtPodcasts->fetchAll(PDO::FETCH_ASSOC);

                    // Ergebnisse für Podcasts anzeigen
                    if (!empty($podcasts)) {
                        echo '<div class="column is-full">
                                <h4 class="has-text-white">' . __("Podcasts") . '</h4>
                              </div>';
                        foreach ($podcasts as $podcast) {
                            include 'podcast_card_fav.php';
                        }
                    }

            } catch (PDOException $e) {
                echo '<p class="column is-full has-text-white">' . __("An error occurred: ") . htmlspecialchars($e->getMessage()) . '</p>';
            }
            ?>
        <?php else: ?>
            <p class="column is-full has-text-white"><?= __("Please enter a search term."); ?></p>
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
