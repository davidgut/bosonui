{{-- 
@description Button. Set variant="primary|danger|ghost|..." and size="sm|lg|...". Add icon="name" for leading icon, icon:trailing="name" for trailing. Use square for icon-only buttons. Use as="a" with href="..." to render as a link. The primary variant is used automatically when no variant prop is passed, so variant="primary" is not needed.
@variants primary, secondary, outline, ghost, subtle, danger, danger-soft
@sizes xs, sm, md, lg, xl
@usage <x-boson::button icon="check">Save</x-boson::button>
@usage <x-boson::button as="a" href="/home">Home</x-boson::button>
--}}

@php
    use DavidGut\Boson\Boson;

    $iconAttrs = Boson::extract($attributes, 'icon');
    $btnAttrs = Boson::except($attributes, 'icon');

    $as = $btnAttrs->get('as', 'button');
    $icon = $btnAttrs->get('icon');
    $iconTrailing = $iconAttrs->get('trailing');

    $el = Boson::element($as)
        ->base('btn')
        ->mod($btnAttrs->get('variant', 'primary'))
        ->mod($btnAttrs->get('size', 'md'))
        ->when($btnAttrs->get('square'), 'mod', 'square');
@endphp

<{{ $el->getElement() }} {{ $btnAttrs->except(['icon', 'variant', 'size', 'square', 'as'])->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon :name="$icon" />
    @endif

    {{ $slot }}

    @if ($iconTrailing)
        <x-boson::icon :name="$iconTrailing" />
    @endif
</{{ $el->getElement() }}>
