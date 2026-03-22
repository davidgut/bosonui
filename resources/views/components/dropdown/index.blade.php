{{-- 
@description Dropdown menu. Wrap trigger in dropdown.trigger, items in dropdown.menu. Use dropdown.item for each action (href for links, variant="danger" for destructive). Group with dropdown.group heading="Label".
@placements bottom-start, bottom-end, top-start, top-end, right-start, left-start
@usage <x-boson::dropdown><x-boson::dropdown.trigger><x-boson::button>Menu</x-boson::button></x-boson::dropdown.trigger><x-boson::dropdown.menu><x-boson::dropdown.item icon="pencil">Edit</x-boson::dropdown.item></x-boson::dropdown.menu></x-boson::dropdown>
--}}

@props([
    'placement' => 'bottom-start',
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('dropdown')
        ->data('controller', 'dropdown')
        ->data('placement', $placement);
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
