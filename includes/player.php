<!-- Unsichtbares Audio-Element -->
<audio id="audioPlayer" style="display: none;"></audio>

<!-- Fester Mini-Player (unten fixiert) -->
<div class="fixed-player">
    <div class="content has-text-white-ter is-flex is-justify-content-space-between is-align-items-center">
        
        <!-- Steuerungstasten -->
        <div class="controls is-flex is-align-items-center">
            <button class="control-button" onclick="previousTrack()"><i class="ti ti-player-skip-back-filled"></i></button>
            <button class="control-button" onclick="togglePlayPause()" style="position: relative;">
                <!-- Loader-Kreis -->
                <div id="loader_player" class="loader_player" style="display: none;"></div>
                <!-- Play/Pause-Icons -->
                <i class="ti ti-player-play-filled" id="playIcon"></i>
                <i class="ti ti-player-pause-filled" id="pauseIcon" style="display: none;"></i>
            </button>
            <button class="control-button" onclick="nextTrack()"><i class="ti ti-player-skip-forward-filled"></i></button>
        </div>

        <!-- Song- und Stationsinformationen -->
        <div class="station-info is-flex is-align-items-center">
            <img src="src/img/no_image.jpg" alt="Station Logo" id="playerStationLogo" class="station-logo-img">
            <div class="station-text">
                <h1 class="song-title" id="currentStationName" style="font-size: 24px; letter-spacing: 0.00em;">Select station</h1>
                <p class="station-email mt-0" id="currentsongTitle"><i class="ri-broadcast-line"></i> LIVE</p>
            </div>
        </div>
        
        <!-- Zusätzliche Steuerungselemente -->
        <div class="additional-controls is-flex is-align-items-center">
            <!-- Owner-Link im Mini-Player mit Tooltip -->
            <a href="#" id="currentStationOwnerUrl" class="station-owner-link mr-3 hide-on-mobile">
                <i class="ri-user-fill ri-xl"></i>
                <span class="tooltip" id="ownerTooltip">Unknown</span>
            </a>
            <button class="is-light" id="favoriteButton-mini" onclick="toggleFavorite(currentStationId)">
                <i class="ri-heart-line ri-xl mr-3" id="favoriteIcon-mini"></i>
            </button>
            <div class="volume-controls">
                <i class="ri-volume-up-line ri-xl mr-2" id="volumeIcon"></i>
                <input type="range" id="volumeSlider" min="0" max="1" step="0.01" value="1" onchange="setVolume(this.value)">
            </div>
            <button class="control-button" onclick="toggleOverlay()"><i class="ri-arrow-up-s-line"></i></button>
        </div>
    </div>
</div>

<!-- Overlay für die Übersicht -->
<div class="overlay" id="overlay">
    <!-- Schließen-Button -->
    <button class="close-button" onclick="toggleOverlay()">
        <i class="ri-close-line"></i>
    </button>

    <div class="overlay-content">

        <!-- Station Logo -->
        <img src="https://www.eclosio.ong/wp-content/uploads/2018/08/default.png" alt="Station Logo" id="overlayStationLogo" class="overlay-logo">

        <!-- Station Titel -->
        <h1 class="overlay-title" id="currentStationNameOverlay">Station Name</h1>

        <!-- Aktuelle Sendung / Künstler -->
        <p class="overlay-artist" id="currentSongTitleOverlay"><i class="ri-broadcast-line"></i> LIVE</p>

        <!-- Station Owner Information -->
        <div class="overlay-owner-info">
            <small>
                <a href="#" id="overlayStationOwnerUrl" class="station-owner-link">
                    <i class="ri-user-fill mr-1"></i> <span id="overlayStationOwnerName">Unknown</span>
                </a>
            </small>
        </div>

        <!-- Steuerungselemente -->
        <div class="overlay-controls">
            <button class="control-button" onclick="previousTrack()">
                <i class="ti ti-player-skip-back-filled" style="font-size: 2rem;"></i>
            </button>
            <button class="control-button" onclick="togglePlayPause()">
                <i class="ti ti-player-play-filled" style="font-size: 3rem;" id="playIconOverlay"></i>
                <i class="ti ti-player-pause-filled" id="pauseIconOverlay" style="display: none; font-size: 3rem;"></i>
            </button>
            <button class="control-button" onclick="nextTrack()">
                <i class="ti ti-player-skip-forward-filled" style="font-size: 2rem;"></i>
            </button>
        </div>
    </div>
</div>

<!-- Integrierter Code in deiner player.php -->
<script src="src/js/player_latest.js?v=<?= time(); ?>"></script>
<link rel="stylesheet" href="src/css/player.css?v=<?= time(); ?>">
<script src="src/js/player_img.js?v=<?= time(); ?>"></script>
