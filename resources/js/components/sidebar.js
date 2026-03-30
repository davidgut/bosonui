/**
 * BosonSidebar - Collapsible sidebar with overlay and keyboard support
 *
 * Root:     data-controller="sidebar"
 * Toggle:   data-sidebar-target="toggle"
 * Collapse: data-sidebar-target="collapse"
 * Overlay:  data-sidebar-target="overlay"
 *
 * Public: open(), close(), destroy()
 */
import { lifecycle } from '../core/lifecycle.js';

export class BosonSidebar {
    constructor(element) {
        this.element = element;
        this.isOpen = false;

        this.abortController = new AbortController();
        this.init();
    }

    init() {
        const signal = this.abortController.signal;

        // Toggle buttons (outside the sidebar, in the header)
        document.querySelectorAll('[data-sidebar-target="toggle"]').forEach(btn => {
            btn.addEventListener('click', () => this.open(), { signal });
        });

        // Collapse/close button (inside the sidebar)
        this.element.querySelectorAll('[data-sidebar-target="collapse"]').forEach(btn => {
            btn.addEventListener('click', () => this.close(), { signal });
        });

        // Overlay click to close
        const overlay = this.element.querySelector('[data-sidebar-target="overlay"]');
        if (overlay) {
            overlay.addEventListener('click', () => this.close(), { signal });
        }

        // Escape key to close
        document.addEventListener('keydown', (e) => {
            if (this.isOpen && e.key === 'Escape') {
                this.close();
            }
        }, { signal });
    }

    open() {
        this.isOpen = true;
        this.element.dataset.sidebarOpen = 'true';
    }

    close() {
        this.isOpen = false;
        this.element.dataset.sidebarOpen = 'false';
    }

    destroy() {
        this.abortController.abort();
    }
}

lifecycle.register('sidebar', BosonSidebar);
