/**
 * BosonListbox - Custom listbox component with single/multi-select,
 * search filtering, and async options support
 */

import { lifecycle } from '../../core/lifecycle.js';
import { BosonHttp } from '../../core/http.js';
import {
    LISTBOX_SELECTOR,
    CLASSES,
    DEFAULTS,
} from './shared/constants.js';
import {
    createOptionElement,
    isValueSelected,
    updateSingleDisplay,
    updateMultipleDisplay,
    setFocusedOption,
    filterOptions,
    clearAsyncOptions,
    insertOptions,
} from './shared/handlers.js';
import {
    dispatchOpen,
    dispatchClose,
    dispatchChange,
    dispatchSelect,
    dispatchDeselect,
} from './shared/events.js';

export class BosonListbox {
    constructor(element) {
        this.element = element;
        this.trigger = element.querySelector('[data-listbox-target="trigger"]');
        this.menu = element.querySelector('[data-listbox-target="menu"]');
        this.input = element.querySelector('[data-listbox-target="input"]');
        this.triggerText = element.querySelector('[data-listbox-target="text"]');
        this.searchInput = element.querySelector('[data-listbox-target="search"]');
        this.optionsContainer = element.querySelector('[data-listbox-target="options"]');
        this.noResultsEl = element.querySelector('[data-listbox-target="noResults"]');

        this.options = [];
        this.visibleOptions = [];
        this.isOpen = false;
        this.focusedIndex = -1;
        this.placeholder = element.dataset.placeholder || DEFAULTS.PLACEHOLDER;
        this.emptyText = this.noResultsEl?.textContent?.trim() || 'No results found';

        this.isMultiple = element.dataset.multiple === 'true';
        this.selectedSuffix = element.dataset.selectedSuffix || DEFAULTS.SELECTED_SUFFIX;
        this.selectedValues = [];
        this.selectedValue = null;

        this.isSearchable = element.dataset.searchable === 'true';
        this.asyncUrl = element.dataset.async || null;
        this.asyncParam = element.dataset.asyncParam || DEFAULTS.ASYNC_PARAM;
        this.asyncMin = parseInt(element.dataset.asyncMin || DEFAULTS.ASYNC_MIN, 10);
        this.asyncDebounce = parseInt(element.dataset.asyncDebounce || DEFAULTS.ASYNC_DEBOUNCE, 10);
        this.debounceTimer = null;

        this.abortController = new AbortController();
        this.init();
    }

    init() {
        if (! this.trigger || ! this.menu) return;

        this.options = Array.from(this.menu.querySelectorAll('[data-listbox-target="option"]'));
        this.visibleOptions = [...this.options];

        if (this.asyncUrl && this.options.length > 0) {
            this.initialOptions = this.options.map(opt => ({
                value: opt.dataset.value,
                label: opt.dataset.label || opt.textContent.trim(),
                element: opt.cloneNode(true)
            }));
        }

        this.bindOptionEvents();
        this.bindOutsideClick();
        this.initFromValue();
        this.bindTriggerEvents();
        this.bindSearchEvents();
    }

    initFromValue() {
        if (! this.input || ! this.input.value) return;

        if (this.isMultiple) {
            try {
                this.selectedValues = JSON.parse(this.input.value) || [];
            } catch (e) {
                this.selectedValues = [];
            }
        } else {
            this.selectedValue = this.input.value;
        }
        this.updateDisplay();
    }

