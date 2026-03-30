/**
 * Chainable DOM helper for event handlers.
 *
 * Creates a jQuery-like wrapper around matched elements.
 * All methods return the chain for fluent usage.
 *
 * Usage (inside on:* handlers):
 *   $('#badge').text('Active').data('color', 'green')
 *   $('#avatar').attr('src', url)
 *   $('#section').toggle(false)
 */

/**
 * Create a chainable wrapper around elements matching a CSS selector.
 * @param {string} selector - CSS selector
 * @returns {Object} Chainable wrapper with .text(), .class(), .data(), .attr(), .toggle()
 */
export function createDomHelper(selector) {
    const els = document.querySelectorAll(selector);

    const chain = {
        text(v)      { els.forEach(el => el.textContent = v); return chain; },
        class(v)     { els.forEach(el => el.className = v); return chain; },
        data(k, v)   { els.forEach(el => el.setAttribute(`data-${k}`, v)); return chain; },
        attr(k, v)   { els.forEach(el => el.setAttribute(k, v)); return chain; },
        toggle(show) { els.forEach(el => el.hidden = !show); return chain; },
    };

    return chain;
}
