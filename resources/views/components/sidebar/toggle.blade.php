{{--
@description Mobile sidebar toggle button. Opens the sidebar overlay. Use icon="name" to override the default bars-2 icon. Place outside the sidebar, typically in a header.
@usage <x-boson::sidebar.toggle class="lg:hidden" icon="bars-2" />
--}}

@php
    use DavidGut\Boson\Boson;

    $icon = $attributes->get('icon', 'bars-2');

    $el = Boson::element(null, 'button')
        ->base('sidebar-toggle')
        ->attribute('type', 'button')
        ->data('sidebar-target', 'toggle')
        ->aria('label', 'Open sidebar');
@endphp

<{{ $el->getElement() }} {{ $attributes->except(['icon'])->merge($el->getMergeAttributes()) }}>
    <x-boson::icon :name="$icon" variant="outline" />
</{{ $el->getElement() }}>
