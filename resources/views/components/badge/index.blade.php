{{-- 
@description Badge/tag. Set color="green|red|blue|..." and optional variant="pill" for rounded. Add icon="name" for leading icon, icon:trailing="name" for trailing icon. Use as="a" href="..." or as="button" for interactive.
@variants default, pill
@prefixes icon
@usage <x-boson::badge color="green" icon="check">Active</x-boson::badge>
@usage <x-boson::badge color="red" icon:trailing="x-mark">Remove</x-boson::badge>
--}}

@php
    use DavidGut\Boson\Boson;

    $iconAttrs = Boson::extract($attributes, 'icon');
    $badgeAttrs = Boson::except($attributes, 'icon');

    $icon = $badgeAttrs->get('icon');
    $iconTrailing = $iconAttrs->get('trailing');
    $as = $badgeAttrs->get('as', 'span');

    $el = Boson::element($as, 'span')
        ->base('badge')
        ->when($badgeAttrs->get('variant') === 'pill', 'mod', 'pill')
        ->mod($badgeAttrs->get('size'))
        ->mod($badgeAttrs->get('color'));
@endphp

<{{ $el->getElement() }} {{ $badgeAttrs->except(['icon', 'as', 'variant', 'size', 'color'])->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon :name="$icon" />
    @endif

    {{ $slot }}

    @if ($iconTrailing)
        <x-boson::icon :name="$iconTrailing" />
    @endif
</{{ $el->getElement() }}>