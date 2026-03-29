{{-- 
@description Native HTML select input. Renders a standard <select> element with optional placeholder.
    Add multiple for native multi-select. Use select.option with value="x" and selected to pre-select.
    For custom styled dropdowns, use x-boson::listbox or x-boson::combobox instead.
@props name, placeholder, multiple
@usage <x-boson::select name="country" placeholder="Select..."><x-boson::select.option value="us">USA</x-boson::select.option></x-boson::select>
@usage <x-boson::select name="country"><x-boson::select.option value="us" selected>USA</x-boson::select.option></x-boson::select>
--}}

@props([
    'placeholder' => null,
    'name' => null,
    'multiple' => false,
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('select')
        ->base('select')
        ->name($name, array: $multiple)
        ->when($multiple, 'flag', 'multiple');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    @if ($placeholder && ! $multiple)
        <option value="" disabled selected>{{ $placeholder }}</option>
    @endif
    {{ $slot }}
</{{ $el->getElement() }}>
