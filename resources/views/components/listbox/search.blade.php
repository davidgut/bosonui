@props([
    'placeholder' => 'Search...',
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('input')
        ->base('listbox-search')
        ->attribute('type', 'text')
        ->attribute('placeholder', $placeholder)
        ->data('listbox-target', 'search')
        ->attribute('autocomplete', 'off');
@endphp

<div class="listbox-search-wrapper">
    <x-boson::icon name="magnifying-glass" class="listbox-search-icon" />
    <{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
</div>
