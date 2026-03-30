/**
 * Shared constants for listbox and combobox components
 */

export const LISTBOX_SELECTOR = '[data-controller="listbox"]';
export const COMBOBOX_SELECTOR = '[data-controller="combobox"]';

export const CLASSES = {
    SELECTED: 'is-selected',
    FOCUSED: 'is-focused',
    PLACEHOLDER: 'is-placeholder',
    OPEN: 'open',
    ROTATE: 'rotate-180',
    LOADING: 'loading',
};

export const DEFAULTS = {
    PLACEHOLDER: 'Select...',
    SELECTED_SUFFIX: 'selected',
    ASYNC_PARAM: 'q',
    ASYNC_MIN: 2,
    ASYNC_DEBOUNCE: 300,
};
