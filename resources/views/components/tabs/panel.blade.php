{{--
@description Tab panel content area. Set name="value" to match a tab's name. Hidden by default, shown when its tab is active.
@usage <x-boson::tabs.panel name="profile">Profile content...</x-boson::tabs.panel>
--}}

@php
    use DavidGut\Boson\Boson;

    $name = $attributes->get('name');

    $el = Boson::element()
        ->base('tab-panel')
        ->data('tab-target', 'panel')
        ->data('tab-name', $name)
        ->role('tabpanel');
@endphp

<{{ $el->getElement() }} {{ $attributes->except(['name'])->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
