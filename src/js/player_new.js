let currentAudio = null;
let isPlaying = false;
let currentStationId = null;
let currentStationIndex = 0;
let stations = [];

// Stationsdaten vom Server laden
async function loadStations() {
    try {
        const cachedStations = JSON.parse(localStorage.getItem('stations'));
        if (cachedStations && cachedStations.length > 0) {
            console.log("Stations loaded from cache");
            stations = cachedStations;
            loadLastStation() || playCurrentStation(false);
        } else {
            console.log("Fetching stations from server");
            const response = await fetch('get_stations.php');
            if (!response.ok) throw new Error('Network response was not ok');
            stations = await response.json();
            if (stations.length > 0) {
                localStorage.setItem('stations', JSON.stringify(stations)); // Cache Stationsdaten
                loadLastStation() || playCurrentStation(false);
            } else {
                console.error('No stations found');
            }
        }
    } catch (error) {
        console.error('Error loading stations:', error);
    }
}

// Nächste und vorherige Tracks
function nextTrack() {
    if (stations.length > 0) {
        currentStationIndex = (currentStationIndex + 1) % stations.length;
        playCurrentStation();
        preloadNextStation(); // Puffer für nächste Station
    }
}

function previousTrack() {
    if (stations.length > 0) {
        currentStationIndex = (currentStationIndex - 1 + stations.length) % stations.length;
        playCurrentStation();
        preloadPreviousStation(); // Puffer für vorherige Station
    }
}

// Hintergrundpufferung für die nächste Station
function preloadNextStation() {
    const nextStationIndex = (currentStationIndex + 1) % stations.length;
    const nextStation = stations[nextStationIndex];
    if (nextStation) {
        const preloader = new Audio(nextStation.streamUrl);
        preloader.preload = 'auto';
        console.log(`Preloading next station: ${nextStation.name}`);
    }
}

// Hintergrundpufferung für die vorherige Station
function preloadPreviousStation() {
    const previousStationIndex = (currentStationIndex - 1 + stations.length) % stations.length;
    const previousStation = stations[previousStationIndex];
    if (previousStation) {
        const preloader = new Audio(previousStation.streamUrl);
        preloader.preload = 'auto';
        console.log(`Preloading previous station: ${previousStation.name}`);
    }
}

// Aktuelle Station abspielen
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

function playStation(stationId, stationName, streamUrl, logoUrl = '', ownerName = 'Unknown Owner', ownerSlug = '#', saveToLocalStorage = true) {
    console.log("playStation called with:", { stationId, stationName, streamUrl, logoUrl, ownerName, ownerSlug });

    const loaderPlayer = document.getElementById('loader_player');

    // Prüfen, ob dieselbe Station bereits läuft
    if (currentStationId === stationId) {
        togglePlayPause();
        if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
        return;
    }

    // Update der UI mit Stationsinformationen
    document.getElementById('currentStationName').innerText = stationName;
    const validatedLogoUrl = isValidUrl(logoUrl) ? logoUrl : 'https://www.eclosio.ong/wp-content/uploads/2018/08/default.png';
    document.getElementById('playerStationLogo').src = validatedLogoUrl;
    document.getElementById('overlayStationLogo').src = validatedLogoUrl;

    // Debugging: Überprüfen, ob die updateOwnerInfo-Funktion die richtigen Werte erhält
    updateOwnerInfo(ownerName, ownerSlug);

    // Ladeanzeige einblenden
    if (loaderPlayer) loaderPlayer.style.display = 'block';

    // Stoppe aktuelle Wiedergabe und setze zurück
    if (currentAudio) {
        currentAudio.pause();
        currentAudio.currentTime = 0;
        updateIcons(currentStationId, false);
    }

    // Setze Audio-Element und spiele neuen Stream
    currentStationId = stationId;
    currentAudio = new Audio(streamUrl);

    const streamTimeout = setTimeout(() => {
        console.error('Stream timeout reached');
        alert('Stream konnte nicht geladen werden. Bitte versuchen Sie es später erneut.');
        if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
        currentAudio.pause();
    }, 10000); // Timeout von 10 Sekunden

    currentAudio.play().then(() => {
        clearTimeout(streamTimeout); // Timeout abbrechen, wenn Stream lädt
        isPlaying = true;
        updateIcons(stationId, true);
        if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
        fetchSongTitle(stationName);
        updateListenCount(stationId);
    }).catch(error => {
        console.error('Error playing the stream:', error);
        if (loaderPlayer) loaderPlayer.style.display = 'none'; // Loader ausblenden
    });

    currentAudio.onended = function () {
        isPlaying = false;
        updateIcons(stationId, false);
    };

    checkFavoriteStatus(stationId);

    if (saveToLocalStorage) {
        const stationData = {
            stationId,
            stationName,
            streamUrl,
            logoUrl: isValidUrl(logoUrl) ? logoUrl : 'https://www.eclosio.ong/wp-content/uploads/2018/08/default.png',
            ownerName,
            ownerSlug
        };
        localStorage.setItem('currentStation', JSON.stringify(stationData));
    }
}


