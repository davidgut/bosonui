{{-- 
@description Button. Set variant="primary|danger|ghost|..." and size="sm|lg|...". Add icon="name" for leading icon, icon:trailing="name" for trailing. Use square for icon-only buttons. The default variant is used automatically when no variant prop is passed, so variant="default" is not needed.
@variants default, primary, secondary, outline, ghost, subtle, danger, danger-soft
@sizes xs, sm, md, lg, xl
@usage <x-boson::button variant="primary" icon="check">Save</x-boson::button>
--}}

@php
    use DavidGut\Boson\Boson;

    $iconAttrs = Boson::extract($attributes, 'icon');
    $btnAttrs = Boson::except($attributes, 'icon');

    $icon = $btnAttrs->get('icon');
    $iconTrailing = $iconAttrs->get('trailing');

    $el = Boson::element('button')
        ->base('btn')
        ->mod($btnAttrs->get('variant', 'default'))
        ->mod($btnAttrs->get('size', 'md'))
        ->when($btnAttrs->get('square'), 'mod', 'square');
@endphp

<{{ $el->getElement() }} {{ $btnAttrs->except(['icon', 'variant', 'size', 'square'])->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon :name="$icon" />
    @endif

    {{ $slot }}

    @if ($iconTrailing)
        <x-boson::icon :name="$iconTrailing" />
    @endif
</{{ $el->getElement() }}>
