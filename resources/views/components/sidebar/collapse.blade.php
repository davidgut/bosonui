{{--
@description Sidebar collapse/close button. Closes the sidebar on mobile. Place inside sidebar.header.
@usage <x-boson::sidebar.collapse class="lg:hidden" />
--}}

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element(null, 'button')
        ->base('sidebar-collapse')
        ->attribute('type', 'button')
        ->data('sidebar-target', 'collapse')
        ->aria('label', 'Collapse sidebar');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    <x-boson::icon name="x-mark" variant="outline" class="sidebar-collapse-icon" />
</{{ $el->getElement() }}>
