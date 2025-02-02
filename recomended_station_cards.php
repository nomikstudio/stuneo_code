<?php
// Funktion zum Überprüfen, ob eine Bild-URL gültig ist
function isImageAvailable($url) {
    $headers = @get_headers($url);
    if (!$headers || strpos($headers[0], '200') === false) {
        return false;
    }
    $fileType = exif_imagetype($url);
    return in_array($fileType, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF]);
}

// Station und Besitzerinformationen vorbereiten
$station_id = isset($station['station_id']) ? htmlspecialchars($station['station_id']) : null;
$station_name = isset($station['name']) ? htmlspecialchars($station['name']) : 'Unknown Station';
$station_description = isset($station['description']) ? htmlspecialchars($station['description']) : 'No description available';
$station_stream_url = isset($station['stream_url']) ? htmlspecialchars($station['stream_url']) : '';
$station_logo_url = isset($station['logo_url']) && isImageAvailable($station['logo_url']) 
    ? htmlspecialchars($station['logo_url']) 
    : 'src/img/no_image.jpg';

$station_owner_name = isset($owner_station[$station_id]['name']) ? htmlspecialchars($owner_station[$station_id]['name']) : 'Unknown';
$station_owner_slug = isset($owner_station[$station_id]['slug']) ? htmlspecialchars($owner_station[$station_id]['slug']) : '';
$station_owner_url = $station_owner_slug ? "owner/" . $station_owner_slug : '#';

$isFavorite = isset($favoritenArray) && is_array($favoritenArray) && in_array($station_id, $favoritenArray);
?>

<div class="recommended-card">
    <div class="recommended-content">
        <p class="recommended-label"><?php echo __('Recommended for you'); ?></p>
        <h2 class="recommended-title"><?= htmlspecialchars_decode($station_name); ?></h2>
        <p class="recommended-subtitle"><?= htmlspecialchars_decode($station_description); ?></p>

        <!-- Buttons -->
        <div class="recommended-buttons">
            <button class="play-button" id="playButton-<?= htmlspecialchars($station_id); ?>" data-station-id="<?= htmlspecialchars($station_id); ?>"
                onclick="playStation(
                    '<?= htmlspecialchars($station_id); ?>', 
                    '<?= htmlspecialchars($station_name); ?>', 
                    '<?= htmlspecialchars($station_stream_url); ?>', 
                    '<?= htmlspecialchars($station_logo_url); ?>', 
                    '<?= htmlspecialchars($station_owner_name); ?>', 
                    '<?= htmlspecialchars($station_owner_slug); ?>'
                )">
                <i class="play-icon play-icon-<?= $station_id; ?> ti ti-player-play-filled"></i>
                <i class="pause-icon pause-icon-<?= $station_id; ?> ti ti-player-pause-filled" style="display: none;"></i>
            </button>
            <?php if ($station_id): ?>
                        <button class="favorite-button" id="favoriteButton-<?= htmlspecialchars($station_id); ?>" 
                            onclick="toggleFavorite('<?= $station_id; ?>')">
                            <i class="ri-heart-<?= $isFavorite ? 'fill' : 'line'; ?>" id="favoriteIcon-<?= $station_id; ?>"></i>
                        </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="recommended-image" style="background-image: url('<?= htmlspecialchars($station_logo_url); ?>');"></div>
</div>

<!-- Login-Modal -->
<div id="loginModal" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="box modal-box-custom">
            <button class="modal-close-custom" aria-label="close">&times;</button>
            <h3 class="title is-4"><?php echo __('Please login'); ?></h3>
            <p><?php echo __('You need to be logged in to use this feature.'); ?></p>
            <div class="buttons mt-4">
                <a href="login.php" class="button is-primary"><?php echo __('Login'); ?></a>
                <a href="register.php" class="button"><?php echo __('Register'); ?></a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const isLoggedIn = <?= json_encode($is_logged_in); ?>; // PHP-Wert in JS verfügbar machen
    const playButtons = document.querySelectorAll('.play-button');
    const loginModal = document.querySelector('#loginModal');
    const closeModalButton = document.querySelector('.modal-close-custom');
    const modalBackground = document.querySelector('.modal-background');

    playButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            if (!isLoggedIn) {
                e.preventDefault();
                loginModal.classList.add('is-active'); // Modal öffnen
            }
        });
    });

    // Modal schließen
    closeModalButton.addEventListener('click', () => {
        loginModal.classList.remove('is-active');
    });

    modalBackground.addEventListener('click', () => {
        loginModal.classList.remove('is-active');
    });
});

</script>