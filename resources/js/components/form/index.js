/**
 * BosonForm - Async form submission with validation error handling
 *
 * Events (dispatched on the form element):
 * - boson:submitting - Before fetch, cancellable via preventDefault()
 * - boson:submitted  - After any response
 * - boson:success    - After 2xx response
 * - boson:error      - After 4xx/5xx or network error
 */

import { BosonHttp } from '../../core/http.js';
import { FORM_SELECTOR } from './constants.js';
import {
    dispatchSubmitting,
    dispatchSubmitted,
    dispatchSuccess,
    dispatchError,
} from './events.js';
import {
    handleRedirect,
    handleUpdate,
    handleAppendToSelect,
    handleFormReset,
    handleModalClose,
    showFieldError,
    clearErrors,
    setLoading,
} from './handlers.js';

export class BosonForm {
    constructor(form) {
        this.form = form;
        this.init();
    }

    init() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async handleSubmit(e) {
        e.preventDefault();

        // Dispatch submitting event (cancellable)
        if (!dispatchSubmitting(this.form)) {
            return; // Cancelled
        }

        clearErrors(this.form);
        setLoading(this.form, true);

        try {
            const response = await this.submit();
            const data = await BosonHttp.parseResponse(response);

            // Dispatch submitted event
            dispatchSubmitted(this.form, response);

            if (response.ok) {
                this.handleSuccess(data, response);
            } else if (response.status === 422) {
                this.handleValidationError(data);
            } else {
                this.handleError(data, response.status);
            }
        } catch (error) {
            this.handleError({ message: 'Network error' }, 0);
        } finally {
            setLoading(this.form, false);
        }
    }

    async submit() {
        const formData = new FormData(this.form);
        const method = this.form.method.toUpperCase();

        if (method === 'GET') {
            return BosonHttp.get(this.form.action);
        }

        return BosonHttp.post(this.form.action, formData);
    }

    handleSuccess(data, response) {
        dispatchSuccess(this.form, data);

        // Update [data-field] elements on the page
        handleUpdate(this.form, data);

        // Handle redirect (suppressed when data-update is set)
        if (handleRedirect(this.form, data, response)) {
            return;
        }

        // Handle append to select
        handleAppendToSelect(this.form, data);

        // Reset form
        handleFormReset(this.form);

        // Close modal
        handleModalClose(this.form);
    }

    handleValidationError(data) {
        const errors = data.errors || {};

        Object.entries(errors).forEach(([field, messages]) => {
            const message = Array.isArray(messages) ? messages[0] : messages;
            showFieldError(this.form, field, message);
        });

        dispatchError(this.form, errors, 422);
    }

    handleError(data, status) {
        dispatchError(
            this.form,
            { _general: data.message || 'Something went wrong' },
            status
        );
    }
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll(FORM_SELECTOR).forEach(form => {
        new BosonForm(form);
    });
});
