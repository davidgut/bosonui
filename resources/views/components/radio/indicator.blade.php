@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('span')
        ->base('radio-indicator');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    <span class="radio-dot"></span>
</{{ $el->getElement() }}>
