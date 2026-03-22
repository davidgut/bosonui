{{-- 
@description Custom select dropdown. Set name="field" placeholder="...". Add multiple for multi-select, searchable for filtering. Use async="/url" for remote options (with async:param, async:min, async:debounce). Use listbox.option with value="..." and optional icon="...".
@prefixes async, selected
@usage <x-boson::listbox name="country" placeholder="Select country"><x-boson::listbox.option value="us">United States</x-boson::listbox.option><x-boson::listbox.option value="ca">Canada</x-boson::listbox.option></x-boson::listbox>
@usage <x-boson::listbox name="tags" placeholder="Select tags" multiple searchable><x-boson::listbox.option value="php">PHP</x-boson::listbox.option></x-boson::listbox>
@usage <x-boson::listbox name="user" placeholder="Search users..." async="/api/users" async:min="1"><x-boson::listbox.option value="1">John</x-boson::listbox.option></x-boson::listbox>
@usage <x-boson::listbox.option value="active" icon="check-circle">Active</x-boson::listbox.option>
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
    $multiple = $listboxAttrs->has('multiple');
    $searchable = $listboxAttrs->has('searchable');
    $selectedSuffix = $selectedAttrs->get('suffix', 'selected');

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

<{{ $el->getElement() }} {{ $listboxAttrs->except(['placeholder', 'name', 'multiple', 'searchable', 'async'])->merge($el->getMergeAttributes()) }}>
    @if ($multiple)
        <input type="hidden" name="{{ $name }}" data-listbox-target="input" value="[]">
    @else
        <input type="hidden" name="{{ $name }}" data-listbox-target="input">
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
                No results found
            </div>
        </div>
    </div>
</{{ $el->getElement() }}>
