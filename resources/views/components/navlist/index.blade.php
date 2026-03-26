{{--
@description Vertical navigation list. Standalone component usable inside sidebar, cards, or any layout. Contains navlist.item and navlist.group children.
@usage
<x-boson::navlist>
    <x-boson::navlist.item icon="home" href="/" current>Home</x-boson::navlist.item>
    <x-boson::navlist.item icon="inbox" badge="12" href="/inbox">Inbox</x-boson::navlist.item>
    <x-boson::navlist.group expandable heading="Account">
        <x-boson::navlist.item href="#">Profile</x-boson::navlist.item>
        <x-boson::navlist.item href="#">Settings</x-boson::navlist.item>
    </x-boson::navlist.group>
</x-boson::navlist>
--}}

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('nav')
        ->base('navlist')
        ->data('controller', 'navlist');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
