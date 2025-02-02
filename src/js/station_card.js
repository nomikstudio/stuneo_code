function toggleFavorite(stationId) {
    const favoriteIconCard = document.getElementById('favoriteIcon-' + stationId);
    const favoriteIconMini = document.getElementById('favoriteIcon-mini'); // Icon im Player
    const action = favoriteIconCard.classList.contains('ri-heart-fill') ? 'remove' : 'add';

    // AJAX-Aufruf zum Hinzufügen/Entfernen der Favoriten
    fetch(`toggle_favorite.php?station_id=${stationId}&action=${action}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Favoritenstatus im Card-Icon aktualisieren
                favoriteIconCard.classList.toggle('ri-heart-fill', action === 'add');
                favoriteIconCard.classList.toggle('ri-heart-line', action === 'remove');

                // Favoritenstatus im Player-Icon aktualisieren, falls es die gleiche Station ist
                if (favoriteIconMini && currentStationId === stationId) {
                    favoriteIconMini.classList.toggle('ri-heart-fill', action === 'add');
                    favoriteIconMini.classList.toggle('ri-heart-line', action === 'remove');
                }

                // Füge sanftes Fade-Effect hinzu und lade Seite neu
                document.body.style.transition = "opacity 0.5s ease";
                document.body.style.opacity = "0";

                setTimeout(() => {
                    location.reload();
                }, 500); // Nach 0.5 Sekunden neu laden
            } else {
                console.error(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
}
