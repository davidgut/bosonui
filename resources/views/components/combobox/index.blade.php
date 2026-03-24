{{-- 
@description Combobox component - input-triggered dropdown with type-ahead search and async support.
@prefixes async (async:param, async:min, async:debounce)
@defaults empty message is "No results found", override with empty="No matches"
@usage <x-boson::combobox name="user" async="/api/users" placeholder="Search users..."></x-boson::combobox>
--}}

@php
    use DavidGut\Boson\Boson;

    $asyncAttrs = Boson::extract($attributes, 'async');
    $comboboxAttrs = Boson::except($attributes, 'async');

    $async = $comboboxAttrs->get('async');
    $asyncParam = $asyncAttrs->get('param', 'q');
    $asyncMin = $asyncAttrs->get('min', 2);
    $asyncDebounce = $asyncAttrs->get('debounce', 300);

    $placeholder = $comboboxAttrs->get('placeholder');
    $name = $comboboxAttrs->get('name');
    $empty = $comboboxAttrs->get('empty', 'No results found');

    $el = Boson::element()
        ->base('combobox')
        ->data('controller', 'combobox')
        ->data('placeholder', $placeholder)
        ->when($async, fn ($el) => $el
            ->data('async', $async)
            ->data('async-param', $asyncParam)
            ->data('async-min', $asyncMin)
            ->data('async-debounce', $asyncDebounce)
        );
@endphp

<{{ $el->getElement() }} {{ $comboboxAttrs->except(['placeholder', 'name', 'async', 'empty'])->merge($el->getMergeAttributes()) }}>
    <input type="hidden" name="{{ $name }}" data-combobox-target="hiddenInput">

    <div class="combobox-wrapper">
        <input 
            type="text" 
            class="combobox-input"
            placeholder="{{ $placeholder }}"
            data-combobox-target="input"
            autocomplete="off"
        >
        <x-boson::icon name="chevron-down" class="combobox-icon" data-combobox-target="chevron" />
    </div>

    <div 
        class="combobox-menu" 
        role="listbox"
        data-combobox-target="menu"
        tabindex="-1"
    >
        <div class="combobox-content" data-combobox-target="options">
            {{ $slot }}

            <div class="combobox-no-results" data-combobox-target="noResults" style="display: none;">
                {{ $empty }}
            </div>
        </div>
    </div>
</{{ $el->getElement() }}>
