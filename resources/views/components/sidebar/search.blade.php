{{--
@description Sidebar search input with magnifying glass icon. Pass placeholder and other input attributes directly.
@usage <x-boson::sidebar.search placeholder="Search..." />
--}}

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('sidebar-search');
@endphp

<{{ $el->getElement() }} {{ $attributes->only('class')->merge($el->getMergeAttributes()) }}>
    <x-boson::icon name="magnifying-glass" variant="outline" class="sidebar-search-icon" />
    <input type="search" {{ $attributes->except('class')->merge(['class' => 'sidebar-search-input']) }} />
</{{ $el->getElement() }}>
