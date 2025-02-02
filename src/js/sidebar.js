    // Modal öffnen
    function shareAppModal() {
        document.getElementById("shareModal").classList.add("is-active");
    }

    // Modal schließen
    function closeModal() {
        document.getElementById("shareModal").classList.remove("is-active");
    }

    // Link in die Zwischenablage kopieren
    function copyToClipboard() {
        const shareLink = document.getElementById("shareLink");
        shareLink.select();
        shareLink.setSelectionRange(0, 99999); // Für mobile Geräte
        document.execCommand("copy");

        alert("Link wurde kopiert: " + shareLink.value);
    }