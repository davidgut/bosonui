{{-- 
@description Native select input. For custom dropdowns, use x-boson::listbox or x-boson::combobox.
@usage <x-boson::select name="country" placeholder="Select..."><x-boson::select.option value="us">USA</x-boson::select.option></x-boson::select>
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
