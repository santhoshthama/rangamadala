/**
 * Rangamadala Toast Notification System
 * Professional toast notifications matching the elegant gold theme
 */

// Global toast container
function initToastContainer() {
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', initToastContainer);

/**
 * Show a toast notification
 * @param {string} message - The message to display
 * @param {string} type - Type of toast: 'success', 'error', 'warning', 'info'
 * @param {number} duration - Duration in milliseconds (default: 4000)
 */
function showToast(message, type = 'info', duration = 4000) {
    initToastContainer();
    
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Icon based on type
    const icons = {
        success: 'bx bx-badge-check',
        error: 'bx bxs-x-circle',
        warning: 'bx bxs-warning',
        info: 'bx bxs-info-circle'
    };
    
    // Titles based on type
    const titles = {
        success: 'Success',
        error: 'Error',
        warning: 'Warning',
        info: 'Information'
    };
    
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="${icons[type] || icons.info}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${titles[type] || titles.info}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <span class="material-symbols-rounded">close</span>
        </button>
        <div class="toast-progress">
            <div class="toast-progress-bar" style="animation-duration: ${duration}ms"></div>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Trigger animation
    requestAnimationFrame(() => {
        toast.classList.add('show');
    });
    
    // Auto remove
    setTimeout(() => {
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 400);
    }, duration);
    
    return toast;
}

// Convenience functions
function toastSuccess(message, duration = 4000) {
    return showToast(message, 'success', duration);
}

function toastError(message, duration = 5000) {
    return showToast(message, 'error', duration);
}

function toastWarning(message, duration = 4500) {
    return showToast(message, 'warning', duration);
}

function toastInfo(message, duration = 4000) {
    return showToast(message, 'info', duration);
}

// Override native alert (optional - uncomment to enable)
// window.alert = function(message) {
//     showToast(message, 'info');
// };

/**
 * Show a confirmation dialog (replacement for confirm())
 * @param {string} message - The message to display
 * @param {object} options - Configuration options
 * @returns {Promise} Resolves to true/false based on user action
 */
function showConfirm(message, options = {}) {
    return new Promise((resolve) => {
        const {
            title = 'Confirm',
            confirmText = 'Yes, Confirm',
            cancelText = 'Cancel',
            type = 'warning'
        } = options;
        
        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'confirm-overlay';
        
        const icons = {
            success: 'check_circle',
            error: 'cancel',
            warning: 'warning',
            info: 'info',
            question: 'help'
        };
        
        overlay.innerHTML = `
            <div class="confirm-dialog">
                <div class="confirm-icon confirm-icon-${type}">
                    <span class="material-symbols-rounded">${icons[type] || icons.question}</span>
                </div>
                <h3 class="confirm-title">${title}</h3>
                <p class="confirm-message">${message}</p>
                <div class="confirm-buttons">
                    <button class="confirm-btn confirm-btn-cancel">${cancelText}</button>
                    <button class="confirm-btn confirm-btn-confirm">${confirmText}</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        // Animate in
        requestAnimationFrame(() => {
            overlay.classList.add('show');
        });
        
        // Handle buttons
        const confirmBtn = overlay.querySelector('.confirm-btn-confirm');
        const cancelBtn = overlay.querySelector('.confirm-btn-cancel');
        
        const closeDialog = (result) => {
            overlay.classList.remove('show');
            setTimeout(() => {
                overlay.remove();
                resolve(result);
            }, 300);
        };
        
        confirmBtn.addEventListener('click', () => closeDialog(true));
        cancelBtn.addEventListener('click', () => closeDialog(false));
        
        // Close on overlay click
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeDialog(false);
        });
        
        // Close on Escape key
        const handleEscape = (e) => {
            if (e.key === 'Escape') {
                document.removeEventListener('keydown', handleEscape);
                closeDialog(false);
            }
        };
        document.addEventListener('keydown', handleEscape);
    });
}
