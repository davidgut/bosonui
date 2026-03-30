/**
 * BosonEvents - Declarative event handler system
 * 
 * Allows inline JS execution via on:event attributes.
 * 
 * Usage:
 *   <form on:success="$toast.success('Saved!')">
 *   <form on:success="$('#badge').text($data.status)">
 * 
 * Available in handler:
 *   $event              - The Event or CustomEvent object
 *   $(selector)         - Chainable DOM helper: text, class, data, attr, toggle
 *   $data               - Shorthand for $event.detail.data (response data)
 *   $match(val, map)    - Value lookup, like PHP's match expression
 *   $toast              - Toast notifications: $toast.success(), $toast.danger(), etc.
 *   this                - The element with the handler attribute
 */

import { createDomHelper } from './dom.js';
import { match } from './utils.js';
import { $toast } from '../components/toast.js';

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
        document.addEventListener(`boson:${name}`, (e) => this.handleCustom(e, name), true);
    }

    /**
     * Add event listener for a native DOM event.
     */
    listenNative(name) {
        // Use capture phase for focus/blur which don't bubble
        const useCapture = name === 'focus' || name === 'blur';
        document.addEventListener(name, (e) => this.handleNative(e, name), useCapture);
    }

    /**
     * Handle a custom boson event.
     */
    handleCustom(event, eventName) {
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
     * Execute handler code with Boson helpers in scope.
     *
     * Injects $event, $, $data, $match, $toast as scoped variables.
     */
    execute(code, event, element) {
        try {
            const $data = event.detail?.data || {};
            const $ = (selector) => createDomHelper(selector);
            const $match = match;

            const handler = new Function('$event', '$', '$data', '$match', '$toast', code);
            handler.call(element, event, $, $data, $match, $toast);
        } catch (error) {
            console.error('[BosonEvents] Error executing handler:', error);
            console.error('Handler code:', code);
        }
    }
}

// Global instance
export const $events = new BosonEventsManager();

// Auto-initialize on DOMContentLoaded
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => $events.init());
    } else {
        $events.init();
    }
}
