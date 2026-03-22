@props([
    'content' => null,
    'position' => 'bottom-right',
    'color' => 'white',
    'circle' => false,
])

@php
    use DavidGut\Boson\Boson;

    $positionClasses = match($position) {
        'top-right' => 'top-0 right-0',
        'top-left' => 'top-0 left-0',
        'bottom-left' => 'bottom-0 left-0',
        default => 'bottom-0 right-0',
    };

    $el = Boson::element('span')
        ->base('avatar-badge')
        ->mod($color)
        ->when($circle, 'mod', 'circle')
        ->class($positionClasses);
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $content }}{{ $slot }}
</{{ $el->getElement() }}>
