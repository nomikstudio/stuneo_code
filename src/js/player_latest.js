let currentAudio = null;
let isPlaying = false;
let currentStationId = null;
let currentStationIndex = 0;
let stations = [];

// Stationsdaten vom Server laden
async function loadStations() {
    try {
        const response = await fetch('get_stations.php');
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();

        stations = data.filter(station => station.streamUrl && isValidUrl(station.streamUrl));

        if (stations.length === 0) {
            console.error('No valid stations found');
            alert('No valid stations available to play.');
        } else {
            console.log('Stations loaded successfully:', stations);
            loadLastStation() || playCurrentStation(false);
        }
    } catch (error) {
        console.error('Error loading stations:', error);
        alert('Failed to load stations. Please try again later.');
    }
}

// Speicherplatzmanagement: Alte Einträge bereinigen
function cleanUpLocalStorage() {
    const maxEntries = 50; // Maximale Anzahl der gespeicherten Stationen
    const cachedStations = JSON.parse(localStorage.getItem('stations'));
    if (cachedStations && cachedStations.length > maxEntries) {
        const trimmedStations = cachedStations.slice(0, maxEntries);
        localStorage.setItem('stations', JSON.stringify(trimmedStations));
    }
}



// Zur nächsten Station wechseln
function nextTrack() {
    if (stations.length > 0) {
        currentStationIndex = (currentStationIndex + 1) % stations.length;
        playCurrentStation();
        preloadNextStation(); // Nächste Station vorladen
    } else {
        console.warn('No stations available to switch to next.');
    }
}

// Zur vorherigen Station wechseln
function previousTrack() {
    if (stations.length > 0) {
        currentStationIndex = (currentStationIndex - 1 + stations.length) % stations.length;
        playCurrentStation();
        preloadPreviousStation(); // Vorherige Station vorladen
    } else {
        console.warn('No stations available to switch to previous.');
    }
}

// Nächste Station vorladen
function preloadNextStation() {
    const nextIndex = (currentStationIndex + 1) % stations.length;
    preloadStation(nextIndex);
}

// Vorherige Station vorladen
function preloadPreviousStation() {
    const prevIndex = (currentStationIndex - 1 + stations.length) % stations.length;
    preloadStation(prevIndex);
}

// Station vorladen
function preloadStation(index) {
    const station = stations[index];
    if (station) {
        let preloader = new Audio(station.streamUrl); // Ändere const zu let
        preloader.preload = 'auto';
        preloader.oncanplaythrough = () => {
            console.log(`Preloaded station: ${station.name}`);
            preloader.pause(); // Sicherstellen, dass nichts abgespielt wird
            preloader = null;  // Referenz entfernen
        };
    } else {
        console.warn(`No station found at index: ${index}`);
    }
}

// Station abspielen
function playCurrentStation(saveToLocalStorage = true) {
    if (stations.length === 0) return;
    const station = stations[currentStationIndex];
    if (station) {
        playStation(
            station.id,
            station.name,
            station.streamUrl,
            station.logoUrl,
            station.ownerName,
            station.ownerSlug,
            saveToLocalStorage
        );
    }
}


// Play oder Pause der aktuellen Station umschalten
function togglePlayPause() {
    const loaderPlayer = document.getElementById('loader_player');

    if (currentAudio) {
        if (isPlaying) {
            currentAudio.pause();
            isPlaying = false;
            updateIcons(currentStationId, false);
            if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
        } else {
            if (loaderPlayer) loaderPlayer.style.display = 'block'; // Loader anzeigen
            currentAudio.play().then(() => {
                isPlaying = true;
                updateIcons(currentStationId, true);
                if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
            }).catch(error => {
                console.error('Error playing audio:', error);
                if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
            });
        }
    }
}

// Letzte Station aus localStorage laden
function loadLastStation() {
    const lastStation = JSON.parse(localStorage.getItem('currentStation'));
    if (lastStation) {
        playStation(
            lastStation.stationId,
            lastStation.stationName,
            lastStation.streamUrl,
            lastStation.logoUrl,
            lastStation.ownerName,
            lastStation.ownerSlug,
            false
        );
        return true;
    }
    return false;
}

