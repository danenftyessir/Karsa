/**
 * resources/js/app.js
 * main javascript file untuk aplikasi
 */

import './bootstrap';

/**
 * notification helper
 * untuk menampilkan flash messages dari session
 */
class NotificationManager {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        this.createContainer();
        this.showFlashMessages();
    }

    createContainer() {
        // Cek apakah container sudah ada
        const existingContainer = document.getElementById('notification-container');
        if (existingContainer) {
            this.container = existingContainer;
            return;
        }

        // Buat container baru
        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        this.container.className = 'fixed top-20 right-4 z-[9998] space-y-2 max-w-sm';
        document.body.appendChild(this.container);
    }

    show(message, type = 'info', duration = 5000) {
        // Pastikan container ada
        if (!this.container) {
            this.createContainer();
        }

        const notification = document.createElement('div');
        const colors = {
            success: 'bg-green-50 border-green-200 text-green-800',
            error: 'bg-red-50 border-red-200 text-red-800',
            info: 'bg-blue-50 border-blue-200 text-blue-800',
            warning: 'bg-yellow-50 border-yellow-200 text-yellow-800'
        };

        const icons = {
            success: '<svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
            error: '<svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
            info: '<svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
            warning: '<svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>'
        };

        notification.className = `${colors[type]} px-4 py-3 rounded-lg border shadow-lg flex items-start space-x-3 animate-slideInRight`;
        notification.innerHTML = `
            ${icons[type]}
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        `;

        this.container.appendChild(notification);

        // auto remove setelah duration
        if (duration > 0) {
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-in forwards';
                setTimeout(() => notification.remove(), 300);
            }, duration);
        }
    }

    showFlashMessages() {
        // cek data attributes dari blade template
        const successMsg = document.querySelector('[data-success-message]');
        const errorMsg = document.querySelector('[data-error-message]');
        const infoMsg = document.querySelector('[data-info-message]');
        const warningMsg = document.querySelector('[data-warning-message]');

        if (successMsg) {
            this.show(successMsg.dataset.successMessage, 'success');
        }
        if (errorMsg) {
            this.show(errorMsg.dataset.errorMessage, 'error');
        }
        if (infoMsg) {
            this.show(infoMsg.dataset.infoMessage, 'info');
        }
        if (warningMsg) {
            this.show(warningMsg.dataset.warningMessage, 'warning');
        }
    }
}

// DISABLED: NotificationManager sudah dihandle di app.blade.php
// Hanya export showNotification untuk digunakan di tempat lain
window.showNotification = (message, type = 'info', duration = 5000) => {
    // Gunakan NotificationManager yang sudah di-init di app.blade.php
    if (window.notificationManager) {
        window.notificationManager.show(message, type, duration);
    } else {
        console.warn('NotificationManager belum di-init');
    }
};

/**
 * page transition effect
 */
document.addEventListener('DOMContentLoaded', () => {
    // fade in saat load
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.3s ease-in';
        document.body.style.opacity = '1';
    }, 10);

    // fade out sebelum navigate
    document.querySelectorAll('a:not([target="_blank"])').forEach(link => {
        if (link.hostname === window.location.hostname && !link.href.includes('#')) {
            link.addEventListener('click', function(e) {
                if (e.ctrlKey || e.metaKey) return;
                
                e.preventDefault();
                const destination = this.href;
                
                document.body.style.transition = 'opacity 0.2s ease-out';
                document.body.style.opacity = '0';
                
                setTimeout(() => {
                    window.location.href = destination;
                }, 200);
            });
        }
    });
});

/**
 * scroll to top button
 */
document.addEventListener('DOMContentLoaded', () => {
    const scrollBtn = document.getElementById('scroll-to-top');
    if (!scrollBtn) return;

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            scrollBtn.classList.remove('hidden');
        } else {
            scrollBtn.classList.add('hidden');
        }
    }, { passive: true });

    scrollBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});

/**
 * logout loading state
 */
document.addEventListener('DOMContentLoaded', () => {
    const logoutForms = document.querySelectorAll('form[action*="logout"]');
    logoutForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.innerHTML = '<span class="flex items-center space-x-2"><svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>keluar...</span></span>';
            }
        });
    });
});

/**
 * helper functions
 */
window.getCsrfToken = function() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
};

window.showLoading = function() {
    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]';
    overlay.innerHTML = `
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 font-medium">memproses...</span>
        </div>
    `;
    document.body.appendChild(overlay);
};

window.hideLoading = function() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.remove();
};

/**
 * animation styles
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .animate-slideInRight {
        animation: slideInRight 0.3s ease-out;
    }
`;
document.head.appendChild(style);