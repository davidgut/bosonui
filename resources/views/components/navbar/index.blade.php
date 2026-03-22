{{-- 
@description Navigation bar. Contains navbar.item children with href and optional icon. Current page auto-detected or set current explicitly.
@usage <x-boson::navbar><x-boson::navbar.item href="/dashboard" icon="home">Dashboard</x-boson::navbar.item></x-boson::navbar>
--}}

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('nav')
        ->base('navbar');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
