// custom_toast.js
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.classList.add('toast', type);

    // Icon abhängig vom Typ festlegen
    const iconHtml = type === 'error' 
        ? '<i class="ri-close-circle-fill ri-lg" style="color: #ff4d4f; margin-right: 10px;"></i>' 
        : '<i class="ri-checkbox-circle-fill ri-lg" style="color: #4caf50; margin-right: 10px;"></i>';

    toast.innerHTML = `
        <div class="toast-content" style="display: flex; align-items: center;">
            ${iconHtml}
            <span>${message}</span>
        </div>
    `;

    toastContainer.appendChild(toast);

    // Toast entfernen nach 5 Sekunden und mit Fade-Out-Effekt
    setTimeout(() => {
        toast.classList.add('fade-out');
        setTimeout(() => toast.remove(), 500); // Entfernen nach dem Fade-Out-Effekt
    }, 5000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.style.position = 'fixed';
    container.style.bottom = '20px';
    container.style.right = '20px';
    container.style.zIndex = '1000';
    document.body.appendChild(container);
    return container;
}
