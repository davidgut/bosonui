@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('thead')
        ->base('table-head');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