// Besitzerinformationen aktualisieren
function updateOwnerInfo(ownerName, ownerSlug) {
    const ownerLink = document.getElementById('currentStationOwnerUrl');
    const ownerTooltip = document.getElementById('ownerTooltip');
    const ownerNameElement = document.getElementById('currentStationOwnerName');
    const overlayOwnerLink = document.getElementById('overlayStationOwnerUrl');
    const overlayOwnerNameElement = document.getElementById('overlayStationOwnerName');

    const ownerUrl = ownerSlug ? `radio-owner?slug=${ownerSlug}` : '#';

    if (ownerLink) ownerLink.href = ownerUrl;
    if (ownerTooltip) ownerTooltip.innerText = ownerName || "Unknown Owner";
    if (ownerNameElement) ownerNameElement.innerText = ownerName;
    if (overlayOwnerLink) overlayOwnerLink.href = ownerUrl;
    if (overlayOwnerNameElement) overlayOwnerNameElement.innerText = ownerName || "Unknown Owner";
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
        ? `${stationName} - ${songTitle} by ${artist} | tunespace` 
        : `${stationName} | tunespace`;
    document.title = titleText;

    const playingInfo = songTitle && artist 
        ? `${songTitle} by ${artist}` 
        : `<i class="ri-broadcast-line ri-lg"></i> LIVE`;

    document.getElementById('currentStationName').innerText = stationName;
    document.getElementById('currentsongTitle').innerHTML = playingInfo;

    document.getElementById('currentStationNameOverlay').innerText = stationName;
    document.getElementById('currentSongTitleOverlay').innerHTML = playingInfo;
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

// Lautstärke für den Player festlegen
function setVolume(value) {
    if (currentAudio) {
        currentAudio.volume = value;
        isMuted = (value == 0);
        updateMuteIcon();
    }
}

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
            "linear-gradient(135deg, rgb(80, 20, 20), rgb(50, 20, 10))",
            "linear-gradient(135deg, rgb(60, 30, 20), rgb(20, 10, 10))",
            "linear-gradient(135deg, rgb(90, 30, 30), rgb(60, 20, 20))",
            "linear-gradient(135deg, rgb(70, 20, 30), rgb(30, 10, 10))",
            "linear-gradient(135deg, rgb(100, 20, 20), rgb(20, 10, 10))",
            "linear-gradient(135deg, rgb(85, 30, 25), rgb(40, 20, 15))"
        ];
        const randomGradient = gradients[Math.floor(Math.random() * gradients.length)];
        overlay.style.background = randomGradient;
        
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        fixedPlayer.classList.add('hidden');
    }
}

// URL-Validierung
function isValidUrl(url) {
    try {
        new URL(url);
        return true;
    } catch (_) {
        return false;
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


// Lade Stationen bei Seitenaufruf
window.onload = loadStations;