// JavaScript für den Audio-Player mit Fortschrittsbalken
const bottomPlayer = document.getElementById('bottom-player');
const playerThumbnail = document.getElementById('player-thumbnail');
const playerTitle = document.getElementById('player-title');
const playerSubtitle = document.getElementById('player-subtitle');
const playerPlayButton = document.getElementById('player-play-button');
const volumeControl = document.getElementById('volume-control');
const progressBar = document.getElementById('progress-bar');

let currentPlayingAudio = null;
let isPlaying = false;
let lastSavedTime = 0; // Letzter gespeicherter Fortschritt

// Fortschritt speichern
function saveUserPodcastProgress(audioSrc, currentTime) {
    const button = document.querySelector(`.podcast-play-button[data-audio="${audioSrc}"]`);
    const podcastId = button ? button.getAttribute('data-podcast-id') : null;
    const userId = 1; // Beispiel: Benutzer-ID

    if (!podcastId) {
        console.error('podcast_id is null');
        return;
    }

    // Fortschritt nur alle 5 Sekunden speichern
    if (Math.floor(currentTime) - lastSavedTime >= 5) {
        lastSavedTime = Math.floor(currentTime);

        const payload = {
            user_id: userId,
            podcast_id: podcastId,
            episode_guid: audioSrc,
            current_time_user: Math.floor(currentTime),
        };

        console.log("Payload for save_progress.php:", payload);

        fetch('/save_progress.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Fortschritt erfolgreich gespeichert.');
                } else {
                    console.error('Fehler beim Speichern des Fortschritts:', data.message);
                }
            })
            .catch(error => {
                console.error('Netzwerkfehler beim Speichern des Fortschritts:', error);
            });
    }
}

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
    if (isPlayingNow) {
        playerIcon.classList.remove('ti-player-play-filled');
        playerIcon.classList.add('ti-player-pause-filled');
    } else {
        playerIcon.classList.remove('ti-player-pause-filled');
        playerIcon.classList.add('ti-player-play-filled');
    }
}

function updateProgressBar() {
    if (currentPlayingAudio) {
        const progress = (currentPlayingAudio.currentTime / currentPlayingAudio.duration) * 100;
        progressBar.style.background = `linear-gradient(to right, #fa7109 0%, #fa7109 ${progress}%, #666 ${progress}%, #666 100%)`;
        progressBar.value = progress;
    }
}

function seekAudio() {
    if (currentPlayingAudio) {
        const newTime = (progressBar.value / 100) * currentPlayingAudio.duration;
        currentPlayingAudio.currentTime = newTime;
    }
}

progressBar.addEventListener('input', seekAudio);

document.querySelectorAll('.podcast-play-button').forEach(button => {
    button.addEventListener('click', () => {
        const audioSrc = button.getAttribute('data-audio');
        const title = button.getAttribute('data-title');
        const subtitle = button.getAttribute('data-subtitle');
        const thumbnail = button.getAttribute('data-thumbnail');

        if (currentPlayingAudio && currentPlayingAudio.src === audioSrc) {
            if (isPlaying) {
                currentPlayingAudio.pause();
                isPlaying = false;
            } else {
                currentPlayingAudio.play();
                isPlaying = true;
            }
        } else {
            if (currentPlayingAudio) {
                currentPlayingAudio.pause();
                currentPlayingAudio.removeEventListener('timeupdate', updateProgressBar);
                currentPlayingAudio.removeEventListener('timeupdate', saveUserPodcastProgress);
                currentPlayingAudio.removeEventListener('loadedmetadata', updateProgressBar);
            }
            currentPlayingAudio = new Audio(audioSrc);
            currentPlayingAudio.volume = volumeControl.value;

            // Fortschritt laden
            fetch(`/load_progress.php?episode_guid=${audioSrc}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Fortschritt geladen:', data);
                    if (data.success && data.current_time_user) {
                        currentPlayingAudio.currentTime = data.current_time_user;
                    }
                })
                .catch(error => console.error('Fehler beim Laden des Fortschritts:', error));

            currentPlayingAudio.addEventListener('timeupdate', () => {
                updateProgressBar();
                saveUserPodcastProgress(audioSrc, currentPlayingAudio.currentTime);
            });

            currentPlayingAudio.addEventListener('loadedmetadata', updateProgressBar);

            currentPlayingAudio.play();
            isPlaying = true;

            playerThumbnail.src = thumbnail;
            playerTitle.textContent = title;
            playerSubtitle.textContent = subtitle;
        }

        updatePlayPauseButtons(audioSrc, isPlaying);
    });
});

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

volumeControl.addEventListener('input', (event) => {
    if (currentPlayingAudio) {
        currentPlayingAudio.volume = event.target.value;
    }
});
