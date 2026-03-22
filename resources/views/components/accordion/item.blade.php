@php
    use DavidGut\Boson\Boson;

    $expanded = $attributes->get('expanded', false);

    $el = Boson::element()
        ->base('accordion-item')
        ->data('accordion-target', 'item')
        ->when($expanded, 'data', 'expanded', 'true');
@endphp

<{{ $el->getElement() }} {{ $attributes->except(['expanded'])->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
