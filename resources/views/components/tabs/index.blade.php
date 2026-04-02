{{--
@description Tabs container. Wraps tabs.list and tabs.panel components. The first tab activates by default.
@usage <x-boson::tabs><x-boson::tabs.list><x-boson::tabs.tab name="profile">Profile</x-boson::tabs.tab></x-boson::tabs.list><x-boson::tabs.panel name="profile">...</x-boson::tabs.panel></x-boson::tabs>
--}}

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('tabs')
        ->data('controller', 'tab');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
