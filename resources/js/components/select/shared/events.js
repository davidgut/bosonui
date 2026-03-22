/**
 * Shared event utilities for listbox and combobox components
 */

export const EVENTS = {
    OPEN: 'boson:open',
    CLOSE: 'boson:close',
    CHANGE: 'boson:change',
    SELECT: 'boson:select',
    DESELECT: 'boson:deselect',
};

/**
 * Create and dispatch a custom event on an element.
 * @param {HTMLElement} element - The element to dispatch on
 * @param {string} eventName - The event name
 * @param {Object} detail - Event detail payload
 * @param {boolean} cancelable - Whether the event is cancelable
 * @returns {boolean} - False if event was prevented, true otherwise
 */
export function dispatchEvent(element, eventName, detail = {}, cancelable = false) {
    const event = new CustomEvent(eventName, {
        bubbles: true,
        cancelable,
        detail,
    });
    return element.dispatchEvent(event);
}

export function dispatchOpen(element) {
    dispatchEvent(element, EVENTS.OPEN, { element });
}

export function dispatchClose(element) {
    dispatchEvent(element, EVENTS.CLOSE, { element });
}

export function dispatchChange(element, value, label = null) {
    dispatchEvent(element, EVENTS.CHANGE, { element, value, label });
}

export function dispatchSelect(element, value, label) {
    dispatchEvent(element, EVENTS.SELECT, { element, value, label });
}

export function dispatchDeselect(element, value, label) {
    dispatchEvent(element, EVENTS.DESELECT, { element, value, label });
}
