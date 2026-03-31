{{-- 
@description Badge/tag. Set color="green|red|blue|..." and optional variant="pill" for rounded. Add icon="name" for leading icon, icon:trailing="name" for trailing icon. Use as="a" href="..." or as="button" for interactive. Supports turbo:* attributes when used as link/button. Props render as data attributes (data-color, data-size, data-variant) for easy JS-driven updates.
@variants default, pill
@prefixes icon, turbo
@usage <x-boson::badge color="green" icon="check">Active</x-boson::badge>
@usage <x-boson::badge color="red" icon:trailing="x-mark">Remove</x-boson::badge>
--}}

@php
    use DavidGut\Boson\Boson;

    $iconAttrs = Boson::extract($attributes, 'icon');
    $turboAttrs = Boson::extract($attributes, 'turbo');
    $badgeAttrs = Boson::except($attributes, ['icon', 'turbo']);

    $icon = $badgeAttrs->get('icon');
    $iconTrailing = $iconAttrs->get('trailing');
    $as = $badgeAttrs->get('as', 'span');
    $turbo = $badgeAttrs->get('turbo', true);

    $el = Boson::element($as, 'span')
        ->base('badge')
        ->data('variant', $badgeAttrs->get('variant'))
        ->data('size', $badgeAttrs->get('size'))
        ->data('color', $badgeAttrs->get('color'))
        ->when(! $turbo, 'data', 'turbo', 'false')
        ->turbo($turboAttrs);
@endphp

<{{ $el->getElement() }} {{ $badgeAttrs->except(['icon', 'as', 'variant', 'size', 'color', 'turbo'])->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon :name="$icon" />
    @endif

    {{ $slot }}

    @if ($iconTrailing)
        <x-boson::icon :name="$iconTrailing" />
    @endif
</{{ $el->getElement() }}>