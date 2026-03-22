@php
    use DavidGut\Boson\Boson;

    $el = Boson::element(null, 'button')
        ->base('accordion-heading')
        ->data('accordion-target', 'heading')
        ->attribute('type', 'button')
        ->aria('expanded', false);
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    <span class="accordion-heading-text">{{ $slot }}</span>
    <x-boson::icon name="chevron-down" variant="mini" class="accordion-icon" />
</{{ $el->getElement() }}>
