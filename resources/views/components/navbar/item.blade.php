@props([
    'icon' => null,
    'iconTrailing' => null,
    'current' => null,
    'badge' => null,
    'badgeColor' => null,
])
@php
    use DavidGut\Boson\Boson;

    // Determine if it should be an 'a' or 'button' based on href
    $element = $attributes->has('href') ? 'a' : 'button';

    // Auto-detect current page based on href if current prop not explicitly set
    $href = $attributes->get('href', '');
    $isCurrent = $current ?? (
        $href ? request()->is(trim($href, '/') ?: '/') : false
    );

    $el = Boson::element($element)
        ->base('navbar-item')
        ->when($isCurrent, 'mod', 'current')
        ->when($isCurrent, 'attribute', 'aria-current', 'page')
        ->only($element === 'button')
            ->attribute('data-dropdown-target', 'trigger')
            ->attribute('type', 'button')
        ->end();
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon :name="$icon" class="navbar-item-icon" />
    @endif

    <span>{{ $slot }}</span>

    @if ($badge)
        <x-boson::badge :color="$badgeColor" size="xs">{{ $badge }}</x-boson::badge>
    @endif

    @if ($iconTrailing)
        <x-boson::icon :name="$iconTrailing" class="navbar-item-icon" />
    @endif
</{{ $el->getElement() }}>
