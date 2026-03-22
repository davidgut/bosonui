@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('accordion-content')
        ->data('accordion-target', 'content')
        ->role('region');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    <div class="accordion-content-inner">
        {{ $slot }}
    </div>
</{{ $el->getElement() }}>
