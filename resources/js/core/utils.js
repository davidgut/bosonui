/**
 * Generic utility functions for Boson.
 */

/**
 * Look up a value in a map, like PHP's match expression.
 *
 * Usage (inside on:* handlers):
 *   $match($data.status, { pending: 'gray', published: 'green' })
 *   $match($data.role, { admin: 'Admin', user: 'User' }, 'Unknown')
 *
 * @param {*} value - Value to look up
 * @param {Object} map - Key-value mapping
 * @param {*} fallback - Default if no match found
 * @returns {*} Matched value or fallback
 */
export function match(value, map, fallback = '') {
    return map[value] ?? fallback;
}
