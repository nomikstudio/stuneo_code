// Funktion: Fortschrittsanzeige in der Episodenliste aktualisieren
function updateProgressBarInDOM(episodeGuid, progressPercent) {
    const progressBar = document.querySelector(`.progress-bar[data-episode-guid="${episodeGuid}"]`);
    if (progressBar) {
        progressBar.style.width = `${progressPercent}%`;
    }
}

// Funktion: Fortschritt im Hintergrund synchronisieren
function syncProgressInBackground() {
    if (!currentPlayingAudio || !userId) {
        return;
    }

    const audioSrc = currentPlayingAudio.src;
    const button = document.querySelector(`.podcast-play-button[data-audio="${audioSrc}"]`);
    if (!button) {
        return;
    }

    const episodeGuid = button.getAttribute('data-episode-guid')?.trim();
    const podcastId = button.getAttribute('data-podcast-id');

    if (!podcastId || !episodeGuid) {
        return;
    }

    const url = `get_progress.php?podcast_id=${podcastId}&episode_guid=${encodeURIComponent(episodeGuid)}&user_id=${userId}`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.error) {
                const newProgress = parseFloat(data.current_time);
                if (!isNaN(newProgress)) {
                    const progressPercent = Math.round((newProgress / currentPlayingAudio.duration) * 100);

                    // Fortschritt im Audio-Player aktualisieren
                    if (currentPlayingAudio.currentTime < newProgress) {
                        currentPlayingAudio.currentTime = newProgress;
                        updateProgressBar();
                    }

                    // Fortschrittsanzeige in der Episodenliste aktualisieren
                    updateProgressBarInDOM(episodeGuid, progressPercent);
                }
            }
        })
        .catch(() => {
            // Fehler während der Synchronisation ignorieren
        });
}

// Hintergrund-Synchronisation alle 5 Sekunden starten
if (typeof userId !== 'undefined' && userId) {
    setInterval(syncProgressInBackground, 5000);
}
