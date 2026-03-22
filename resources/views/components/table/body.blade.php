@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('tbody')
        ->base('table-body');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
