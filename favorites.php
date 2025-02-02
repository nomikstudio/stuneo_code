<?php 
$page = "Favorites";
include 'access_control.php';
include_once('includes/header.php');
include_once('includes/nav-mobile.php');
include_once('includes/sidebar.php');

// Benutzer-ID aus der Session
$user_id = $_SESSION['user_data']['user_id'];

// Token für die Favoritenliste abrufen oder erstellen
$stmt = $conn->prepare("SELECT share_token FROM favorites_tokens WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$token = $stmt->fetchColumn();

// Genehmigte Favoriten des Benutzers abrufen
$stmt = $conn->prepare("SELECT * FROM stations WHERE station_id IN (SELECT station_id FROM favorites WHERE user_id = :user_id) AND status = 'approved'");
$stmt->execute(['user_id' => $user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Genehmigte Favoriten für Podcasts abrufen, einschließlich Kategorien
$stmt = $conn->prepare("
    SELECT p.*, c.name AS category_name 
    FROM podcasts p
    LEFT JOIN podcast_categories c ON p.category_id = c.category_id
    WHERE p.podcast_id IN (
        SELECT podcast_id 
        FROM favorites_podcast 
        WHERE user_id = :user_id
    )
");
$stmt->execute(['user_id' => $user_id]);
$favoritesPodcasts = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Favoriten-Array für die Anzeige des gefüllten Herzsymbols
$favoritenArray = array_column($favorites, 'station_id');

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
            <h3 class="has-text-white" style="font-size: 24px; font-weight: 800;"><?php echo __('All favorites'); ?></h3>
        </div>
        <div class="level-right hide-on-mobile">
            <?php if ($token): ?>
                <!-- Link anzeigen -->
                <button class="button is-primary mr-3" style="border-radius: 8px; font-weight: bold;" onclick="showFavoritesShareModal('<?php echo $token; ?>')">
                    <i class="ri-share-line mr-1"></i><?php echo __('View Link'); ?>
                </button>
                <!-- Link löschen -->
                <button class="button is-danger mr-3" style="border-radius: 8px; font-weight: bold;" onclick="deleteShareLink()">
                    <i class="ri-delete-bin-line mr-1"></i><?php echo __('Delete Link'); ?>
                </button>
            <?php else: ?>
                <!-- Link erstellen -->
                <button class="button is-primary mr-3" style="border-radius: 8px; font-weight: bold;" onclick="createShareLink()">
                    <i class="ri-share-line mr-1"></i><?php echo __('Create Link'); ?>
                </button>
            <?php endif; ?>
            <?php include_once('includes/account_button.php'); ?>

        </div>
    </header>

    <!-- Alle Favoriten anzeigen -->
    <header class="level mb-2">
        <div class="level-left">
            <h3 class="has-text-white" style="font-size: 24px;">
                <?php echo __('Your Stations'); ?>
            </h3>
        </div>
    </header>
    <div class="columns is-multiline">
        <?php if (count($favorites) > 0): ?>
            <?php foreach ($favorites as $station): ?>
                <?php include 'station_card.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="column is-full has-text-white" style="font-size: 14px;"><?php echo __('You have no favorite stations.'); ?></p>
        <?php endif; ?>
    </div>
    <!-- Favoritenbereich: Podcasts -->
    <header class="level mt-6">
        <div class="level-left">
            <h3 class="has-text-white" style="font-size: 24px; font-weight: 800;">
                <?php echo __('Your Podcasts'); ?>
            </h3>
        </div>
    </header>
    <div class="columns is-multiline" style="gap: 0.5rem;">
        <?php if (count($favoritesPodcasts) > 0): ?>
            <?php foreach ($favoritesPodcasts as $podcast): ?>
                <?php include 'podcast_card_fav.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="column is-full has-text-white" style="font-size: 14px;">
                <?php echo __('You have no favorite podcasts.'); ?>
            </p>
        <?php endif; ?>
    </div>

</div>

<!-- Modal für Teilen -->
<div id="favoritesShareModal" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="box modal-box-custom">
            <!-- X-Button oben rechts -->
            <button class="modal-close-custom" aria-label="close" onclick="closeFavoritesShareModal()">&times;</button>
            <h3 class="title is-4"><?php echo __('Your Share Link'); ?></h3>
            <p class="mb-2"><b><?php echo __('Share this link with others to let them view your favorites:'); ?></b></p>
            <input type="text" id="favoritesShareLink"  class="input is-light" readonly>
            <button class="button is-primary mt-4" onclick="copyFavoritesShareLink()"><?php echo __(key: 'Copy link'); ?></button>
        </div>
    </div>
</div>

<!-- Rückmeldungen -->
<?php if (isset($error) && !empty($error)): ?>
    <script>
        showToast('Error', '<?= addslashes($error) ?>', 'error');
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
    <script>
        showToast('Success', '<?= addslashes($_SESSION['success']) ?>', 'success');
    </script>
    <?php unset($_SESSION['success']); // Erfolgs-Session nach dem Toast löschen ?>
<?php endif; ?>

<script>
// Zeigt das Modal mit dem Link an
function showFavoritesShareModal(token) {
    const shareModal = document.getElementById('favoritesShareModal');
    const shareLinkInput = document.getElementById('favoritesShareLink');
    shareLinkInput.value = `${window.location.origin}/list/${token}`;
    shareModal.classList.add('is-active');
}

// Schließt das Modal
function closeFavoritesShareModal() {
    const shareModal = document.getElementById('favoritesShareModal');
    shareModal.classList.remove('is-active');
}

// Kopiert den Link in die Zwischenablage
function copyFavoritesShareLink() {
    const shareLinkInput = document.getElementById('favoritesShareLink');
    shareLinkInput.select();
    document.execCommand('copy');
    alert('<?php echo __('Link copied to clipboard!'); ?>');
}

// Erstellt einen neuen Link
function createShareLink() {
    fetch('create_share_link.php', {
        method: 'POST'
    }).then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              showToast('<?php echo __('Success'); ?>', '<?php echo __('Share link created successfully.'); ?>', 'success');
              location.reload(); // Seite neu laden, um den neuen Token anzuzeigen
          } else {
              showToast('<?php echo __('Error'); ?>', data.message, 'error');
          }
      }).catch(error => {
          showToast('<?php echo __('Error'); ?>', '<?php echo __('An unexpected error occurred.'); ?>', 'error');
          console.error(error);
      });
}

// Löscht den existierenden Link
function deleteShareLink() {
    fetch('delete_share_link.php', {
        method: 'POST'
    }).then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              showToast('<?php echo __('Success'); ?>', '<?php echo __('Share link deleted successfully.'); ?>', 'success');
              location.reload(); // Seite neu laden, um die Buttons zu aktualisieren
          } else {
              showToast('<?php echo __('Error'); ?>', data.message, 'error');
          }
      }).catch(error => {
          showToast('<?php echo __('Error'); ?>', '<?php echo __('An unexpected error occurred.'); ?>', 'error');
          console.error(error);
      });
}

</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const favoriteButtons = document.querySelectorAll(".favorite-btn");

    favoriteButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const podcastId = this.getAttribute("data-podcast-id");
            const heartIcon = document.getElementById(`heart-icon-${podcastId}`);

            fetch("toggle_favorite_podcast.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ podcast_id: podcastId }),
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.status === "added") {
                        heartIcon.classList.remove("ri-heart-line");
                        heartIcon.classList.add("ri-heart-fill");
                        heartIcon.style.color = "white";
                    } else if (data.status === "removed") {
                        heartIcon.classList.remove("ri-heart-fill");
                        heartIcon.classList.add("ri-heart-line");
                        heartIcon.style.color = "inherit";
                        // Seite neu laden nach Entfernen
                        setTimeout(() => {
                            location.reload();
                        }, 500); // Optional: Verzögerung, um die UI-Aktualisierung abzuwarten
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