function playStation(stationId, stationName, streamUrl, logoUrl = '', ownerName = 'Unknown', ownerSlug = '#', saveToLocalStorage = true) {
    console.log("playStation called with:", { stationId, stationName, streamUrl });

    const loaderPlayer = document.getElementById('loader_player');
    const audioPlayer = document.getElementById('audioPlayer'); // Audio-Element

    if (!audioPlayer) {
        console.error("Error: Element with ID 'audioPlayer' not found.");
        return;
    }

    // Prüfen, ob dieselbe Station bereits läuft
    if (currentStationId === stationId) {
        togglePlayPause();
        if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
        return;
    }

    // Bestehendes Audio pausieren und Speicher freigeben
    if (currentAudio) {
        currentAudio.pause();
        currentAudio.src = ''; // Speicher freigeben
        currentAudio = null;
        updateIcons(currentStationId, false);
    }

    // Stream-URL prüfen
    if (!streamUrl || !isValidUrl(streamUrl)) {
        console.error("Invalid or missing stream URL:", streamUrl);
        if (loaderPlayer) loaderPlayer.style.display = 'none';
        alert("Error: Invalid stream URL. Cannot play this station.");
        return;
    }

    // Ladeanzeige zeigen
    if (loaderPlayer) loaderPlayer.style.display = 'block';

    // Update der UI mit Stationsinformationen
    updateStationUI(stationName, logoUrl, ownerName, ownerSlug);

    currentStationId = stationId;
    currentAudio = audioPlayer;
    currentAudio.src = streamUrl;

    const volumeSlider = document.getElementById('volumeSlider');
    const savedVolume = localStorage.getItem('volume') || 1; // Standardlautstärke auf 1
    currentAudio.volume = parseFloat(savedVolume);
    volumeSlider.value = savedVolume;

    // Für .m3u8 HLS.js verwenden
    if (streamUrl.endsWith('.m3u8')) {
        if (Hls.isSupported()) {
            const hls = new Hls();
            hls.loadSource(streamUrl);
            hls.attachMedia(audioPlayer);
            hls.on(Hls.Events.MANIFEST_PARSED, () => {
                audioPlayer.play()
                    .then(() => {
                        console.log(`Playing HLS stream: ${streamUrl}`);
                        isPlaying = true;
                        updateIcons(stationId, true);
                        fetchSongTitle(stationName);
                        updateListenCount(stationId);
                    })
                    .catch(error => {
                        console.error('Error playing HLS stream:', error);
                    })
                    .finally(() => {
                        if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
                    });
            });
        } else if (audioPlayer.canPlayType('application/vnd.apple.mpegurl')) {
            // Native HLS-Unterstützung (z. B. Safari)
            audioPlayer.src = streamUrl;
            audioPlayer.play()
                .then(() => {
                    console.log(`Playing native HLS stream: ${streamUrl}`);
                    isPlaying = true;
                    updateIcons(stationId, true);
                    fetchSongTitle(stationName);
                    updateListenCount(stationId);
                })
                .catch(error => {
                    console.error('Error playing native HLS stream:', error);
                })
                .finally(() => {
                    if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
                });
        } else {
            console.error('HLS is not supported in this browser.');
            alert('Your browser does not support HLS playback. Please use a supported browser.');
            if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
        }
    } else {
        // Für andere Audio-Dateiformate
        audioPlayer.src = streamUrl;
        audioPlayer.play()
            .then(() => {
                console.log(`Playing non-HLS audio: ${streamUrl}`);
                isPlaying = true;
                updateIcons(stationId, true);
                fetchSongTitle(stationName);
                updateListenCount(stationId);
            })
            .catch(error => {
                console.error('Error playing audio stream:', error);
            })
            .finally(() => {
                if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
            });
    }

    // Ereignis bei Ende des Streams
    audioPlayer.onended = () => {
        isPlaying = false;
        updateIcons(stationId, false);
    };

    // Favoritenstatus prüfen
    checkFavoriteStatus(stationId);

    // Station in localStorage speichern
    if (saveToLocalStorage) {
        saveStationToLocalStorage(stationId, stationName, streamUrl, logoUrl, ownerName, ownerSlug);
    }
}


// UI mit Stationsinformationen aktualisieren
function updateStationUI(stationName, logoUrl, ownerName, ownerSlug) {
    document.getElementById('currentStationName').innerText = stationName;

    const validatedLogoUrl = isValidUrl(logoUrl) ? logoUrl : 'https://www.eclosio.ong/wp-content/uploads/2018/08/default.png';
    document.getElementById('playerStationLogo').src = validatedLogoUrl;
    document.getElementById('overlayStationLogo').src = validatedLogoUrl;

    updateOwnerInfo(ownerName, ownerSlug);
}

