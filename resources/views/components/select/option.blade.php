{{-- 
@description Native select option. For custom dropdowns, use x-boson::listbox.option or x-boson::combobox.option.
--}}

@props([
    'value' => null,
])

<option value="{{ $value }}" {{ $attributes }}>{{ $slot }}</option>
