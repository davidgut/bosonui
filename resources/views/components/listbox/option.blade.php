{{-- 
@description Option element for the listbox component.
@usage <x-boson::listbox.option value="1">Option One</x-boson::listbox.option>
--}}

@php
    use DavidGut\Boson\Boson;

    $value = $attributes->get('value');

    $el = Boson::element()
        ->base('listbox-option')
        ->role('option')
        ->data('listbox-target', 'option')
        ->data('value', $value)
        ->data('label', trim($slot->toHtml()))
        ->tabindex('-1');
@endphp

<{{ $el->getElement() }} {{ $attributes->except('value')->merge($el->getMergeAttributes()) }}>
    <span class="flex-1">{{ $slot }}</span>
    <x-boson::icon name="check" class="listbox-option-check" />
</{{ $el->getElement() }}>
