{{-- 
@description Heroicon. Set name="icon-name" (without heroicon- prefix). variant="micro" (16px, default), "mini" (20px), "outline" or "solid" (24px).
@variants outline, solid, mini, micro
@usage <x-boson::icon name="check" variant="solid" class="text-green-500" />
--}}

@props([
    'name',
    'variant' => 'micro',
])

@php
    use DavidGut\Boson\Boson;

    $variants = [
        'outline' => 'heroicon-o-',
        'solid' => 'heroicon-s-',
        'mini' => 'heroicon-m-',
        'micro' => 'heroicon-c-',
    ];

    $iconComponent = "{$variants[$variant]}{$name}";

    $el = Boson::element('svg')
        ->base('icon')
        ->mod($variant);
@endphp

<x-dynamic-component 
    :component="$iconComponent" 
    {{ $attributes->merge($el->getMergeAttributes()) }} 
/>
