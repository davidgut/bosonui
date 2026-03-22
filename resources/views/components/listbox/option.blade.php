@props([
    'value' => null,
    'icon' => null,
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('listbox-option')
        ->attribute('role', 'option')
        ->data('listbox-target', 'option')
        ->data('value', $value)
        ->data('label', trim($slot->toHtml()))
        ->attribute('tabindex', '-1');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon :name="$icon" class="listbox-option-icon" />
    @endif
    
    <span class="flex-1">{{ $slot }}</span>
    
    <x-boson::icon name="check" class="listbox-option-check" />
</{{ $el->getElement() }}>
