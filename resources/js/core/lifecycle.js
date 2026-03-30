/**
 * BosonLifecycle - Component initialization and teardown manager
 *
 * Handles automatic component lifecycle for Turbo Drive, Turbo Frames,
 * Turbo Streams, and standard page loads. Uses MutationObserver to
 * detect dynamically inserted/removed elements.
 *
 * Components register via:
 *   lifecycle.register('dropdown', BosonDropdown)
 *
 * Protocol: each component class must accept (element) in constructor
 * and may implement destroy() for cleanup.
 */

class BosonLifecycle {
    constructor() {
        this.registry = new Map();
        this.observer = null;
        this.started = false;
    }

    /**
     * Register a component class for a controller name.
     * @param {string} name - Controller name (matches data-controller="name")
     * @param {Function} ComponentClass - Class with constructor(element) and optional destroy()
     */
    register(name, ComponentClass) {
        this.registry.set(name, ComponentClass);

        // If already started, initialize any existing elements for this controller
        if (this.started) {
            this.initController(name, ComponentClass);
        }
    }

    /**
     * Initialize all registered components within a root element.
     * Safe to call multiple times (idempotent).
     * @param {Element} root - Root element to scan (defaults to document)
     */
    init(root = document) {
        this.registry.forEach((ComponentClass, name) => {
            this.initController(name, ComponentClass, root);
        });
    }

    /**
     * Initialize all elements for a specific controller within a root.
     * @param {string} name - Controller name
     * @param {Function} ComponentClass - Component class
     * @param {Element} root - Root element to scan
     */
    initController(name, ComponentClass, root = document) {
        const selector = `[data-controller="${name}"]`;
        const elements = root.querySelectorAll(selector);

        elements.forEach(el => this.connect(el, name, ComponentClass));
    }

    /**
     * Initialize a single element if not already connected.
     * @param {Element} el - The DOM element
     * @param {string} name - Controller name
     * @param {Function} ComponentClass - Component class
     */
    connect(el, name, ComponentClass) {
        // Skip if already initialized
        if (el.boson) return;

        try {
            el.boson = new ComponentClass(el);
        } catch (error) {
            console.error(`[Boson] Failed to initialize "${name}":`, error);
        }
    }

    /**
     * Tear down all components within a root element.
     * Called before Turbo caches the page.
     * @param {Element} root - Root element to scan (defaults to document)
     */
    teardown(root = document) {
        const elements = root.querySelectorAll('[data-controller]');

        elements.forEach(el => this.disconnect(el));
    }

    /**
     * Tear down a single element's component.
     * @param {Element} el - The DOM element
     */
    disconnect(el) {
        if (!el.boson) return;

        try {
            if (typeof el.boson.destroy === 'function') {
                el.boson.destroy();
            }
        } catch (error) {
            const name = el.getAttribute('data-controller');
            console.error(`[Boson] Failed to destroy "${name}":`, error);
        }

        delete el.boson;
    }

    /**
     * Start the lifecycle system.
     * Sets up event listeners and MutationObserver.
     */
    start() {
        if (this.started) return;
        this.started = true;

        // Initial page load
        this.init();

        // Turbo Drive: re-initialize after navigation
        document.addEventListener('turbo:load', () => this.init());

        // Turbo Drive: clean up before caching
        document.addEventListener('turbo:before-cache', () => this.teardown());

        // MutationObserver for Turbo Frames, Streams, and dynamic DOM changes
        this.observer = new MutationObserver(mutations => {
            for (const mutation of mutations) {
                // Initialize components in added nodes
                for (const node of mutation.addedNodes) {
                    if (node.nodeType !== Node.ELEMENT_NODE) continue;

                    // Check the node itself
                    if (node.hasAttribute?.('data-controller')) {
                        this.initElement(node);
                    }

                    // Check descendants
                    const children = node.querySelectorAll?.('[data-controller]');
                    if (children) {
                        children.forEach(el => this.initElement(el));
                    }
                }

                // Tear down components in removed nodes
                for (const node of mutation.removedNodes) {
                    if (node.nodeType !== Node.ELEMENT_NODE) continue;

                    if (node.boson) {
                        this.disconnect(node);
                    }

                    const children = node.querySelectorAll?.('[data-controller]');
                    if (children) {
                        children.forEach(el => this.disconnect(el));
                    }
                }
            }
        });

        this.observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    /**
     * Initialize a single element by looking up its controller name.
     * @param {Element} el - The DOM element with data-controller
     */
    initElement(el) {
        if (el.boson) return;

        const name = el.getAttribute('data-controller');
        const ComponentClass = this.registry.get(name);

        if (ComponentClass) {
            this.connect(el, name, ComponentClass);
        }
    }
}

// Global singleton
export const lifecycle = new BosonLifecycle();

// Auto-start when DOM is ready
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => lifecycle.start());
    } else {
        lifecycle.start();
    }
}
