/**
 * BosonTab - Switchable tab panels
 *
 * Root:   data-controller="tab"  (also role="tablist")
 * Tab:    data-tab-target="tab"    (data-tab-name="...")
 * Panel:  data-tab-target="panel"  (data-tab-name="...")
 *
 * Public: activate(name), destroy()
 */
import { lifecycle } from '../core/lifecycle.js';

export class BosonTab {
    constructor(element) {
        this.element = element;
        this.tabs = [];
        this.panels = [];
        this.abortController = new AbortController();
        this.init();
    }

    init() {
        const signal = this.abortController.signal;

        this.tabs = [...this.element.querySelectorAll('[data-tab-target="tab"]')];
        this.panels = [...this.element.querySelectorAll('[data-tab-target="panel"]')];

        this.tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const name = tab.getAttribute('data-tab-name');
                if (name) this.activate(name);
            }, { signal });
        });

        // Activate the selected tab, or fall back to the first tab
        const selectedTab = this.tabs.find(tab => tab.hasAttribute('data-tab-selected'));
        const firstName = selectedTab?.getAttribute('data-tab-name') || this.tabs[0]?.getAttribute('data-tab-name');

        if (firstName) {
            this.activate(firstName);
        }
    }

    activate(name) {
        // Update tabs
        this.tabs.forEach(tab => {
            const isActive = tab.getAttribute('data-tab-name') === name;
            tab.classList.toggle('tab-active', isActive);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        // Update panels
        this.panels.forEach(panel => {
            const isActive = panel.getAttribute('data-tab-name') === name;
            panel.classList.toggle('tab-panel-active', isActive);
        });
    }

    destroy() {
        this.abortController.abort();
    }
}

lifecycle.register('tab', BosonTab);
