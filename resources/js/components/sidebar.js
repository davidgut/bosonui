export class BosonSidebar {
    constructor(element) {
        this.element = element;
        this.isOpen = false;
        this.init();
    }

    init() {
        // Toggle buttons (outside the sidebar, in the header)
        document.querySelectorAll('[data-sidebar-target="toggle"]').forEach(btn => {
            btn.addEventListener('click', () => this.open());
        });

        // Collapse/close button (inside the sidebar)
        this.element.querySelectorAll('[data-sidebar-target="collapse"]').forEach(btn => {
            btn.addEventListener('click', () => this.close());
        });

        // Overlay click to close
        const overlay = this.element.querySelector('[data-sidebar-target="overlay"]');
        if (overlay) {
            overlay.addEventListener('click', () => this.close());
        }

        // Escape key to close
        document.addEventListener('keydown', (e) => {
            if (this.isOpen && e.key === 'Escape') {
                this.close();
            }
        });
    }

    open() {
        this.isOpen = true;
        this.element.dataset.sidebarOpen = 'true';
    }

    close() {
        this.isOpen = false;
        this.element.dataset.sidebarOpen = 'false';
    }
}

export class BosonNavlist {
    constructor(element) {
        this.element = element;
        this.init();
    }

    init() {
        // Expandable groups
        this.element.querySelectorAll('[data-navlist-group="expandable"]').forEach(group => {
            const trigger = group.querySelector('[data-navlist-group-target="trigger"]');
            if (trigger) {
                trigger.addEventListener('click', () => {
                    const expanded = group.dataset.expanded === 'true';
                    group.dataset.expanded = expanded ? 'false' : 'true';
                });
            }
        });
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-controller="sidebar"]').forEach(el => {
        el.bosonSidebar = new BosonSidebar(el);
    });

    document.querySelectorAll('[data-controller="navlist"]').forEach(el => {
        el.bosonNavlist = new BosonNavlist(el);
    });
});
