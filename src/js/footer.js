function searchStations(event) {
    event.preventDefault(); // Verhindert das Neuladen der Seite

    const query = document.getElementById("search-query").value.trim();
    const resultsContainer = document.getElementById("search-results");

    // Wenn das Eingabefeld leer ist, Ergebnisse zurücksetzen und nichts suchen
    if (query === "") {
        resultsContainer.innerHTML = "";
        return;
    }

    // AJAX-Anfrage an den Server
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "search_stations.php?query=" + encodeURIComponent(query), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            resultsContainer.innerHTML = xhr.responseText;
        } else {
            console.error("Failed to load search results.");
        }
    };
    xhr.send();
}

document.addEventListener("DOMContentLoaded", function() {
    const burgerIcon = document.querySelector(".navbar-burger");
    const navbarMenu = document.getElementById("navbarMenu");

    burgerIcon.addEventListener("click", function() {
        navbarMenu.classList.toggle("is-hidden");
    });
});


document.addEventListener('DOMContentLoaded', () => {
    const mainContent = document.querySelector('.main-content');

    // Dunklere, sanfte Farben für einen angenehmen Verlauf
    const colors = [
        '#000',  // Dunkles Blau
    ];

    const randomColor = colors[Math.floor(Math.random() * colors.length)];

    // Gradient von oben nach unten mit sanftem Übergang
    mainContent.style.background = `linear-gradient(
        to bottom, 
        ${randomColor} 0%,           /* Startfarbe */
        #1a1a1a 50%,                 /* Sanftes Dunkelgrau für Übergang */
        #000000 100%                 /* Tiefes Schwarz als Endfarbe */
    )`;
});

function toggleFavorite(stationId) {
    const favoriteIconCard = document.getElementById('favoriteIcon-' + stationId);
    const favoriteIconMini = document.getElementById('favoriteIcon-mini'); // Icon im Player

    // Favoritenstatus anhand des vorhandenen Icons (im Player oder in der Karte) ermitteln
    const isFavorite = (favoriteIconCard && favoriteIconCard.classList.contains('ri-heart-fill')) ||
                       (favoriteIconMini && favoriteIconMini.classList.contains('ri-heart-fill'));
    const action = isFavorite ? 'remove' : 'add';

    // AJAX-Aufruf zum Hinzufügen/Entfernen der Favoriten
    fetch(`toggle_favorite.php?station_id=${stationId}&action=${action}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Favoritenstatus im Karten-Icon aktualisieren, falls vorhanden
                if (favoriteIconCard) {
                    favoriteIconCard.classList.toggle('ri-heart-fill', action === 'add');
                    favoriteIconCard.classList.toggle('ri-heart-line', action === 'remove');
                }

                // Favoritenstatus im Player-Icon aktualisieren, falls es die gleiche Station ist
                if (favoriteIconMini && currentStationId === stationId) {
                    favoriteIconMini.classList.toggle('ri-heart-fill', action === 'add');
                    favoriteIconMini.classList.toggle('ri-heart-line', action === 'remove');
                }

                // Optional: Sanfter Fade-out und Reload
                document.getElementById('loader-overlay').classList.add('active');
                setTimeout(() => {
                    location.reload();
                }, 500); // Nach 0.5 Sekunden neu laden
            } else {
                console.error(data.message);
            }
        })
        .catch(error => console.error('Error updating favorites:', error));
}


function toggleDropdown() {
    const dropdown = document.getElementById("dropdownMenu");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

// Dropdown automatisch schließen, wenn außerhalb geklickt wird
window.addEventListener("click", function(event) {
    const dropdown = document.getElementById("dropdownMenu");
    const userIconContainer = document.querySelector(".user-account-dropdown");

    // Prüfe, ob userIconContainer und dropdown existieren
    if (!dropdown || !userIconContainer) {
        return; // Falls eines der Elemente fehlt, brich die Funktion ab
    }

    if (!userIconContainer.contains(event.target)) {
        dropdown.style.display = "none";
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const playButtons = document.querySelectorAll('.play-button');

    playButtons.forEach(button => {
        button.addEventListener('click', () => {
            const stationId = button.getAttribute('data-station-id');

            if (!stationId || isNaN(stationId)) {
                console.error('Invalid station ID.');
                return;
            }

            // GET-Anfrage an track_play.php
            fetch(`https://open.stuneo.com/track_play.php?station_id=${encodeURIComponent(stationId)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status !== 'success') {
                        console.error(`Error: ${data.message}`);
                    }
                })
                .catch(() => {
                    console.error('An error occurred while tracking the play event.');
                });
        });
    });
});



