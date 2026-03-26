{{--
@description Sidebar brand link with logo image and name. Use inside sidebar.header.
@usage <x-boson::sidebar.brand href="/" logo="/logo.png" name="Acme Inc." />
--}}

@php
    use DavidGut\Boson\Boson;

    $logo = $attributes->get('logo');
    $name = $attributes->get('name');
    $href = $attributes->get('href', '#');

    $el = Boson::element(null, 'a')
        ->base('sidebar-brand')
        ->href($href);
@endphp

<{{ $el->getElement() }} {{ $attributes->except(['logo', 'name', 'href'])->merge($el->getMergeAttributes()) }}>
    @if ($logo)
        <img src="{{ $logo }}" alt="{{ $name ?? '' }}" class="sidebar-brand-logo" />
    @endif

    @if ($name)
        <span class="sidebar-brand-name">{{ $name }}</span>
    @endif
</{{ $el->getElement() }}>
