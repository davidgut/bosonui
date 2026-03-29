/**
 * Form success/error handlers
 * Extracted handler logic for BosonForm
 */

import { APPEND_TO_ATTR, NO_RESET_ATTR, NO_CLOSE_ATTR, MODAL_ATTR, ERROR_SELECTOR } from './constants.js';

/**
 * Handle redirect response via native HTTP redirect detection.
 * Suppressed when the response contains update data (data.data key).
 * @param {HTMLFormElement} form
 * @param {Object} data - Parsed response data
 * @param {Response} response - The fetch Response object
 * @returns {boolean} - True if redirecting
 */
export function handleRedirect(form, data, response) {
    if (data?.data) {
        return false;
    }

    if (response.redirected) {
        window.location.href = response.url;
        return true;
    }

    return false;
}

/**
 * Flatten a nested object into dot-notation keys.
 * e.g. { user: { name: "John" } } → { "user.name": "John" }
 * @param {Object} obj - Object to flatten
 * @param {string} prefix - Key prefix for recursion
 * @returns {Object} - Flat key-value pairs
 */
function flattenObject(obj, prefix = '') {
    const result = {};

    for (const [key, value] of Object.entries(obj)) {
        const path = prefix ? `${prefix}.${key}` : key;

        if (value !== null && typeof value === 'object' && !Array.isArray(value)) {
            Object.assign(result, flattenObject(value, path));
        } else {
            result[path] = value;
        }
    }

    return result;
}

/**
 * Handle in-page update of [data-field] elements from response data.
 * Flattens nested data into dot-notation keys, then matches against
 * [data-field] elements on the page. Supports both flat and nested data:
 *   { name: "John" }               → [data-field="name"]
 *   { user: { name: "John" } }     → [data-field="user.name"]
 * @param {HTMLFormElement} form
 * @param {Object} data - Response data (expects { data: { field: value, ... } })
 */
export function handleUpdate(form, data) {
    const fields = data?.data;
    if (!fields || typeof fields !== 'object') {
        return;
    }

    const flat = flattenObject(fields);

    Object.entries(flat).forEach(([field, value]) => {
        const targets = document.querySelectorAll(`[data-field="${field}"]`);
        targets.forEach(target => {
            target.textContent = value;
        });
    });
}

/**
 * Handle appending new item to select element(s).
 * @param {HTMLFormElement} form
 * @param {Object} data - Response data containing the new item
 */
export function handleAppendToSelect(form, data) {
    if (!form.hasAttribute(APPEND_TO_ATTR)) {
        return;
    }

    const selector = form.getAttribute(APPEND_TO_ATTR);
    const targets = document.querySelectorAll(selector);

    if (targets.length === 0) {
        console.warn(`BosonForm: No elements found for selector "${selector}"`);
        return;
    }

    targets.forEach(target => {
        if (target.bosonSelect) {
            // Boson select component
            target.bosonSelect.appendOption(data);
            const value = data.id || data.value;
            if (value) {
                target.bosonSelect.selectValue(value);
            }
        } else if (target.tagName === 'SELECT') {
            // Native select handling
            const value = data.value || data.id || '';
            const label = data.label || data.name || data.text || value;

            const option = new Option(label, value);
            target.add(option);
            target.value = value;
            target.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
}

/**
 * Handle form reset after success.
 * @param {HTMLFormElement} form
 */
export function handleFormReset(form) {
    if (!form.hasAttribute(NO_RESET_ATTR)) {
        form.reset();
    }
}

/**
 * Handle closing parent modal after success.
 * @param {HTMLFormElement} form
 */
export function handleModalClose(form) {
    if (form.hasAttribute(NO_CLOSE_ATTR)) {
        return;
    }

    const modal = form.closest(`[${MODAL_ATTR}]`);
    if (modal && modal.bosonModal) {
        modal.bosonModal.close();
    }
}

/**
 * Show a validation error for a specific field.
 * @param {HTMLFormElement} form
 * @param {string} field - Field name
 * @param {string} message - Error message
 */
export function showFieldError(form, field, message) {
    const container = form.querySelector(`[data-boson-error="${field}"]`);
    if (container) {
        container.textContent = message;
        container.classList.remove('hidden');
    }
}

/**
 * Clear all validation errors from the form.
 * @param {HTMLFormElement} form
 */
export function clearErrors(form) {
    form.querySelectorAll(ERROR_SELECTOR).forEach(el => {
        el.textContent = '';
        el.classList.add('hidden');
    });
}

/**
 * Set loading state on submit button.
 * @param {HTMLFormElement} form
 * @param {boolean} loading
 */
export function setLoading(form, loading) {
    const submitBtn = form.querySelector('[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = loading;
        submitBtn.classList.toggle('loading', loading);
    }
}
