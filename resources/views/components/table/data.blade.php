@props([
    'align' => 'left', // left, center, right
])

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('td')
        ->base('table-data')
        ->when($align === 'left', 'class', 'text-start')
        ->when($align === 'center', 'class', 'text-center')
        ->when($align === 'right', 'class', 'text-end');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
