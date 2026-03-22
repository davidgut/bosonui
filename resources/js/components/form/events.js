/**
 * Form event utilities
 * Helpers for dispatching Boson form events
 */

/**
 * Event names dispatched by BosonForm
 */
export const EVENTS = {
    SUBMITTING: 'boson:submitting',
    SUBMITTED: 'boson:submitted',
    SUCCESS: 'boson:success',
    ERROR: 'boson:error',
};

/**
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

/**
 * Dispatch the submitting event (cancellable).
 * @param {HTMLFormElement} form
 * @returns {boolean} - False if cancelled
 */
export function dispatchSubmitting(form) {
    return dispatchEvent(form, EVENTS.SUBMITTING, { form }, true);
}

/**
 * Dispatch the submitted event.
 * @param {HTMLFormElement} form
 * @param {Response} response
 */
export function dispatchSubmitted(form, response) {
    dispatchEvent(form, EVENTS.SUBMITTED, { form, response });
}

/**
 * Dispatch the success event.
 * @param {HTMLFormElement} form
 * @param {Object} data
 */
export function dispatchSuccess(form, data) {
    dispatchEvent(form, EVENTS.SUCCESS, { form, data });
}

/**
 * Dispatch the error event.
 * @param {HTMLFormElement} form
 * @param {Object} errors
 * @param {number} status
 */
export function dispatchError(form, errors, status) {
    dispatchEvent(form, EVENTS.ERROR, { form, errors, status });
}
