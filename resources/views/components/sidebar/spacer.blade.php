{{--
@description Sidebar spacer. Pushes subsequent content to the bottom of the sidebar using flex-grow.
@usage <x-boson::sidebar.spacer />
--}}

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('sidebar-spacer');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}></{{ $el->getElement() }}>
