const bottomPlayer = document.getElementById('bottom-player');
const playerThumbnail = document.getElementById('player-thumbnail');
const playerTitle = document.getElementById('player-title');
const playerSubtitle = document.getElementById('player-subtitle');
const playerPlayButton = document.getElementById('player-play-button');
const volumeControl = document.getElementById('volume-control');
const progressBar = document.getElementById('progress-bar');

let currentPlayingAudio = null;
let isPlaying = false;

// Funktion: Play/Pause-Buttons aktualisieren
function updatePlayPauseButtons(audioSrc, isPlayingNow) {
    document.querySelectorAll('.podcast-play-button').forEach(button => {
        const buttonAudioSrc = button.getAttribute('data-audio');
        const icon = button.querySelector('i');
        if (buttonAudioSrc === audioSrc) {
            icon.classList.toggle('ti-player-play-filled', !isPlayingNow);
            icon.classList.toggle('ti-player-pause-filled', isPlayingNow);
        } else {
            icon.classList.remove('ti-player-pause-filled');
            icon.classList.add('ti-player-play-filled');
        }
    });

    const playerIcon = playerPlayButton.querySelector('i');
    playerIcon.classList.toggle('ti-player-play-filled', !isPlayingNow);
    playerIcon.classList.toggle('ti-player-pause-filled', isPlayingNow);
}

// Funktion: Fortschrittsbalken aktualisieren
function updateProgressBar() {
    if (currentPlayingAudio) {
        const progress = (currentPlayingAudio.currentTime / currentPlayingAudio.duration) * 100;
        progressBar.style.background = `linear-gradient(to right, #fa7109 0%, #fa7109 ${progress}%, #666 ${progress}%, #666 100%)`;
        progressBar.value = progress;
    }
}

// Funktion: Audio-Vorspulen
function seekAudio() {
    if (currentPlayingAudio) {
        const newTime = (progressBar.value / 100) * currentPlayingAudio.duration;
        currentPlayingAudio.currentTime = newTime;
    }
}

// Funktion: Fortschritt speichern
function saveUserPodcastProgress(podcastId, episodeGuid, currentTime) {
    if (!podcastId || !episodeGuid || isNaN(currentTime)) {
        return;
    }

    const formData = new FormData();
    formData.append('podcast_id', podcastId);
    formData.append('episode_guid', episodeGuid);
    formData.append('current_time', Math.floor(currentTime));

    fetch('save_progress.php', {
        method: 'POST',
        body: formData,
    });
}

// Event: Fortschrittsbalken ändern
progressBar.addEventListener('input', seekAudio);

// Event: Abspielen oder Neustarten eines Podcasts
document.addEventListener('DOMContentLoaded', () => {
    // Play-Button
    document.querySelectorAll('.podcast-play-button').forEach(button => {
        button.addEventListener('click', () => {
            const audioSrc = button.getAttribute('data-audio');
            const title = button.getAttribute('data-title');
            const subtitle = button.getAttribute('data-subtitle');
            const thumbnail = button.getAttribute('data-thumbnail');
            const episodeGuid = button.getAttribute('data-episode-guid');
            const podcastId = button.getAttribute('data-podcast-id');
            const currentProgress = parseFloat(button.getAttribute('data-current-progress')) || 0;

            if (currentPlayingAudio && currentPlayingAudio.src === audioSrc) {
                if (isPlaying) {
                    currentPlayingAudio.pause();
                } else {
                    currentPlayingAudio.play();
                }
                isPlaying = !isPlaying;
                updatePlayPauseButtons(audioSrc, isPlaying);
                return;
            }

            // Vorherigen Audio-Player zurücksetzen
            if (currentPlayingAudio) {
                currentPlayingAudio.pause();
                currentPlayingAudio.removeEventListener('timeupdate', updateProgressBar);
                currentPlayingAudio.removeEventListener('timeupdate', currentPlayingAudio._saveProgressHandler);
            }

            // Neuen Audio-Player initialisieren
            currentPlayingAudio = new Audio(audioSrc);
            currentPlayingAudio.volume = volumeControl.value;

            // Fortschritt setzen
            currentPlayingAudio.currentTime = currentProgress;

            const saveProgressHandler = () => {
                updateProgressBar();
                saveUserPodcastProgress(podcastId, episodeGuid, currentPlayingAudio.currentTime);

                // Button synchronisieren
                button.setAttribute('data-current-progress', currentPlayingAudio.currentTime);
            };

            currentPlayingAudio._saveProgressHandler = saveProgressHandler;

            currentPlayingAudio.addEventListener('timeupdate', saveProgressHandler);
            currentPlayingAudio.addEventListener('loadedmetadata', updateProgressBar);

            currentPlayingAudio.play();
            isPlaying = true;

            // Player-Details aktualisieren
            playerThumbnail.src = thumbnail;
            playerTitle.textContent = title;
            playerSubtitle.textContent = subtitle;

            updatePlayPauseButtons(audioSrc, isPlaying);
        });
    });

    // Reload-Button
    document.querySelectorAll('.restart-episode-button').forEach(button => {
        button.addEventListener('click', () => {
            const audioSrc = button.getAttribute('data-audio');
            const episodeGuid = button.getAttribute('data-episode-guid');
            const podcastId = button.getAttribute('data-podcast-id');

            if (currentPlayingAudio) {
                currentPlayingAudio.pause();
                currentPlayingAudio.removeEventListener('timeupdate', updateProgressBar);
                currentPlayingAudio.removeEventListener('timeupdate', currentPlayingAudio._saveProgressHandler);
            }

            currentPlayingAudio = new Audio(audioSrc);
            currentPlayingAudio.volume = volumeControl.value;
            currentPlayingAudio.currentTime = 0;

            const saveProgressHandler = () => {
                updateProgressBar();
                saveUserPodcastProgress(podcastId, episodeGuid, currentPlayingAudio.currentTime);

                const playButton = document.querySelector(`.podcast-play-button[data-episode-guid="${episodeGuid}"]`);
                if (playButton) {
                    playButton.setAttribute('data-current-progress', currentPlayingAudio.currentTime);
                }
            };

            currentPlayingAudio._saveProgressHandler = saveProgressHandler;

            currentPlayingAudio.addEventListener('timeupdate', saveProgressHandler);
            currentPlayingAudio.addEventListener('loadedmetadata', updateProgressBar);

            currentPlayingAudio.play();
            isPlaying = true;

            updatePlayPauseButtons(audioSrc, isPlaying);
        });
    });
});

// Event: Play/Pause-Button im Bottom-Player
playerPlayButton.addEventListener('click', () => {
    if (currentPlayingAudio) {
        if (isPlaying) {
            currentPlayingAudio.pause();
        } else {
            currentPlayingAudio.play();
        }
        isPlaying = !isPlaying;
        updatePlayPauseButtons(currentPlayingAudio.src, isPlaying);
    }
});

// Event: Lautstärke ändern
volumeControl.addEventListener('input', event => {
    if (currentPlayingAudio) {
        currentPlayingAudio.volume = event.target.value;
    }
});
