/**
 * Shared event utilities for listbox and combobox components
 */

import { dispatchEvent } from '../../../core/dispatch.js';

export const EVENTS = {
    OPEN: 'boson:open',
    CLOSE: 'boson:close',
    CHANGE: 'boson:change',
    SELECT: 'boson:select',
    DESELECT: 'boson:deselect',
};

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
