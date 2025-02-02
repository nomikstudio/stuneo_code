// custom_toast.js
function showToast(title, message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.classList.add('toast');

    // Icon abhängig vom Typ festlegen
    const iconHtml = type === 'error' 
        ? '<i class="ri-close-circle-fill ri-lg mt-1" style="color: #ff4d4f; margin-right: 10px;"></i>' 
        : '<i class="ri-checkbox-circle-fill ri-lg mt-1" style="color: #4caf50; margin-right: 10px;"></i>';

    toast.innerHTML = `
        <div class="toast-content" style="display: flex; align-items: center;">
            <div class="toast-title" style="display: flex; align-items: center;">
                ${iconHtml}
            </div>
            <div>${message}</div>
        </div>
    `;

    toastContainer.appendChild(toast);
    setTimeout(() => { toast.remove(); }, 5000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
    return container;
}
