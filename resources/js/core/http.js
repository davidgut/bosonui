/**
 * BosonHttp - Shared HTTP client for Boson components
 * 
 * Provides consistent fetch behavior with:
 * - Automatic CSRF token handling (Laravel convention)
 * - Standard JSON headers
 * - Response parsing utilities
 */
export class BosonHttp {
    /**
     * Get the CSRF token from meta tag (Laravel standard).
     * @returns {string|null}
     */
    static getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    /**
     * Build standard headers for Boson HTTP requests.
     * @param {Object} extra - Additional headers to merge
     * @returns {Object}
     */
    static getHeaders(extra = {}) {
        const csrfToken = this.getCsrfToken();
        return {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            ...extra,
        };
    }

    /**
     * Perform a GET request.
     * @param {string} url - The URL to fetch
     * @param {Object} options - Additional fetch options
     * @returns {Promise<Response>}
     */
    static async get(url, options = {}) {
        return fetch(url, {
            method: 'GET',
            headers: this.getHeaders(options.headers),
            ...options,
        });
    }

    /**
     * Perform a POST request.
     * @param {string} url - The URL to post to
     * @param {FormData|Object|string} body - Request body
     * @param {Object} options - Additional fetch options
     * @returns {Promise<Response>}
     */
    static async post(url, body, options = {}) {
        return fetch(url, {
            method: 'POST',
            headers: this.getHeaders(options.headers),
            body,
            ...options,
        });
    }

    /**
     * Parse a Response object, handling both JSON and text responses.
     * @param {Response} response - The fetch Response
     * @returns {Promise<Object>}
     */
    static async parseResponse(response) {
        const contentType = response.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            return response.json();
        }
        return { message: await response.text() };
    }
}
