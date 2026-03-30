/**
 * BosonToast - Toast notification system
 *
 * Container: data-controller="toast"
 * Item:      data-toast-target="item"
 * Close:     data-toast-target="close"
 *
 * Public: $toast.show(options), $toast.success(msg), $toast.warning(msg),
 *         $toast.danger(msg), $toast.dismiss(toast)
 */
class BosonToastManager {
    constructor() {
        this.container = null;
        this.defaultDuration = 5000;
    }

    getContainer() {
        if (!this.container) {
            this.container = document.querySelector('[data-controller="toast"]');
        }
        return this.container;
    }

    show(options) {
        const container = this.getContainer();

        if (!container) {
            console.warn('BosonToast: No toast container found. Add <x-boson::toast /> to your layout.');
            return;
        }

        const config = this.normalise(options);
        const toast = this.createToast(config);

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
        toast.setAttribute('data-toast-target', 'item');
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
                <button type="button" class="btn btn-ghost btn-sm btn-square" data-toast-target="close">
                    ${this.createIcon('x-mark')}
                </button>
            </div>
        `;

        toast.querySelector('[data-toast-target="close"]').addEventListener('click', () => {
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
        if (!toast.parentNode) return;

        toast.removeAttribute('data-entering');
        toast.setAttribute('data-exiting', '');
        
        const fallback = setTimeout(() => toast.remove(), 300);

        toast.addEventListener('animationend', () => {
            clearTimeout(fallback);
            toast.remove();
        }, { once: true });
    }

    /**
     * Normalise options into a config object.
     * Accepts a string (shorthand for text) or an object.
     */
    normalise(options, variant = null) {
        const config = typeof options === 'string' ? { text: options } : { ...options };
        if (variant) config.variant = variant;
        return config;
    }

    success(options) {
        return this.show(this.normalise(options, 'success'));
    }

    warning(options) {
        return this.show(this.normalise(options, 'warning'));
    }

    danger(options) {
        return this.show(this.normalise(options, 'danger'));
    }
}

// Global instance
export const $toast = new BosonToastManager();

// Make available globally
if (typeof window !== 'undefined') {
    window.$toast = $toast;
}

// Auto-initialize close buttons and auto-dismiss for server-rendered toasts
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-toast-target="item"] [data-toast-target="close"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const toast = btn.closest('[data-toast-target="item"]');
            if (toast) $toast.dismiss(toast);
        });
    });

    document.querySelectorAll('[data-toast-target="item"]').forEach(toast => {
        const duration = toast.hasAttribute('data-duration') 
            ? parseInt(toast.dataset.duration, 10) 
            : 5000;
        
        if (duration > 0) {
            setTimeout(() => $toast.dismiss(toast), duration);
        }
    });
});
