@props([
    'align' => 'left', // left, center, right
    'icon' => null,
    'iconTrailing' => null,
])

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('th')
        ->base('table-header')
        ->when($align === 'left', 'class', 'text-start')
        ->when($align === 'center', 'class', 'text-center')
        ->when($align === 'right', 'class', 'text-end');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    <div @class([
        'inline-flex items-center gap-1.5',
        'justify-center' => $align === 'center',
        'justify-end' => $align === 'right',
        // Default is left, so no class needed for justify-start usually, but flex default is start
    ])>
        @if ($icon)
            <x-boson::icon :name="$icon" class="text-gray-400 group-hover:text-gray-500" />
        @endif

        {{ $slot }}

        @if ($iconTrailing)
            <x-boson::icon :name="$iconTrailing" class="text-gray-400 group-hover:text-gray-500" />
        @endif
    </div>
</{{ $el->getElement() }}>
