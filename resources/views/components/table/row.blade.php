@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('tr')
        ->base('table-row');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
