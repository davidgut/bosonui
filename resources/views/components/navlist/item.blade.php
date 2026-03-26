{{--
@description Navlist item. Set icon="name" for leading icon, badge="count" for badge. Pass badge:variant, badge:size, badge:color for badge customization. Auto-detects current page from href, or set current explicitly.
@usage <x-boson::navlist.item icon="home" href="/" current>Home</x-boson::navlist.item>
@usage <x-boson::navlist.item icon="inbox" badge="12" badge:color="red" href="/inbox">Inbox</x-boson::navlist.item>
--}}

@php
    use DavidGut\Boson\Boson;

    $badgeAttrs = Boson::extract($attributes, 'badge');
    $itemAttrs = Boson::except($attributes, 'badge');

    $icon = $itemAttrs->get('icon');
    $badge = $itemAttrs->get('badge');
    $current = $itemAttrs->get('current');

    $element = $itemAttrs->has('href') ? 'a' : 'button';

    $href = $itemAttrs->get('href', '');
    $isCurrent = $current ?? (
        $href ? request()->is(trim($href, '/') ?: '/') : false
    );

    $el = Boson::element($element)
        ->base('navlist-item')
        ->when($isCurrent, 'mod', 'current')
        ->when($isCurrent, 'attribute', 'aria-current', 'page');
@endphp

<{{ $el->getElement() }} {{ $itemAttrs->except(['icon', 'badge', 'current'])->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon :name="$icon" variant="outline" class="navlist-item-icon" />
    @endif

    <span class="navlist-item-text">{{ $slot }}</span>

    @if ($badge)
        <x-boson::badge :attributes="$badgeAttrs->merge(['size' => 'xs'])">{{ $badge }}</x-boson::badge>
    @endif
</{{ $el->getElement() }}>
