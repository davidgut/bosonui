{{--
@description Sidebar layout container. Use sticky for fixed positioning. Set collapsible="mobile" for mobile overlay behavior.
Contains sub-components: sidebar.header, sidebar.brand, sidebar.collapse, sidebar.search, sidebar.spacer, sidebar.toggle.
Use navlist for navigation, and profile for the user profile button.
@usage
<x-boson::sidebar sticky collapsible="mobile" class="bg-zinc-50 border-r border-zinc-200">
    <x-boson::sidebar.header>
        <x-boson::sidebar.brand href="#" logo="/logo.png" name="Acme Inc." />
        <x-boson::sidebar.collapse class="lg:hidden" />
    </x-boson::sidebar.header>

    <x-boson::sidebar.search placeholder="Search..." />

    <x-boson::navlist>
        <x-boson::navlist.item icon="home" href="/" current>Home</x-boson::navlist.item>
        <x-boson::navlist.item icon="inbox" badge="12" href="/inbox">Inbox</x-boson::navlist.item>
        <x-boson::navlist.group expandable heading="Favorites">
            <x-boson::navlist.item href="#">Marketing site</x-boson::navlist.item>
        </x-boson::navlist.group>
    </x-boson::navlist>

    <x-boson::sidebar.spacer />

    <x-boson::dropdown position="top" align="start">
        <x-boson::profile avatar="/user.png" name="Olivia Martin" />
        <x-boson::dropdown.menu>...</x-boson::dropdown.menu>
    </x-boson::dropdown>
</x-boson::sidebar>
--}}

@php
    use DavidGut\Boson\Boson;

    $collapsible = $attributes->get('collapsible');
    $sticky = $attributes->has('sticky');

    $el = Boson::element(null, 'aside')
        ->base('sidebar')
        ->when($sticky, 'mod', 'sticky')
        ->data('controller', 'sidebar')
        ->data('sidebar-collapsible', $collapsible ?: null);
@endphp

<{{ $el->getElement() }} {{ $attributes->except(['sticky', 'collapsible'])->merge($el->getMergeAttributes()) }}>
    <div class="sidebar-inner">
        {{ $slot }}
    </div>
    <div class="sidebar-overlay" data-sidebar-target="overlay"></div>
</{{ $el->getElement() }}>
