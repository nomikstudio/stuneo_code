// Datei: js/favorites.js
let currentStationId = null; // Globale Station-ID für den Player

function toggleFavorite(stationId) {
    const action = isFavorite(stationId) ? 'remove' : 'add';

    fetch(`toggle_favorite.php?station_id=${stationId}&action=${action}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateFavoriteIcon(stationId, action === 'add');
                if (currentStationId === stationId) {
                    updateFavoriteIconInPlayer(action === 'add');
                }
            } else {
                console.error('Error updating favorite');
            }
        })
        .catch(error => console.error('Error:', error));
}

function updateFavoriteIcon(stationId, isFavorite) {
    const favoriteIconCard = document.getElementById(`favoriteIcon-${stationId}`);
    if (favoriteIconCard) {
        favoriteIconCard.classList.toggle('ri-heart-fill', isFavorite);
        favoriteIconCard.classList.toggle('ri-heart-line', !isFavorite);
    }
}

function updateFavoriteIconInPlayer(isFavorite) {
    const favoriteIconMini = document.getElementById('favoriteIcon-mini');
    if (favoriteIconMini) {
        favoriteIconMini.classList.toggle('ri-heart-fill', isFavorite);
        favoriteIconMini.classList.toggle('ri-heart-line', !isFavorite);
    }
}

function isFavorite(stationId) {
    const favoriteIcon = document.getElementById(`favoriteIcon-${stationId}`);
    return favoriteIcon && favoriteIcon.classList.contains('ri-heart-fill');
}


