/**
 * BosonEvents - Declarative event handler system
 * 
 * Allows inline JS execution via on:event attributes.
 * 
 * Usage:
 *   <form on:success="BosonToast.success('Saved!')">
 *   <button on:click="handleClick()">
 * 
 * Available in handler:
 *   $event - The Event or CustomEvent object
 *   this   - The element with the handler attribute
 */
class BosonEventsManager {
    constructor() {
        // Custom boson events (dispatched as boson:eventname)
        this.customEvents = new Set([
            'submitting',
            'submitted',
            'success',
            'error',
            'open',
            'close',
            'change',
            'select',
            'deselect',
        ]);

        // Native DOM events (use event delegation)
        this.nativeEvents = new Set([
            'click',
            'dblclick',
            'submit',
            'input',
            'focus',
            'blur',
            'keydown',
            'keyup',
            'keypress',
            'mouseenter',
            'mouseleave',
        ]);

        this.initialized = false;
    }

    /**
     * Initialize the event system.
     */
    init() {
        if (this.initialized) return;
        this.initialized = true;

        console.log('[BosonEvents] Initializing custom events:', [...this.customEvents]);
        console.log('[BosonEvents] Initializing native events:', [...this.nativeEvents]);

        // Listen for custom boson events
        this.customEvents.forEach(name => this.listenCustom(name));

        // Listen for native events via delegation
        this.nativeEvents.forEach(name => this.listenNative(name));
    }

    /**
     * Register and listen for a custom boson event.
     */
    register(name, isNative = false) {
        if (isNative) {
            if (!this.nativeEvents.has(name)) {
                this.nativeEvents.add(name);
                this.listenNative(name);
            }
        } else {
            if (!this.customEvents.has(name)) {
                this.customEvents.add(name);
                this.listenCustom(name);
            }
        }
    }

    /**
     * Add event listener for a custom boson event.
     */
    listenCustom(name) {
        console.log(`[BosonEvents] Listening for boson:${name}`);
        document.addEventListener(`boson:${name}`, (e) => this.handleCustom(e, name), true);
    }

    /**
     * Add event listener for a native DOM event.
     */
    listenNative(name) {
        console.log(`[BosonEvents] Listening for native: ${name}`);
        // Use capture phase for focus/blur which don't bubble
        const useCapture = name === 'focus' || name === 'blur';
        document.addEventListener(name, (e) => this.handleNative(e, name), useCapture);
    }

    /**
     * Handle a custom boson event.
     */
    handleCustom(event, eventName) {
        console.log(`[BosonEvents] Custom event received: boson:${eventName}`, event.target);
        
        const element = this.findHandlerElement(event.target, eventName);
        
        if (element) {
            const code = this.getHandlerCode(element, eventName);
            if (code) {
                this.execute(code, event, element);
            }
        }
    }

    /**
     * Handle a native DOM event.
     */
    handleNative(event, eventName) {
        const element = this.findHandlerElement(event.target, eventName);
        
        if (element) {
            const code = this.getHandlerCode(element, eventName);
            if (code) {
                console.log(`[BosonEvents] Native event: ${eventName}`, element);
                this.execute(code, event, element);
            }
        }
    }

    /**
     * Find the closest element with a handler for this event.
     */
    findHandlerElement(target, eventName) {
        if (!target || !target.closest) return null;

        const selector = `[on\\:${eventName}]`;

        try {
            return target.closest(selector);
        } catch (e) {
            console.error(`[BosonEvents] Selector error:`, e);
            return null;
        }
    }

    /**
     * Get the handler code from an element.
     */
    getHandlerCode(element, eventName) {
        return element.getAttribute(`on:${eventName}`);
    }

    /**
     * Execute handler code with $event and this context.
     */
    execute(code, event, element) {
        console.log(`[BosonEvents] Executing:`, code);
        try {
            const handler = new Function('$event', code);
            handler.call(element, event);
        } catch (error) {
            console.error('[BosonEvents] Error executing handler:', error);
            console.error('Handler code:', code);
        }
    }
}

// Global instance
export const BosonEvents = new BosonEventsManager();

// Auto-initialize on DOMContentLoaded
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => BosonEvents.init());
    } else {
        BosonEvents.init();
    }
}

