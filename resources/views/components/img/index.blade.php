{{-- 
@description Image with lazy loading (default). Set size="sm|md|lg|xl|full" for fixed dimensions. Add circle for circular images, rounded for rounded corners. Use ratio="square|video|portrait" to constrain aspect. Add eager for above-fold images.
@usage <x-boson::img src="/photo.jpg" alt="Photo" />
@usage <x-boson::img src="/photo.jpg" alt="Photo" rounded ratio="video" />
@usage <x-boson::img src="/avatar.jpg" alt="User" circle size="lg" />
--}}

@props([
    'rounded' => false,
    'circle' => false,
    'size' => null,
    'ratio' => null,
    'eager' => false,
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('img')
        ->base('img')
        ->when($rounded, 'mod', 'rounded')
        ->when($circle, 'mod', 'circle')
        ->mod($size)
        ->mod($ratio)
        ->loading($eager);
@endphp

<img {{ $attributes->merge($el->getMergeAttributes()) }}>