// Station in localStorage speichern
function saveStationToLocalStorage(stationId, stationName, streamUrl, logoUrl, ownerName, ownerSlug) {
    const validatedLogoUrl = isValidUrl(logoUrl) ? logoUrl : 'https://www.eclosio.ong/wp-content/uploads/2018/08/default.png';
    const stationData = {
        stationId,
        stationName,
        streamUrl,
        logoUrl: validatedLogoUrl,
        ownerName,
        ownerSlug
    };
    localStorage.setItem('currentStation', JSON.stringify(stationData));
    cleanUpLocalStorage();
}

// URL-Validierung: Prüfen, ob die URL gültig ist
function isValidUrl(url) {
    try {
        const validUrl = new URL(url);
        return validUrl.protocol === 'http:' || validUrl.protocol === 'https:';
    } catch (_) {
        return false;
    }
}


// Titel und Künstler der Station abrufen
function fetchSongTitle(stationName) {
    fetch(`get_song_title?station=${encodeURIComponent(stationName)}`)
        .then(response => response.json())
        .then(data => {
            const songTitle = data.songTitle || "";
            const artist = data.artist || "";
            updatePageTitleAndPlayingInfo(stationName, songTitle, artist);
        })
        .catch(error => console.error('Error fetching song title:', error));
}


// Icons für Play/Pause aktualisieren
function updateIcons(stationId, isPlaying) {
    const playIconPlayer = document.getElementById('playIcon');
    const pauseIconPlayer = document.getElementById('pauseIcon');
    const playIconOverlay = document.getElementById('playIconOverlay');
    const pauseIconOverlay = document.getElementById('pauseIconOverlay');

    if (playIconPlayer && pauseIconPlayer) {
        playIconPlayer.style.display = isPlaying ? 'none' : 'block';
        pauseIconPlayer.style.display = isPlaying ? 'block' : 'none';
    }
    if (playIconOverlay && pauseIconOverlay) {
        playIconOverlay.style.display = isPlaying ? 'none' : 'block';
        pauseIconOverlay.style.display = isPlaying ? 'block' : 'none';
    }

    const playIcons = document.querySelectorAll(`.play-icon-${stationId}`);
    const pauseIcons = document.querySelectorAll(`.pause-icon-${stationId}`);
    
    playIcons.forEach(playIcon => playIcon.style.display = isPlaying ? 'none' : 'block');
    pauseIcons.forEach(pauseIcon => pauseIcon.style.display = isPlaying ? 'block' : 'none');
}

// Titel und Informationen der Seite aktualisieren
function updatePageTitleAndPlayingInfo(stationName, songTitle = "", artist = "") {
    const titleText = songTitle && artist 
        ? `${stationName} - ${songTitle} by ${artist} | stuneo` 
        : `${stationName} | stuneo`;
    document.title = titleText;

    const playingInfo = songTitle && artist 
        ? `${songTitle} by ${artist}` 
        : `<i class="ri-broadcast-line ri-lg"></i> LIVE`;

    document.getElementById('currentStationName').innerText = stationName;
    document.getElementById('currentsongTitle').innerHTML = playingInfo;

    document.getElementById('currentStationNameOverlay').innerText = stationName;
    document.getElementById('currentSongTitleOverlay').innerHTML = playingInfo;
}



// Besitzerinformationen aktualisieren
function updateOwnerInfo(ownerName, ownerSlug) {
    const ownerLink = document.getElementById('currentStationOwnerUrl');
    const ownerTooltip = document.getElementById('ownerTooltip');
    const ownerNameElement = document.getElementById('currentStationOwnerName');
    const overlayOwnerLink = document.getElementById('overlayStationOwnerUrl');
    const overlayOwnerNameElement = document.getElementById('overlayStationOwnerName');

    const ownerUrl = ownerSlug ? `owner/${ownerSlug}` : '#';

    if (ownerLink) ownerLink.href = ownerUrl;
    if (ownerTooltip) ownerTooltip.innerText = ownerName || "Unknown Owner";
    if (ownerNameElement) ownerNameElement.innerText = ownerName;
    if (overlayOwnerLink) overlayOwnerLink.href = ownerUrl;
    if (overlayOwnerNameElement) overlayOwnerNameElement.innerText = ownerName || "Unknown Owner";
}