    bindTriggerEvents() {
        this.trigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggle();
        });

        this.trigger.addEventListener('keydown', (e) => this.handleKeydown(e));
        this.menu.addEventListener('keydown', (e) => this.handleKeydown(e));
    }

    bindSearchEvents() {
        if (! this.searchInput) return;

        this.searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));
        this.searchInput.addEventListener('keydown', (e) => this.handleSearchKeydown(e));
        this.searchInput.addEventListener('click', (e) => e.stopPropagation());
    }

    bindOptionEvents() {
        this.optionsContainer.addEventListener('click', (e) => {
            const option = e.target.closest('[data-listbox-target="option"]');
            if (option) {
                e.preventDefault();
                e.stopPropagation();
                this.selectOption(option);
            }
        });

        this.optionsContainer.addEventListener('mouseenter', (e) => {
            const option = e.target.closest('[data-listbox-target="option"]');
            if (option) {
                const visibleIndex = this.visibleOptions.indexOf(option);
                if (visibleIndex >= 0) {
                    this.setFocusedIndex(visibleIndex);
                }
            }
        }, true);
    }

    bindOutsideClick() {
        document.addEventListener('click', (e) => {
            if (this.isOpen && ! this.element.contains(e.target)) {
                this.close();
            }
        }, { signal: this.abortController.signal });
    }

    handleSearch(query) {
        if (this.asyncUrl) {
            this.handleAsyncSearch(query);
        } else {
            this.handleLocalSearch(query);
        }
    }

    handleLocalSearch(query) {
        const { visibleOptions, hasMatches } = filterOptions(this.options, query);
        this.visibleOptions = visibleOptions;

        if (this.noResultsEl) {
            this.noResultsEl.style.display = hasMatches ? 'none' : 'block';
        }

        if (this.visibleOptions.length > 0) {
            this.setFocusedIndex(0);
        } else {
            this.focusedIndex = -1;
        }
    }

    handleAsyncSearch(query) {
        clearTimeout(this.debounceTimer);
        const normalizedQuery = query.trim();

        if (this.noResultsEl) {
            this.noResultsEl.style.display = 'none';
        }

        if (normalizedQuery.length < this.asyncMin) {
            if (this.initialOptions?.length > 0) {
                this.restoreInitialOptions();
            } else {
                this.clearOptions();
            }
            return;
        }

        this.debounceTimer = setTimeout(() => this.fetchOptions(normalizedQuery), this.asyncDebounce);
    }

    async fetchOptions(query) {
        try {
            const url = new URL(this.asyncUrl, window.location.origin);
            url.searchParams.set(this.asyncParam, query);

            const response = await BosonHttp.get(url.toString());

            if (! response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const json = await response.json();
            const items = json.data || json;
            this.renderAsyncOptions(items);
        } catch (error) {
            console.error('BosonListbox: Failed to fetch options', error);
            this.clearOptions();
            if (this.noResultsEl) {
                this.noResultsEl.textContent = 'Failed to load options';
                this.noResultsEl.style.display = 'block';
            }
        }
    }

    renderAsyncOptions(items) {
        this.clearOptions();

        if (! items || items.length === 0) {
            if (this.noResultsEl) {
                this.noResultsEl.textContent = this.emptyText;
                this.noResultsEl.style.display = 'block';
            }
            return;
        }

        const optionElements = items.map(item => {
            const isSelected = isValueSelected(
                typeof item === 'object' ? (item.value || item.id) : item,
                this.selectedValue,
                this.selectedValues,
                this.isMultiple
            );
            return createOptionElement(item, isSelected, 'listbox');
        });

        insertOptions(this.optionsContainer, optionElements, this.noResultsEl);
        this.refreshOptions();

        if (this.visibleOptions.length > 0) {
            this.setFocusedIndex(0);
        }
    }

    clearOptions() {
        clearAsyncOptions(this.options);
        this.options = [];
        this.visibleOptions = [];
        this.focusedIndex = -1;
    }

    restoreInitialOptions() {
        if (! this.initialOptions?.length) return;

        this.clearOptions();

        const optionElements = this.initialOptions.map(item => {
            const option = item.element.cloneNode(true);
            const isSelected = isValueSelected(item.value, this.selectedValue, this.selectedValues, this.isMultiple);

            if (isSelected) {
                option.classList.add(CLASSES.SELECTED);
            } else {
                option.classList.remove(CLASSES.SELECTED);
            }
            return option;
        });

        insertOptions(this.optionsContainer, optionElements, this.noResultsEl);
        this.refreshOptions();

        if (this.noResultsEl) {
            this.noResultsEl.style.display = 'none';
        }
    }

    refreshOptions() {
        this.options = Array.from(this.menu.querySelectorAll('[data-listbox-target="option"]'));
        this.visibleOptions = [...this.options];
    }

    handleSearchKeydown(e) {
        switch (e.key) {
            case 'Enter':
                e.preventDefault();
                e.stopPropagation();
                const optionToSelect = this.visibleOptions[this.focusedIndex] || this.visibleOptions[0];
                if (optionToSelect) {
                    this.selectOption(optionToSelect);
                }
                break;
            case 'Escape':
                e.preventDefault();
                this.close();
                this.trigger.focus();
                break;
            case 'ArrowDown':
                e.preventDefault();
                this.setFocusedIndex(Math.min(this.focusedIndex + 1, this.visibleOptions.length - 1));
                break;
            case 'ArrowUp':
                e.preventDefault();
                this.setFocusedIndex(Math.max(this.focusedIndex - 1, 0));
                break;
            case 'Tab':
                this.close();
                break;
        }
    }

    handleKeydown(e) {
        if (this.isSearchable && this.isOpen && e.target === this.searchInput) {
            return;
        }

        switch (e.key) {
            case 'Enter':
            case ' ':
                e.preventDefault();
                if (this.isOpen && this.focusedIndex >= 0) {
                    this.selectOption(this.visibleOptions[this.focusedIndex]);
                } else if (! this.isOpen) {
                    this.open();
                }
                break;
            case 'Escape':
                e.preventDefault();
                this.close();
                this.trigger.focus();
                break;
            case 'ArrowDown':
                e.preventDefault();
                if (! this.isOpen) {
                    this.open();
                } else {
                    this.setFocusedIndex(Math.min(this.focusedIndex + 1, this.visibleOptions.length - 1));
                }
                break;
            case 'ArrowUp':
                e.preventDefault();
                if (this.isOpen) {
                    this.setFocusedIndex(Math.max(this.focusedIndex - 1, 0));
                }
                break;
            case 'Home':
                e.preventDefault();
                if (this.isOpen) {
                    this.setFocusedIndex(0);
                }
                break;
            case 'End':
                e.preventDefault();
                if (this.isOpen) {
                    this.setFocusedIndex(this.visibleOptions.length - 1);
                }
                break;
            case 'Tab':
                if (this.isOpen) {
                    this.close();
                }
                break;
        }
    }

    setFocusedIndex(index) {
        this.focusedIndex = setFocusedOption(this.visibleOptions, index);
    }

    selectOption(option) {
        const value = option.dataset.value;

        if (this.isMultiple) {
            this.toggleMultipleOption(option, value);
        } else {
            this.selectSingleOption(option, value);
        }
    }

    selectSingleOption(option, value) {
        const label = option.dataset.label || option.textContent.trim();

        if (this.input) {
            this.input.value = value;
            this.input.dispatchEvent(new Event('change', { bubbles: true }));
        }

        this.selectedValue = value;
        this.updateDisplay();

        this.options.forEach(opt => opt.classList.remove(CLASSES.SELECTED));
        option.classList.add(CLASSES.SELECTED);

        dispatchSelect(this.element, value, label);
        dispatchChange(this.element, value, label);

        this.close();

        if (this.searchInput) {
            this.searchInput.blur();
        }
        setTimeout(() => this.trigger.focus(), 10);
    }

    toggleMultipleOption(option, value) {
        const label = option.dataset.label || option.textContent.trim();
        const index = this.selectedValues.indexOf(value);

        if (index > -1) {
            this.selectedValues.splice(index, 1);
            option.classList.remove(CLASSES.SELECTED);
            dispatchDeselect(this.element, value, label);
        } else {
            this.selectedValues.push(value);
            option.classList.add(CLASSES.SELECTED);
            dispatchSelect(this.element, value, label);
        }

        if (this.input) {
            this.input.value = JSON.stringify(this.selectedValues);
            this.input.dispatchEvent(new Event('change', { bubbles: true }));
        }

        dispatchChange(this.element, [...this.selectedValues], null);

        this.updateDisplay();
    }

    updateDisplay() {
        if (! this.triggerText) return;

        if (this.isMultiple) {
            updateMultipleDisplay(
                this.triggerText,
                this.options,
                this.selectedValues,
                this.placeholder,
                this.selectedSuffix
            );
        } else {
            updateSingleDisplay(
                this.triggerText,
                this.options,
                this.selectedValue,
                this.placeholder
            );
        }
    }

    /**
     * Append a new option to the listbox.
     * @param {Object|string} item - Option data
     */
    appendOption(item) {
        const value = typeof item === 'object' ? (item.value || item.id || '') : item;
        const exists = this.options.some(opt => opt.dataset.value === String(value));

        if (exists) {
            console.warn(`BosonListbox: Option with value "${value}" already exists.`);
            return;
        }

        const option = createOptionElement(item, false, 'listbox');
        insertOptions(this.optionsContainer, [option], this.noResultsEl);
        this.refreshOptions();

        if (this.noResultsEl?.style.display !== 'none') {
            this.noResultsEl.style.display = 'none';
        }
    }

    /**
     * Programmatically select a value.
     * @param {string} value - Value to select
     */
    selectValue(value) {
        const option = this.options.find(opt => opt.dataset.value === String(value));

        if (option) {
            this.selectOption(option);
        } else {
            console.warn(`BosonListbox: Option with value "${value}" not found.`);
        }
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        this.isOpen = true;
        this.menu.classList.add(CLASSES.OPEN);

        dispatchOpen(this.element);

        this.trigger.setAttribute('aria-expanded', 'true');

        if (this.searchInput) {
            this.searchInput.value = '';

            if (this.asyncUrl) {
                if (this.initialOptions?.length > 0) {
                    this.restoreInitialOptions();
                } else {
                    this.clearOptions();
                }
            } else {
                this.handleLocalSearch('');
            }

            setTimeout(() => this.searchInput.focus(), 0);
        }

        if (this.visibleOptions.length > 0) {
            let focusIndex = 0;
            if (this.isMultiple && this.selectedValues.length > 0) {
                focusIndex = this.visibleOptions.findIndex(opt => this.selectedValues.includes(opt.dataset.value));
            } else {
                focusIndex = this.visibleOptions.findIndex(opt => opt.classList.contains(CLASSES.SELECTED));
            }
            this.setFocusedIndex(focusIndex >= 0 ? focusIndex : 0);
        }
    }

    close() {
        this.isOpen = false;
        this.menu.classList.remove(CLASSES.OPEN);

        dispatchClose(this.element);

        this.trigger.setAttribute('aria-expanded', 'false');

        this.focusedIndex = -1;
        this.options.forEach(opt => opt.classList.remove(CLASSES.FOCUSED));
        clearTimeout(this.debounceTimer);
    }

    destroy() {
        this.abortController.abort();
        clearTimeout(this.debounceTimer);
    }
}

lifecycle.register('listbox', BosonListbox);
