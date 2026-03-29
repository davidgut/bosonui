{{-- 
@description Listbox - a custom select dropdown with support for single and multi-select, search filtering, and async options.
    Add multiple for multi-select, searchable for local filtering. Use async="/url" for remote options.
    Set value="x" (or :value="['a','b']" for multiple) to pre-select options on page load.
    The selected:suffix prop controls the label when multiple items are selected (e.g. "3 selected").
@props name, placeholder, value, multiple, searchable, empty
@prefixes async (async:param, async:min, async:debounce), selected (selected:suffix)
@defaults empty="No results found", selected:suffix="selected"
@usage <x-boson::listbox name="country" placeholder="Select country" value="us"><x-boson::listbox.option value="us">United States</x-boson::listbox.option></x-boson::listbox>
@usage <x-boson::listbox name="tags" placeholder="Select tags" multiple :value="['php', 'js']"><x-boson::listbox.option value="php">PHP</x-boson::listbox.option><x-boson::listbox.option value="js">JavaScript</x-boson::listbox.option></x-boson::listbox>
@usage <x-boson::listbox name="user" placeholder="Search users..." async="/api/users" searchable></x-boson::listbox>
--}}

@php
    use DavidGut\Boson\Boson;

    $asyncAttrs = Boson::extract($attributes, 'async');
    $selectedAttrs = Boson::extract($attributes, 'selected');
    $listboxAttrs = Boson::except($attributes, ['async', 'selected']);

    $async = $listboxAttrs->get('async');
    $asyncParam = $asyncAttrs->get('param', 'q');
    $asyncMin = $asyncAttrs->get('min', 2);
    $asyncDebounce = $asyncAttrs->get('debounce', 300);

    $placeholder = $listboxAttrs->get('placeholder');
    $name = $listboxAttrs->get('name');
    $value = $listboxAttrs->get('value');
    $multiple = $listboxAttrs->has('multiple');
    $searchable = $listboxAttrs->has('searchable');
    $selectedSuffix = $selectedAttrs->get('suffix', 'selected');
    $empty = $listboxAttrs->get('empty', 'No results found');

    $isSearchable = $searchable || $async;

    $el = Boson::element()
        ->base('listbox')
        ->data('controller', 'listbox')
        ->data('placeholder', $placeholder)
        ->data('multiple', $multiple)
        ->data('selected-suffix', $selectedSuffix)
        ->data('searchable', $isSearchable)
        ->when($async, fn ($el) => $el
            ->data('async', $async)
            ->data('async-param', $asyncParam)
            ->data('async-min', $asyncMin)
            ->data('async-debounce', $asyncDebounce)
        );
@endphp

<{{ $el->getElement() }} {{ $listboxAttrs->except(['placeholder', 'name', 'value', 'multiple', 'searchable', 'async', 'empty'])->merge($el->getMergeAttributes()) }}>
    @if ($multiple)
        <input type="hidden" name="{{ $name }}" data-listbox-target="input" value="{{ $value ? json_encode((array) $value) : '[]' }}">
    @else
        <input type="hidden" name="{{ $name }}" data-listbox-target="input" value="{{ $value }}">
    @endif

    <button 
        type="button" 
        class="listbox-trigger"
        data-listbox-target="trigger"
        aria-haspopup="listbox"
        aria-expanded="false"
        @if ($multiple) aria-multiselectable="true" @endif
    >
        <span class="listbox-trigger-text is-placeholder" data-listbox-target="text">
            {{ $placeholder ?? 'Select...' }}
        </span>
        <x-boson::icon name="chevron-down" class="listbox-trigger-icon" />
    </button>

    <div 
        class="listbox-menu" 
        role="listbox"
        data-listbox-target="menu"
        tabindex="-1"
        @if ($multiple) aria-multiselectable="true" @endif
    >
        @if ($isSearchable)
            <x-boson::listbox.search placeholder="Search..." />
        @endif

        <div class="listbox-content" data-listbox-target="options">
            {{ $slot }}

            <div class="listbox-no-results" data-listbox-target="noResults" style="display: none;">
                {{ $empty }}
            </div>
        </div>
    </div>
</{{ $el->getElement() }}>
