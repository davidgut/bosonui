{{--
@description Navbar item. Set icon="name" for leading icon, icon:trailing="name" for trailing icon. Pass badge="count" with badge:color, badge:size for badge customization. Auto-detects current page from href, or set current explicitly. Use turbo:* prefix for Turbo attributes (e.g. turbo:frame, turbo:action).
@usage <x-boson::navbar.item icon="home" href="/">Home</x-boson::navbar.item>
@usage <x-boson::navbar.item icon="inbox" badge="12" badge:color="red" href="/inbox">Inbox</x-boson::navbar.item>
@defaults icon inherits variant from icon component (default: mini), overridable via icon:variant
--}}

@php
    use DavidGut\Boson\Boson;

    $iconAttrs = Boson::extract($attributes, 'icon');
    $iconTrailingAttrs = Boson::extract($iconAttrs, 'trailing');
    $badgeAttrs = Boson::extract($attributes, 'badge');
    $turboAttrs = Boson::extract($attributes, 'turbo');
    $itemAttrs = Boson::except($attributes, ['icon', 'badge', 'turbo']);

    $icon = $itemAttrs->get('icon');
    $iconTrailing = $iconAttrs->get('trailing');
    $badge = $itemAttrs->get('badge');
    $current = $itemAttrs->get('current');
    $turbo = $itemAttrs->get('turbo', true);

    $element = $itemAttrs->has('href') ? 'a' : 'button';

    $href = $itemAttrs->get('href', '');
    $isCurrent = $current ?? (
        $href ? request()->is(trim($href, '/') ?: '/') : false
    );

    $el = Boson::element($element)
        ->base('navbar-item')
        ->when($isCurrent, 'mod', 'current')
        ->when($isCurrent, 'attribute', 'aria-current', 'page')
        ->when(! $turbo, 'data', 'turbo', 'false')
        ->turbo($turboAttrs)
        ->only($element === 'button')
            ->data('dropdown-target', 'trigger')
            ->attribute('type', 'button')
        ->end();
@endphp

<{{ $el->getElement() }} {{ $itemAttrs->except(['icon', 'badge', 'current', 'turbo'])->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon
            :name="$icon"
            {{ $iconAttrs->except(['trailing'])->merge(['class' => 'navbar-item-icon']) }}
        />
    @endif

    <span>{{ $slot }}</span>

    @if ($badge)
        <x-boson::badge :attributes="$badgeAttrs->merge(['size' => 'xs'])">{{ $badge }}</x-boson::badge>
    @endif

    @if ($iconTrailing)
        <x-boson::icon
            :name="$iconTrailing"
            {{ $iconTrailingAttrs->merge(['class' => 'navbar-item-icon']) }}
        />
    @endif
</{{ $el->getElement() }}>
