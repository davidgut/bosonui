/**
 * BosonModal - Modal dialog with trigger, backdrop, and keyboard support
 *
 * Root:     data-controller="modal" data-modal-name="{name}"
 * Triggers: data-modal-target="trigger" data-modal-name="{name}"
 * Close:    data-modal-target="close"
 *
 * Public: open(), close(), destroy()
 */
export class BosonModal {
    constructor(element) {
        this.element = element;
        this.name = this.element.dataset.modalName;
        this.isOpen = false;

        this.abortController = new AbortController();
        this.init();
    }

    init() {
        const signal = this.abortController.signal;

        // Find triggers
        const triggers = document.querySelectorAll(`[data-modal-target="trigger"][data-modal-name="${this.name}"]`);
        triggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                this.open();
            }, { signal });
        });

        // Close on overlay click or explicit close button
        this.element.addEventListener('click', (e) => {
            if (e.target === this.element || e.target.closest('[data-modal-target="close"]')) {
                this.close();
            }
        }, { signal });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (this.isOpen && e.key === 'Escape') {
                this.close();
            }
        }, { signal });
    }

    open() {
        this.isOpen = true;
        this.element.classList.add('open');
        this.element.classList.remove('invisible');
        document.body.style.overflow = 'hidden';
    }

    close() {
        this.isOpen = false;
        this.element.classList.remove('open');
        document.body.style.overflow = '';

        const onEnd = () => {
            if (!this.isOpen) this.element.classList.add('invisible');
        };

        this.element.addEventListener('transitionend', onEnd, { once: true });

        // Fallback for cases where no CSS transition is defined
        setTimeout(onEnd, 300);
    }

    destroy() {
        this.abortController.abort();
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-controller="modal"]').forEach(el => {
        el.bosonModal = new BosonModal(el);
    });
});
