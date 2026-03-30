/**
 * BosonNavlist - Expandable navigation list groups
 *
 * Root:    data-controller="navlist"
 * Group:   data-navlist-group="expandable" (data-expanded="true|false")
 * Trigger: data-navlist-group-target="trigger"
 */
export class BosonNavlist {
    constructor(element) {
        this.element = element;
        this.init();
    }

    init() {
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
    document.querySelectorAll('[data-controller="navlist"]').forEach(el => {
        el.bosonNavlist = new BosonNavlist(el);
    });
});
