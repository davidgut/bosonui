/**
 * Form event utilities
 * Helpers for dispatching Boson form events
 */

import { dispatchEvent } from '../../core/dispatch.js';

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
