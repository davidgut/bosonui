{{-- 
@description Heading. Set level="1-6" for h1-h6 element, size="lg|xl|2xl|..." for visual size. Add serif or mono prop for font family.
@sizes lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl
@usage <x-boson::heading level="2" size="xl">Section Title</x-boson::heading>
--}}

@props([
    'size' => null,
    'level' => null,
    'serif' => false,
    'mono' => false,
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::heading($level)
        ->base('heading')
        ->mod($size)
        ->when($serif, 'mod', 'font-serif')
        ->when($mono, 'mod', 'font-mono');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
