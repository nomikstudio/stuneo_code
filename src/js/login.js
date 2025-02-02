document.addEventListener('DOMContentLoaded', () => {
    // Überprüfen, ob der `logout=success` Parameter in der URL ist
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('logout') && urlParams.get('logout') === 'success') {
        // Spracheinstellungen des Benutzers abrufen (Beispiel: `navigator.language`)
        const userLanguage = navigator.language || navigator.userLanguage;

        // Übersetzungen definieren
        const translations = {
            en: {
                title: 'Logout successful',
                message: 'You have been logged out successfully.'
            },
            de: {
                title: 'Erfolgreich abgemeldet',
                message: 'Du wurdest erfolgreich abgemeldet.'
            }
        };

        // Standardsprache auf Englisch setzen
        const defaultLanguage = 'en';

        // Sprache auswählen (entweder `en` oder `de`)
        const language = userLanguage.startsWith('de') ? 'de' : defaultLanguage;

        // Erfolgs-Toast anzeigen mit der passenden Übersetzung
        const { title, message } = translations[language];
        showToast(title, message, 'success');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    // Dynamischer Gradient
    const body = document.querySelector('body');

    // Farben definieren
    const colors = [
        '#1c0c01', // Dunklere Primärfarbe
        '#803401', // Sekundärfarbe
        '#000000'  // Schwarzer Übergang
    ];

    // Zufällige Primärfarbe auswählen
    const randomColor = colors[Math.floor(Math.random() * colors.length)];

    // Schöner weicher Verlauf von der linken oberen Ecke
    body.style.background = `linear-gradient(135deg, ${randomColor} 0%, #803401 50%, #000 100%)`;

    // Übergangsanimation
    body.style.transition = 'background 2s ease-in-out';
});

