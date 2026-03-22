@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('dropdown-trigger')
        ->data('dropdown-target', 'trigger')
        ->aria('haspopup', true)
        ->aria('expanded', false);
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>