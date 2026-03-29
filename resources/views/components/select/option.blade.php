{{-- 
@description Option for the native select component. Set selected to pre-select this option on page load.
@props value, selected
@usage <x-boson::select.option value="us" selected>USA</x-boson::select.option>
--}}

@props([
    'value' => null,
    'selected' => false,
])

<option value="{{ $value }}" {{ $attributes }} @selected($selected)>{{ $slot }}</option>
