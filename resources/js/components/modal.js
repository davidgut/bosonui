export class BosonModal {
    constructor(element) {
        this.element = element;
        this.name = this.element.dataset.name;
        this.isOpen = false;

        this.init();
    }

    init() {
        // Find triggers
        const triggers = document.querySelectorAll(`[data-modal-trigger="${this.name}"]`);
        triggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                this.open();
            });
        });

        // Close on overlay click (if targeting the overlay itself or explicit close)
        this.element.addEventListener('click', (e) => {
            if (e.target === this.element || e.target.closest('[data-modal-close]')) {
                this.close();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (this.isOpen && e.key === 'Escape') {
                this.close();
            }
        });
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
        setTimeout(() => {
            if (!this.isOpen) this.element.classList.add('invisible');
        }, 200); // Wait for transition
        document.body.style.overflow = '';
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-modal]').forEach(el => {
        el.bosonModal = new BosonModal(el);
    });
});
