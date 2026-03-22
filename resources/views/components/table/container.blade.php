@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('table-container');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
