/**
 * BosonToast - Toast notification system
 */
class BosonToastManager {
    constructor() {
        this.container = null;
        this.defaultDuration = 5000;
    }

    getContainer() {
        if (!this.container) {
            this.container = document.querySelector('[data-boson-toast-container]');
        }
        return this.container;
    }

    show(options) {
        console.log('[BosonToast] show() called with:', options);
        
        const container = this.getContainer();
        console.log('[BosonToast] Container:', container);
        
        if (!container) {
            console.warn('BosonToast: No toast container found. Add <x-boson::toast /> to your layout.');
            return;
        }

        const config = typeof options === 'string' 
            ? { text: options } 
            : options;

        const toast = this.createToast(config);
        console.log('[BosonToast] Created toast:', toast);
        
        container.appendChild(toast);

        // Trigger enter animation
        requestAnimationFrame(() => {
            toast.setAttribute('data-entering', '');
        });

        // Auto dismiss
        const duration = config.duration ?? this.defaultDuration;
        if (duration > 0) {
            setTimeout(() => this.dismiss(toast), duration);
        }

        return toast;
    }

    createToast(config) {
        const { variant, heading, text } = config;

        const icons = {
            success: this.createIcon('check-circle'),
            warning: this.createIcon('exclamation-triangle'),
            danger: this.createIcon('x-circle'),
        };

        const toast = document.createElement('div');
        toast.className = 'toast';
        if (variant) {
            toast.setAttribute('data-variant', variant);
        }
        toast.setAttribute('data-boson-toast', '');
        toast.setAttribute('role', 'alert');

        const iconHtml = variant && icons[variant] 
            ? `<div class="toast-icon">${icons[variant]}</div>` 
            : '';

        toast.innerHTML = `
            <div class="toast-body">
                ${iconHtml}
                <div class="toast-content">
                    ${heading ? `<p class="toast-heading">${this.escapeHtml(heading)}</p>` : ''}
                    <p class="toast-text">${this.escapeHtml(text)}</p>
                </div>
            </div>
            <div class="toast-actions">
                <button type="button" class="btn btn-ghost btn-sm btn-square" data-toast-close>
                    ${this.createIcon('x-mark')}
                </button>
            </div>
        `;

        toast.querySelector('[data-toast-close]').addEventListener('click', () => {
            this.dismiss(toast);
        });

        return toast;
    }

    createIcon(name) {
        // Heroicons micro 16x16 SVG paths
        const icons = {
            'check-circle': '<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm3.844-8.791a.75.75 0 0 0-1.188-.918l-3.7 4.79-1.649-1.833a.75.75 0 1 0-1.114 1.004l2.25 2.5a.75.75 0 0 0 1.151-.043l4.25-5.5Z" clip-rule="evenodd" />',
            'exclamation-triangle': '<path fill-rule="evenodd" d="M6.701 2.25c.577-1 2.02-1 2.598 0l5.196 9a1.5 1.5 0 0 1-1.299 2.25H2.804a1.5 1.5 0 0 1-1.3-2.25l5.197-9ZM8 4a.75.75 0 0 1 .75.75v3a.75.75 0 1 1-1.5 0v-3A.75.75 0 0 1 8 4Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />',
            'x-circle': '<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm2.78-4.22a.75.75 0 0 1-1.06 0L8 9.06l-1.72 1.72a.75.75 0 1 1-1.06-1.06L6.94 8 5.22 6.28a.75.75 0 0 1 1.06-1.06L8 6.94l1.72-1.72a.75.75 0 1 1 1.06 1.06L9.06 8l1.72 1.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />',
            'x-mark': '<path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8 4.22 10.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z" />',
        };
        
        return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" width="16" height="16" class="icon icon-micro">${icons[name] || ''}</svg>`;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    dismiss(toast) {
        toast.removeAttribute('data-entering');
        toast.setAttribute('data-exiting', '');
        
        toast.addEventListener('animationend', () => {
            toast.remove();
        }, { once: true });

        // Fallback removal
        setTimeout(() => toast.remove(), 300);
    }

    success(options) {
        return this.show({ ...(typeof options === 'string' ? { text: options } : options), variant: 'success' });
    }

    warning(options) {
        return this.show({ ...(typeof options === 'string' ? { text: options } : options), variant: 'warning' });
    }

    danger(options) {
        return this.show({ ...(typeof options === 'string' ? { text: options } : options), variant: 'danger' });
    }
}

// Global instance
export const BosonToast = new BosonToastManager();

// Make available globally
if (typeof window !== 'undefined') {
    window.BosonToast = BosonToast;
}

// Auto-initialize close buttons for server-rendered toasts
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-boson-toast] [data-toast-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const toast = btn.closest('[data-boson-toast]');
            if (toast) {
                toast.setAttribute('data-exiting', '');
                setTimeout(() => toast.remove(), 300);
            }
        });
    });

    // Auto-dismiss server-rendered toasts
    document.querySelectorAll('[data-boson-toast]').forEach(toast => {
        const duration = toast.hasAttribute('data-duration') 
            ? parseInt(toast.dataset.duration, 10) 
            : 5000;
        
        // Duration of 0 means permanent (no auto-dismiss)
        if (duration > 0) {
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.setAttribute('data-exiting', '');
                    setTimeout(() => toast.remove(), 300);
                }
            }, duration);
        }
    });
});
