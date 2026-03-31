{{--
@description Breadcrumb item. Set href for a link, icon for a leading icon, separator to change the divider icon (default: chevron-right). Omit href for the current/last item. Use turbo:* prefix for Turbo attributes on the link (e.g. turbo:frame, turbo:action).
@prefixes icon, turbo
@usage <x-boson::breadcrumbs.item href="/" icon="home">Home</x-boson::breadcrumbs.item>
@usage <x-boson::breadcrumbs.item separator="slash" href="/">Home</x-boson::breadcrumbs.item>
--}}

@php
    use DavidGut\Boson\Boson;

    $iconAttrs = Boson::extract($attributes, 'icon');
    $turboAttrs = Boson::extract($attributes, 'turbo');
    $itemAttrs = Boson::except($attributes, ['icon', 'turbo']);

    $icon = $itemAttrs->get('icon');
    $iconVariant = $iconAttrs->get('variant', 'mini');
    $href = $itemAttrs->get('href');
    $separator = $itemAttrs->get('separator', 'chevron-right');
    $turbo = $itemAttrs->get('turbo', true);
    $isCurrent = !$href && $slot->isNotEmpty();

    $el = Boson::element('li')
        ->base('breadcrumbs-item')
        ->when($isCurrent, 'mod', 'current')
        ->when($isCurrent, 'attribute', 'aria-current', 'page');

    // Build turbo data attributes for the inner <a> tag
    $turboHtml = Boson::element('a')
        ->when(! $turbo, 'data', 'turbo', 'false')
        ->turbo($turboAttrs);
    $turboDataAttrs = collect($turboHtml->getAttributes())
        ->map(fn ($v, $k) => $k . '="' . e($v) . '"')
        ->implode(' ');
@endphp

<{{ $el->getElement() }} {{ $itemAttrs->except(['icon', 'href', 'separator'])->merge($el->getMergeAttributes()) }}>
    @if ($separator === 'slash')
        <span class="breadcrumbs-separator" aria-hidden="true">/</span>
    @else
        <x-boson::icon :name="$separator" class="breadcrumbs-separator" aria-hidden="true" />
    @endif

    @if ($href)
        <a href="{{ $href }}" class="breadcrumbs-link" {!! $turboDataAttrs !!}>
            @if ($icon)
                <x-boson::icon :name="$icon" :variant="$iconVariant" class="breadcrumbs-icon" />
            @endif
            @if ($slot->isNotEmpty())
                {{ $slot }}
            @endif
        </a>
    @elseif ($icon && $slot->isEmpty())
        <x-boson::icon :name="$icon" :variant="$iconVariant" class="breadcrumbs-icon" />
        {{ $slot }}
    @else
        <span class="breadcrumbs-text">
            @if ($icon)
                <x-boson::icon :name="$icon" :variant="$iconVariant" class="breadcrumbs-icon" />
            @endif
            {{ $slot }}
        </span>
    @endif
</{{ $el->getElement() }}>
