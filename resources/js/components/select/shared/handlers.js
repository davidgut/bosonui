/**
 * Shared handler functions for listbox and combobox components
 */

import { CLASSES } from './constants.js';

/**
 * Create an option DOM element from item data.
 * @param {Object|string} item - Option data or simple value
 * @param {boolean} isSelected - Whether this option is selected
 * @param {string} targetPrefix - Target attribute prefix ('listbox' or 'combobox')
 * @returns {HTMLElement}
 */
export function createOptionElement(item, isSelected = false, targetPrefix = 'listbox') {
    const value = typeof item === 'object' ? (item.value || item.id || '') : item;
    const label = typeof item === 'object' ? (item.label || item.name || item.text || value) : item;

    const option = document.createElement('div');
    option.className = `${targetPrefix}-option`;
    option.setAttribute('role', 'option');
    option.setAttribute(`data-${targetPrefix}-target`, 'option');
    option.setAttribute('data-value', value);
    option.setAttribute('data-label', label);
    option.setAttribute('tabindex', '-1');

    if (isSelected) {
        option.classList.add(CLASSES.SELECTED);
    }

    let html = '';
    html += `<span class="flex-1">${label}</span>`;
    html += `<svg class="${targetPrefix}-option-check size-4 ml-auto" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
    </svg>`;

    option.innerHTML = html;
    return option;
}

/**
 * Check if a value is selected.
 * @param {string} value - Value to check
 * @param {string|null} selectedValue - Currently selected value (single select)
 * @param {string[]} selectedValues - Currently selected values (multi-select)
 * @param {boolean} isMultiple - Whether this is a multi-select
 * @returns {boolean}
 */
export function isValueSelected(value, selectedValue, selectedValues, isMultiple) {
    if (isMultiple) {
        return selectedValues.includes(String(value));
    }
    return String(selectedValue) === String(value);
}

/**
 * Update the trigger text for single select mode.
 * @param {HTMLElement} triggerText - The trigger text element
 * @param {HTMLElement[]} options - All option elements
 * @param {string|null} selectedValue - Currently selected value
 * @param {string} placeholder - Placeholder text
 */
export function updateSingleDisplay(triggerText, options, selectedValue, placeholder) {
    if (! triggerText) return;

    if (selectedValue) {
        const selectedOption = options.find(opt => opt.dataset.value === selectedValue);
        if (selectedOption) {
            const text = selectedOption.dataset.label || selectedOption.textContent.trim();
            triggerText.textContent = text;
            triggerText.classList.remove(CLASSES.PLACEHOLDER);
            selectedOption.classList.add(CLASSES.SELECTED);
            return;
        }
    }

    triggerText.textContent = placeholder;
    triggerText.classList.add(CLASSES.PLACEHOLDER);
}

/**
 * Update the trigger text for multi-select mode.
 * @param {HTMLElement} triggerText - The trigger text element
 * @param {HTMLElement[]} options - All option elements
 * @param {string[]} selectedValues - Currently selected values
 * @param {string} placeholder - Placeholder text
 * @param {string} selectedSuffix - Suffix for count display
 */
export function updateMultipleDisplay(triggerText, options, selectedValues, placeholder, selectedSuffix) {
    if (! triggerText) return;

    const count = selectedValues.length;

    if (count === 0) {
        triggerText.textContent = placeholder;
        triggerText.classList.add(CLASSES.PLACEHOLDER);
    } else if (count === 1) {
        const selectedOption = options.find(opt => opt.dataset.value === selectedValues[0]);
        if (selectedOption) {
            const text = selectedOption.dataset.label || selectedOption.textContent.trim();
            triggerText.textContent = text;
            triggerText.classList.remove(CLASSES.PLACEHOLDER);
        }
    } else {
        triggerText.textContent = `${count} ${selectedSuffix}`;
        triggerText.classList.remove(CLASSES.PLACEHOLDER);
    }

    options.forEach(opt => {
        if (selectedValues.includes(opt.dataset.value)) {
            opt.classList.add(CLASSES.SELECTED);
        } else {
            opt.classList.remove(CLASSES.SELECTED);
        }
    });
}

/**
 * Set the focused index and update visual state.
 * @param {HTMLElement[]} visibleOptions - Currently visible options
 * @param {number} newIndex - New focus index
 * @returns {number} The new focused index
 */
export function setFocusedOption(visibleOptions, newIndex) {
    visibleOptions.forEach(opt => opt.classList.remove(CLASSES.FOCUSED));

    if (newIndex >= 0 && newIndex < visibleOptions.length) {
        visibleOptions[newIndex].classList.add(CLASSES.FOCUSED);
        visibleOptions[newIndex].scrollIntoView({ block: 'nearest' });
    }

    return newIndex;
}

/**
 * Filter options by search query.
 * @param {HTMLElement[]} options - All option elements
 * @param {string} query - Search query
 * @returns {{ visibleOptions: HTMLElement[], hasMatches: boolean }}
 */
export function filterOptions(options, query) {
    const normalizedQuery = query.toLowerCase().trim();
    let hasMatches = false;

    options.forEach(option => {
        const label = (option.dataset.label || option.textContent).toLowerCase();
        const matches = normalizedQuery === '' || label.includes(normalizedQuery);
        option.style.display = matches ? '' : 'none';
        if (matches) hasMatches = true;
    });

    const visibleOptions = options.filter(opt => opt.style.display !== 'none');

    return { visibleOptions, hasMatches };
}

/**
 * Reset option filtering to show all options.
 * @param {HTMLElement[]} options - All option elements
 * @param {HTMLElement|null} noResultsEl - No results message element
 */
export function resetFiltering(options, noResultsEl) {
    options.forEach(opt => {
        opt.style.display = '';
    });
    if (noResultsEl) {
        noResultsEl.style.display = 'none';
    }
}

/**
 * Clear all dynamic async options.
 * @param {HTMLElement[]} options - Current option elements to remove
 */
export function clearAsyncOptions(options) {
    options.forEach(opt => opt.remove());
}

/**
 * Insert options into the container.
 * @param {HTMLElement} container - Options container element
 * @param {HTMLElement[]} optionElements - Option elements to insert
 * @param {HTMLElement|null} noResultsEl - No results element to insert before
 */
export function insertOptions(container, optionElements, noResultsEl) {
    const fragment = document.createDocumentFragment();
    optionElements.forEach(opt => fragment.appendChild(opt));

    if (noResultsEl) {
        container.insertBefore(fragment, noResultsEl);
    } else {
        container.appendChild(fragment);
    }
}
