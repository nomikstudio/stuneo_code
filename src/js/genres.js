document.addEventListener("DOMContentLoaded", function() {
    // Funktion zum Laden der Stationen per AJAX
    function loadStations(genreId) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "fetch_stations?genre_id=" + genreId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById("stations-container").innerHTML = xhr.responseText;
            } else {
                console.error("Failed to load stations.");
            }
        };
        xhr.send();
    }

    // Standardmäßig alle Stationen anzeigen
    loadStations(0);

    // Event-Listener für die Genre-Tabs
    const genreTabs = document.querySelectorAll(".genre-tab");
    genreTabs.forEach(tab => {
        tab.addEventListener("click", function() {
            // Aktiven Tab aktualisieren
            document.querySelector(".genre-tab.is-active")?.classList.remove("is-active");
            this.classList.add("is-active");

            // Genre-ID abrufen und Stationen laden
            const genreId = this.getAttribute("data-genre-id");
            loadStations(genreId);
        });
    });
});