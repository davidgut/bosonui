/**
 * BosonNavlist - Expandable navigation list groups
 *
 * Root:    data-controller="navlist"
 * Group:   data-navlist-group="expandable" (data-expanded="true|false")
 * Trigger: data-navlist-group-target="trigger"
 */
import { lifecycle } from '../core/lifecycle.js';

export class BosonNavlist {
    constructor(element) {
        this.element = element;

        this.abortController = new AbortController();
        this.init();
    }

    init() {
        const signal = this.abortController.signal;

        this.element.querySelectorAll('[data-navlist-group="expandable"]').forEach(group => {
            const trigger = group.querySelector('[data-navlist-group-target="trigger"]');
            if (trigger) {
                trigger.addEventListener('click', () => {
                    const expanded = group.dataset.expanded === 'true';
                    group.dataset.expanded = expanded ? 'false' : 'true';
                }, { signal });
            }
        });
    }

    destroy() {
        this.abortController.abort();
    }
}

lifecycle.register('navlist', BosonNavlist);
