/**
 * Shared event dispatch helper for Boson components.
 *
 * Create and dispatch a custom event on an element.
 * @param {HTMLElement} element - The element to dispatch on
 * @param {string} eventName - The event name (e.g., 'boson:success')
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
