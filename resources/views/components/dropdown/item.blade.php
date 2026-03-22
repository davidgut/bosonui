@php
    use DavidGut\Boson\Boson;

    $iconAttrs = Boson::extract($attributes, 'icon');
    $itemAttrs = Boson::except($attributes, 'icon');

    $icon = $itemAttrs->get('icon');
    $iconTrailing = $iconAttrs->get('trailing');

    $el = Boson::element($itemAttrs->get('as'), 'button')
        ->base('dropdown-item')
        ->href($itemAttrs->get('href'))
        ->class('group')
        ->when($itemAttrs->get('variant') === 'danger', 'mod', 'danger')
        ->role('menuitem')
        ->tabindex(-1);
@endphp

<{{ $el->getElement() }} {{ $itemAttrs->except(['icon', 'variant', 'as'])->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon :name="$icon" class="dropdown-item-icon" />
    @endif

    <span class="flex-1">{{ $slot }}</span>

    @if ($iconTrailing)
        <x-boson::icon :name="$iconTrailing" class="dropdown-item-icon" />
    @endif
</{{ $el->getElement() }}>
