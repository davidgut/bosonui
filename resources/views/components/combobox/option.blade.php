@props([
    'value' => null,
    'icon' => null,
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('combobox-option')
        ->attribute('role', 'option')
        ->attribute('data-combobox-target', 'option')
        ->attribute('data-value', $value)
        ->attribute('data-label', trim($slot->toHtml()))
        ->attribute('tabindex', '-1');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon :name="$icon" class="combobox-option-icon" />
    @endif
    
    <span class="flex-1">{{ $slot }}</span>
    
    <x-boson::icon name="check" class="combobox-option-check" />
</{{ $el->getElement() }}>
