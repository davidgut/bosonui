/**
 * BosonDropdown - Click-triggered dropdown menu
 *
 * Root:    data-controller="dropdown"
 * Trigger: data-dropdown-target="trigger"
 * Menu:    data-dropdown-target="menu"
 *
 * Public: open(), close(), toggle(), destroy()
 */
export class BosonDropdown {
    constructor(element) {
        this.element = element;
        this.trigger = this.element.querySelector('[data-dropdown-target="trigger"]');
        this.menu = this.element.querySelector('[data-dropdown-target="menu"]');
        this.isOpen = false;

        this.abortController = new AbortController();
        this.init();
    }

    init() {
        if (!this.trigger || !this.menu) return;

        const signal = this.abortController.signal;

        // Toggle on click
        this.trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        }, { signal });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.element.contains(e.target)) {
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

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        this.isOpen = true;
        this.menu.classList.add('open');
        this.trigger.setAttribute('aria-expanded', 'true');
    }

    close() {
        this.isOpen = false;
        this.menu.classList.remove('open');
        this.trigger.setAttribute('aria-expanded', 'false');
    }

    destroy() {
        this.abortController.abort();
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-controller="dropdown"]').forEach(el => {
        el.bosonDropdown = new BosonDropdown(el);
    });
});