// Hörerkontingent der Station aktualisieren
function updateListenCount(stationId) {
    if (!stationId) {
        console.error('Invalid stationId:', stationId);
        return;
    }

    fetch(`update_listen_count.php?station_id=${encodeURIComponent(stationId)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Listen count updated successfully');
            } else {
                console.error('Failed to update listen count:', data.error);
            }
        })
        .catch(error => console.error('Error updating listen count:', error));
}

// Lautstärke festlegen
function setVolume(value) {
    const volumeSlider = document.getElementById('volumeSlider');
    volumeSlider.value = value;

    if (currentAudio) {
        currentAudio.volume = value;
        isMuted = value === 0;
        updateMuteIcon();
    }

    localStorage.setItem('volume', value);
}

// Lautstärke bei Seiten-Reload anwenden
document.addEventListener('DOMContentLoaded', () => {
    const volumeSlider = document.getElementById('volumeSlider');
    const savedVolume = localStorage.getItem('volume') || 1;
    volumeSlider.value = savedVolume;

    if (currentAudio) {
        currentAudio.volume = parseFloat(savedVolume);
    }

    volumeSlider.addEventListener('input', (e) => setVolume(e.target.value));
});

// Speicherplatzmanagement beim Schließen
window.onbeforeunload = () => {
    if (currentAudio) {
        currentAudio.pause();
        currentAudio.src = '';
    }
    currentAudio = null;
};

// Stummschalten umschalten
function toggleMute() {
    const volumeSlider = document.getElementById('volumeSlider');
    if (currentAudio) {
        if (isMuted) {
            currentAudio.volume = volumeSlider.value;
            isMuted = false;
        } else {
            currentAudio.volume = 0;
            isMuted = true;
        }
        updateMuteIcon();
    }
}

// Lautstärke-Symbol basierend auf dem Stummschaltungsstatus aktualisieren
function updateMuteIcon() {
    const volumeIcon = document.getElementById('volumeIcon');
    if (isMuted) {
        volumeIcon.classList.remove('ri-volume-up-line');
        volumeIcon.classList.add('ri-volume-mute-line');
    } else {
        volumeIcon.classList.remove('ri-volume-mute-line');
        volumeIcon.classList.add('ri-volume-up-line');
    }
}

// Overlay umschalten
function toggleOverlay() {
    const overlay = document.querySelector('.overlay');
    const fixedPlayer = document.querySelector('.fixed-player');
    
    if (overlay.classList.contains('active')) {
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto';
        fixedPlayer.classList.remove('hidden');
    } else {
        const gradients = [
            "linear-gradient(135deg, #a34702, #803401 50%, #662d01"
        ];
        const randomGradient = gradients[Math.floor(Math.random() * gradients.length)];
        overlay.style.background = randomGradient;
        
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        fixedPlayer.classList.add('hidden');
    }
}


// Favoritenstatus umschalten
function toggleFavorite(stationId) {
    const favoriteIconMini = document.getElementById('favoriteIcon-mini');
    const action = favoriteIconMini.classList.contains('ri-heart-fill') ? 'remove' : 'add';

    fetch(`toggle_favorite.php?station_id=${stationId}&action=${action}`)
        .then(response => response.json())
        .then(data => {
            favoriteIconMini.classList.toggle('ri-heart-fill', action === 'add');
            favoriteIconMini.classList.toggle('ri-heart-line', action === 'remove');
            const favoriteIconCard = document.getElementById('favoriteIcon-' + stationId);
            if (favoriteIconCard) {
                favoriteIconCard.classList.toggle('ri-heart-fill', action === 'add');
                favoriteIconCard.classList.toggle('ri-heart-line', action === 'remove');
            }
        })
        .catch(error => console.error('Error updating favorites:', error));
}

// Favoritenstatus prüfen
function checkFavoriteStatus(stationId) {
    fetch(`check_favorite?station_id=${stationId}`)
        .then(response => response.json())
        .then(data => {
            const favoriteIconMini = document.getElementById('favoriteIcon-mini');
            if (data.isFavorite) {
                favoriteIconMini.classList.add('ri-heart-fill');
                favoriteIconMini.classList.remove('ri-heart-line');
            } else {
                favoriteIconMini.classList.add('ri-heart-line');
                favoriteIconMini.classList.remove('ri-heart-fill');
            }
        })
        .catch(error => console.error('Error checking favorite status:', error));
}



// Event-Listener für den Lautstärkeregler
document.getElementById('volumeSlider').addEventListener('input', (e) => setVolume(e.target.value));

// Speicherplatzverwaltung bei Stationswechsel
function manageResources() {
    if (currentAudio) {
        currentAudio.pause();
        currentAudio.src = ''; // Speicher freigeben
    }
    currentAudio = null;
}

// Ereignisse für das Fenster
window.onbeforeunload = manageResources; // Speicher freigeben beim Schließen


// Lade Stationen bei Seitenaufruf
window.onload = () => {
    loadStations();
    cleanUpLocalStorage();
};

