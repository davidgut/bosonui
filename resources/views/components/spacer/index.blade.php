{{-- 
@description Empty space. Default is vertical (margin-top). Use size="lg|xl|2xl|..." to adjust. Add vertical for horizontal layouts (margin-left).
@sizes xs, sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl
@usage <x-boson::spacer size="xl" />
--}}

@props([
    'size' => 'md',
    'vertical' => false,
])

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('spacer')
        ->mod($size)
        ->when($vertical, 'mod', 'vertical');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}></{{ $el->getElement() }}>
