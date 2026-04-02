{{--
@description Individual tab button. Set name="value" to link to a matching tabs.panel. Add icon="name" for a leading icon, icon:trailing="name" for trailing. Use :selected="true" to pre-select this tab.
@prefixes icon
@usage <x-boson::tabs.tab name="profile" icon="user" :selected="true">Profile</x-boson::tabs.tab>
--}}

@php
    use DavidGut\Boson\Boson;

    $iconAttrs = Boson::extract($attributes, 'icon');
    $iconTrailingAttrs = Boson::extract($iconAttrs, 'trailing');
    $tabAttrs = Boson::except($attributes, 'icon');

    $name = $tabAttrs->get('name');
    $icon = $tabAttrs->get('icon');
    $iconTrailing = $iconAttrs->get('trailing');
    $selected = $tabAttrs->get('selected', false);

    $el = Boson::element('button')
        ->base('tab')
        ->data('tab-target', 'tab')
        ->data('tab-name', $name)
        ->when($selected, 'data', 'tab-selected', 'true')
        ->role('tab')
        ->attribute('type', 'button')
        ->aria('selected', false);
@endphp

<{{ $el->getElement() }} {{ $tabAttrs->except(['name', 'icon', 'selected'])->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon :name="$icon" :attributes="$iconAttrs->except(['trailing'])->merge(['class' => 'tab-icon'])" />
    @endif

    {{ $slot }}

    @if ($iconTrailing)
        <x-boson::icon :name="$iconTrailing" :attributes="$iconTrailingAttrs->merge(['class' => 'tab-icon'])" />
    @endif
</{{ $el->getElement() }}>

