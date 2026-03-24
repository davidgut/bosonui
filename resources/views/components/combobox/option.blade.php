{{-- 
@description Option element for the combobox component.
@usage <x-boson::combobox.option value="1">John Doe</x-boson::combobox.option>
--}}

@php
    use DavidGut\Boson\Boson;

    $value = $attributes->get('value');

    $el = Boson::element()
        ->base('combobox-option')
        ->role('option')
        ->data('combobox-target', 'option')
        ->data('value', $value)
        ->data('label', trim($slot->toHtml()))
        ->tabindex('-1');
@endphp

<{{ $el->getElement() }} {{ $attributes->except('value')->merge($el->getMergeAttributes()) }}>
    <span class="flex-1">{{ $slot }}</span>
    <x-boson::icon name="check" class="combobox-option-check" />
</{{ $el->getElement() }}>
