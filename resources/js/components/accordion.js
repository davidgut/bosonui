
export class BosonAccordion {
    constructor(element) {
        this.element = element;
        this.exclusive = this.element.hasAttribute('data-accordion-exclusive');
        this.transition = this.element.hasAttribute('data-accordion-transition');
        this.items = [];

        this.init();
    }

    init() {
        this.items = [...this.element.querySelectorAll(':scope > [data-accordion-target="item"]')];

        this.items.forEach(item => {
            const heading = item.querySelector('[data-accordion-target="heading"]');
            if (!heading) return;

            heading.addEventListener('click', () => this.toggle(item));

            // Set initial aria state
            const expanded = item.getAttribute('data-expanded') === 'true';
            heading.setAttribute('aria-expanded', expanded ? 'true' : 'false');

            // Handle transition for initially expanded items
            if (this.transition && expanded) {
                const content = item.querySelector('[data-accordion-target="content"]');
                if (content) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                }
            }
        });
    }

    toggle(item) {
        const isExpanded = item.getAttribute('data-expanded') === 'true';

        if (isExpanded) {
            this.collapse(item);
        } else {
            // In exclusive mode, collapse all others first
            if (this.exclusive) {
                this.items.forEach(other => {
                    if (other !== item && other.getAttribute('data-expanded') === 'true') {
                        this.collapse(other);
                    }
                });
            }
            this.expand(item);
        }
    }

    expand(item) {
        const heading = item.querySelector('[data-accordion-target="heading"]');
        const content = item.querySelector('[data-accordion-target="content"]');

        item.setAttribute('data-expanded', 'true');
        if (heading) heading.setAttribute('aria-expanded', 'true');

        if (this.transition && content) {
            content.style.maxHeight = content.scrollHeight + 'px';
        }
    }

    collapse(item) {
        const heading = item.querySelector('[data-accordion-target="heading"]');
        const content = item.querySelector('[data-accordion-target="content"]');

        item.removeAttribute('data-expanded');
        if (heading) heading.setAttribute('aria-expanded', 'false');

        if (this.transition && content) {
            // Set to current height first so transition starts from a known value
            content.style.maxHeight = content.scrollHeight + 'px';
            // Force a reflow
            content.offsetHeight;
            content.style.maxHeight = '0';
        }
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-controller="accordion"]').forEach(el => {
        new BosonAccordion(el);
    });
});
