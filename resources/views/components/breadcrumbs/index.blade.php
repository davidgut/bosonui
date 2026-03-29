{{--
@description Breadcrumb navigation. Contains breadcrumbs.item children with optional href, icon, and separator props.
@usage <x-boson::breadcrumbs><x-boson::breadcrumbs.item href="/">Home</x-boson::breadcrumbs.item><x-boson::breadcrumbs.item>Current</x-boson::breadcrumbs.item></x-boson::breadcrumbs>
@usage Ellipsis dropdown:
    <x-boson::breadcrumbs.item>
        <x-boson::dropdown>
            <x-boson::dropdown.trigger>
                <x-boson::button icon="ellipsis-horizontal" variant="ghost" size="sm" square />
            </x-boson::dropdown.trigger>
            <x-boson::dropdown.menu>
                <x-boson::dropdown.item href="/docs/getting-started">Getting Started</x-boson::dropdown.item>
                <x-boson::dropdown.item href="/docs/installation">Installation</x-boson::dropdown.item>
                <x-boson::dropdown.item href="/docs/configuration">Configuration</x-boson::dropdown.item>
            </x-boson::dropdown.menu>
        </x-boson::dropdown>
    </x-boson::breadcrumbs.item>
--}}

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('nav')
        ->base('breadcrumbs')
        ->attribute('aria-label', 'Breadcrumb');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    <ol class="breadcrumbs-list">
        {{ $slot }}
    </ol>
</{{ $el->getElement() }}>
