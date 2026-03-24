/**
 * BosonCombobox - Input-triggered dropdown with type-ahead search and async support
 */

import { BosonHttp } from '../../core/http.js';
import {
    COMBOBOX_SELECTOR,
    CLASSES,
    DEFAULTS,
} from './shared/constants.js';
import {
    createOptionElement,
    filterOptions,
    resetFiltering,
    clearAsyncOptions,
    insertOptions,
    setFocusedOption,
} from './shared/handlers.js';
import {
    dispatchOpen,
    dispatchClose,
    dispatchChange,
    dispatchSelect,
} from './shared/events.js';

export class BosonCombobox {
    constructor(element) {
        this.element = element;
        this.input = element.querySelector('[data-combobox-target="input"]');
        this.hiddenInput = element.querySelector('[data-combobox-target="hiddenInput"]');
        this.menu = element.querySelector('[data-combobox-target="menu"]');
        this.optionsContainer = element.querySelector('[data-combobox-target="options"]');
        this.noResultsEl = element.querySelector('[data-combobox-target="noResults"]');
        this.chevronIcon = element.querySelector('[data-combobox-target="chevron"]');

        this.options = [];
        this.visibleOptions = [];
        this.isOpen = false;
        this.focusedIndex = -1;
        this.placeholder = element.dataset.placeholder || DEFAULTS.PLACEHOLDER;
        this.emptyText = this.noResultsEl?.textContent?.trim() || 'No results found';

        this.selectedValue = '';
        this.selectedLabel = '';

        this.asyncUrl = element.dataset.async || null;
        this.asyncParam = element.dataset.asyncParam || DEFAULTS.ASYNC_PARAM;
        this.asyncMin = parseInt(element.dataset.asyncMin || DEFAULTS.ASYNC_MIN, 10);
        this.asyncDebounce = parseInt(element.dataset.asyncDebounce || DEFAULTS.ASYNC_DEBOUNCE, 10);
        this.debounceTimer = null;

        this.init();
    }

    init() {
        if (! this.input || ! this.menu) return;

        this.options = Array.from(this.menu.querySelectorAll('[data-combobox-target="option"]'));
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
        this.bindInputEvents();
    }

    bindInputEvents() {
        this.input.addEventListener('focus', () => this.open());

        this.input.addEventListener('input', (e) => {
            if (! this.isOpen) this.open();

            if (this.asyncUrl) {
                this.handleAsyncInput(e.target.value);
            } else {
                this.handleLocalInput(e.target.value);
            }
        });

        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));
    }

    bindOptionEvents() {
        this.options.forEach((option) => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.selectOption(option);
            });

            option.addEventListener('mouseenter', () => {
                const visibleIndex = this.visibleOptions.indexOf(option);
                if (visibleIndex >= 0) {
                    this.setFocusedIndex(visibleIndex);
                }
            });
        });
    }

    bindOutsideClick() {
        document.addEventListener('click', (e) => {
            if (this.isOpen && ! this.element.contains(e.target)) {
                this.close();
            }
        });
    }

    handleLocalInput(value) {
        const { visibleOptions, hasMatches } = filterOptions(this.options, value);
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

    handleAsyncInput(query) {
        clearTimeout(this.debounceTimer);

        if (query.length < this.asyncMin) {
            if (this.initialOptions?.length > 0) {
                this.restoreInitialOptions();
            } else {
                this.clearOptions();
            }
            return;
        }

        this.debounceTimer = setTimeout(() => this.fetchOptions(query), this.asyncDebounce);
    }

    async fetchOptions(query) {
        const url = new URL(this.asyncUrl, window.location.origin);
        url.searchParams.set(this.asyncParam, query);

        try {
            const response = await BosonHttp.get(url.toString());
            if (! response.ok) throw new Error('Failed to fetch');

            const data = await response.json();
            this.renderAsyncOptions(data);
        } catch (error) {
            console.error('BosonCombobox: async fetch error:', error);
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

        if (this.noResultsEl) {
            this.noResultsEl.style.display = 'none';
        }

        const optionElements = items.map(item => createOptionElement(item, false, 'combobox'));
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
            const isSelected = String(item.value) === String(this.selectedValue);

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
        this.options = Array.from(this.menu.querySelectorAll('[data-combobox-target="option"]'));
        this.visibleOptions = [...this.options];
        this.bindOptionEvents();
    }

    handleKeydown(e) {
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (! this.isOpen) {
                    this.open();
                } else if (this.visibleOptions.length > 0) {
                    this.setFocusedIndex(Math.min(this.focusedIndex + 1, this.visibleOptions.length - 1));
                }
                break;
            case 'ArrowUp':
                e.preventDefault();
                if (this.visibleOptions.length > 0) {
                    this.setFocusedIndex(Math.max(this.focusedIndex - 1, 0));
                }
                break;
            case 'Enter':
                e.preventDefault();
                if (this.focusedIndex >= 0 && this.visibleOptions[this.focusedIndex]) {
                    this.selectOption(this.visibleOptions[this.focusedIndex]);
                }
                break;
            case 'Escape':
            case 'Tab':
                this.close();
                break;
        }
    }

    setFocusedIndex(index) {
        this.focusedIndex = setFocusedOption(this.visibleOptions, index);
    }

    selectOption(option) {
        const value = option.dataset.value || '';
        const label = option.dataset.label || option.textContent.trim();

        if (this.hiddenInput) {
            this.hiddenInput.value = value;
        }

        this.selectedValue = value;
        this.selectedLabel = label;
        this.input.value = label;

        this.options.forEach(opt => {
            opt.classList.toggle(CLASSES.SELECTED, String(opt.dataset.value) === String(value));
        });

        dispatchSelect(this.element, value, label);
        dispatchChange(this.element, value, label);

        this.close();
        resetFiltering(this.options, this.noResultsEl);
        this.visibleOptions = [...this.options];
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
            console.warn(`BosonCombobox: Option with value "${value}" not found.`);
        }
    }

    open() {
        this.isOpen = true;
        this.menu.classList.add(CLASSES.OPEN);

        dispatchOpen(this.element);

        if (this.chevronIcon) {
            this.chevronIcon.classList.add(CLASSES.ROTATE);
        }

        if (this.visibleOptions.length > 0) {
            const focusIndex = this.visibleOptions.findIndex(opt => opt.classList.contains(CLASSES.SELECTED));
            this.setFocusedIndex(focusIndex >= 0 ? focusIndex : 0);
        }
    }

    close() {
        this.isOpen = false;
        this.menu.classList.remove(CLASSES.OPEN);

        dispatchClose(this.element);

        if (this.chevronIcon) {
            this.chevronIcon.classList.remove(CLASSES.ROTATE);
        }

        this.input.value = this.selectedLabel || '';
        resetFiltering(this.options, this.noResultsEl);
        this.visibleOptions = [...this.options];

        this.focusedIndex = -1;
        this.options.forEach(opt => opt.classList.remove(CLASSES.FOCUSED));
        clearTimeout(this.debounceTimer);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll(COMBOBOX_SELECTOR).forEach(el => {
        el.bosonCombobox = new BosonCombobox(el);
    });
});
