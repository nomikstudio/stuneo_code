document.addEventListener("DOMContentLoaded", () => {
    const banner = document.getElementById("cookie-banner");
    const modal = document.getElementById("cookie-modal");
    const acceptAll = document.getElementById("accept-all");
    const rejectAll = document.getElementById("reject-all");
    const openSettings = document.getElementById("open-settings");
    const savePreferences = document.getElementById("save-preferences");
    const closeModal = document.getElementById("close-modal");

    const analyticsCheckbox = document.getElementById("analytics");
    const searchConsoleCheckbox = document.getElementById("search-console");
    const mp3StreamsCheckbox = document.getElementById("mp3-streams");

    // Initialisierung: Prüfen, ob Präferenzen vorhanden sind
    const cookiesPreferences = JSON.parse(localStorage.getItem("cookiesPreferences") || "{}");
    if (cookiesPreferences.accepted !== undefined) {
        banner.style.display = "none"; // Banner ausblenden
        applyPreferences(cookiesPreferences);
        setModalCheckboxes(cookiesPreferences); // Checkboxen setzen
    } else {
        banner.style.display = "flex"; // Banner anzeigen
    }

    // Alle akzeptieren
    acceptAll.addEventListener("click", () => {
        savePreferencesToLocalStorage({
            accepted: true,
            analytics: true,
            searchConsole: true,
            mp3Streams: true,
        });
        banner.style.display = "none";
        location.reload(); // Seite neu laden, um Dienste zu aktivieren
    });

    // Alle ablehnen
    rejectAll.addEventListener("click", () => {
        savePreferencesToLocalStorage({
            accepted: true,
            analytics: false,
            searchConsole: false,
            mp3Streams: false,
        });
        banner.style.display = "none";
        location.reload(); // Seite neu laden, um Dienste zu deaktivieren
    });

    // Einstellungen öffnen
    openSettings.addEventListener("click", () => {
        modal.classList.remove("hidden");
    });

    // Einstellungen speichern
    savePreferences.addEventListener("click", () => {
        const preferences = {
            accepted: true,
            analytics: analyticsCheckbox.checked,
            searchConsole: searchConsoleCheckbox.checked,
            mp3Streams: mp3StreamsCheckbox.checked,
        };

        savePreferencesToLocalStorage(preferences);
        applyPreferences(preferences);
        modal.classList.add("hidden");
        banner.style.display = "none";
        location.reload(); // Seite neu laden, um Änderungen anzuwenden
    });

    // Modal schließen
    closeModal.addEventListener("click", () => {
        modal.classList.add("hidden");
    });

    // Speichern der Präferenzen in LocalStorage
    function savePreferencesToLocalStorage(preferences) {
        localStorage.setItem("cookiesPreferences", JSON.stringify(preferences));
    }

    // Anwenden von Präferenzen
    function applyPreferences(preferences) {
        if (!preferences.analytics) {
            window[`ga-disable-UA-XXXXXX-Y`] = true; // Google Analytics deaktivieren
        }
        if (!preferences.searchConsole) {
            console.warn("Google Search Console deaktiviert");
        }
        if (!preferences.mp3Streams) {
            console.warn("MP3-Streams deaktiviert");
        }
    }

    // Checkboxen im Modal setzen
    function setModalCheckboxes(preferences) {
        analyticsCheckbox.checked = !!preferences.analytics;
        searchConsoleCheckbox.checked = !!preferences.searchConsole;
        mp3StreamsCheckbox.checked = !!preferences.mp3Streams;
    }
});