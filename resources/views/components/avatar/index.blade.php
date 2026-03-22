{{-- 
@description Avatar. Use src="url" for image, name="Name" for initials (color="auto" generates from name), or icon="name". Badge with badge:content="3" badge:color="red".
@sizes xs, sm, md, lg, xl
@shapes rounded, circle
@colors auto, red, orange, amber, yellow, lime, green, emerald, teal, cyan, sky, blue, indigo, violet, purple, fuchsia, pink, rose
@usage <x-boson::avatar name="John Doe" color="auto" size="lg" />
--}}

@props([
    'src' => null,
    'icon' => null,
    'name' => null,
    'size' => null,
    'color' => null,
    'shape' => 'rounded',
    'as' => null,
    'href' => null,
])

@php
    use DavidGut\Boson\Boson;

    $badgeAttrs = Boson::extract($attributes, 'badge');
    $iconAttrs = Boson::extract($attributes, 'icon');
    $avatarAttrs = Boson::except($attributes, ['badge', 'icon']);

    $resolvedColor = $color;
    if ($color === 'auto' && $name) {
        $colors = ['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 
                   'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'];
        $resolvedColor = $colors[abs(crc32($name)) % count($colors)];
    }

    $initials = '';
    if ($name) {
        $parts = explode(' ', $name);
        $initials = count($parts) >= 2
            ? strtoupper($parts[0][0] . $parts[1][0])
            : strtoupper(substr($name, 0, 2));
    }

    $el = Boson::element($as)->href($href)
        ->base('avatar')
        ->size($size)
        ->mod($shape)
        ->mod($resolvedColor);
@endphp

<{{ $el->getElement() }} {{ $avatarAttrs->merge($el->getMergeAttributes()) }}>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $name ?? 'Avatar' }}" class="avatar-image">
    @elseif ($icon)
        <x-boson::icon :name="$icon" {{ $iconAttrs->merge(['class' => 'avatar-icon']) }} />
    @elseif ($initials)
        <span class="avatar-initials">{{ $initials }}</span>
    @else
        <x-boson::icon name="user" {{ $iconAttrs->merge(['class' => 'avatar-icon']) }} />
    @endif

    @if ($badgeAttrs->isNotEmpty())
        <x-boson::avatar.badge {{ $badgeAttrs }} />
    @endif

    {{ $slot }}
</{{ $el->getElement() }}>
