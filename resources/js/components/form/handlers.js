/**
 * Form success/error handlers
 * Extracted handler logic for BosonForm
 */

import { APPEND_TO_ATTR, NO_RESET_ATTR, NO_CLOSE_ATTR, MODAL_ATTR, ERROR_SELECTOR } from './constants.js';

/**
 * Handle redirect response.
 * @param {Object} data - Response data
 * @returns {boolean} - True if redirecting
 */
export function handleRedirect(data) {
    if (data.redirect) {
        window.location.href = data.redirect;
        return true;
    }
    return false;
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
