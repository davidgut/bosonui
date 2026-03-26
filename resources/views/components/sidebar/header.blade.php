{{--
@description Sidebar header. Wraps brand and collapse button in a flex row.
@usage <x-boson::sidebar.header><x-boson::sidebar.brand name="Acme" /><x-boson::sidebar.collapse /></x-boson::sidebar.header>
--}}

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('sidebar-header');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
