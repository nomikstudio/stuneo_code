    // Funktion zur Erkennung des Betriebssystems
    function detectOS() {
        let os = "Unknown";
        if (navigator.userAgent.indexOf("Win") !== -1) os = "Windows";
        else if (navigator.userAgent.indexOf("Mac") !== -1 || navigator.userAgent.indexOf("Darwin") !== -1) os = "Mac";
        else if (navigator.userAgent.indexOf("Linux") !== -1) os = "Linux";
        return os;
    }

    // Funktion zur Überprüfung, ob die App in Electron läuft
    function isElectronApp() {
        return navigator.userAgent.includes("Electron");
    }

        // Füge Klasse hinzu, wenn Electron erkannt wird
        document.addEventListener("DOMContentLoaded", function() {
        if (isElectronApp()) {
            document.body.classList.add("electron-mode");
        }
    });

    // Betriebssystem an PHP senden und Button aktualisieren oder ausblenden
    document.addEventListener("DOMContentLoaded", function() {
        // Button nur anzeigen, wenn die App nicht in Electron läuft
        if (isElectronApp()) {
            document.getElementById("downloadButton").style.display = "none";
            return;
        }

        const os = detectOS();
        fetch(window.location.href, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "client_os=" + os
        })
        .then(response => response.json()) // JSON-Daten erwarten
        .then(data => {
            // Button mit dem Download-Link und Text aktualisieren
            document.getElementById("downloadButton").href = data.downloadLink;
            document.getElementById("downloadButtonText").innerText = data.downloadText;
        })
        .catch(error => console.error('Fehler beim Abrufen des Download-Links:', error));
    });
        // Ändere den Text von "BETA" auf "App", wenn Electron erkannt wird
        document.addEventListener("DOMContentLoaded", function() {
        if (isElectronApp()) {
            const betaTag = document.querySelector(".tag.is-light.has-text-black");
            if (betaTag) {
                betaTag.textContent = "APP";
            }
        }
    });