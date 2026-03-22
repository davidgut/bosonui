
export class BosonDropdown {
    constructor(element) {
        this.element = element;
        this.trigger = this.element.querySelector('[data-dropdown-target="trigger"]');
        this.menu = this.element.querySelector('[data-dropdown-target="menu"]');
        this.isOpen = false;

        this.init();
    }

    init() {
        if (!this.trigger || !this.menu) return;

        // Toggle on click
        this.trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.element.contains(e.target)) {
                this.close();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (this.isOpen && e.key === 'Escape') {
                this.close();
            }
        });
        
        // Handle keyboard navigation for accessibility if needed in the future
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
        
        // Basic positioning logic could go here if needed, 
        // relying on CSS absolute positioning for now.
    }

    close() {
        this.isOpen = false;
        this.menu.classList.remove('open');
        this.trigger.setAttribute('aria-expanded', 'false');
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-controller="dropdown"]').forEach(el => {
        new BosonDropdown(el);
    });
});
