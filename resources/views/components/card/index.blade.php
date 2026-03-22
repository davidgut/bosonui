{{-- 
@description Card container for related content such as forms, alerts, or data lists. Set size="sm" for compact content like notifications or brief summaries.
@sizes sm
@usage <x-boson::card>Content</x-boson::card>
@usage <x-boson::card size="sm">Compact content</x-boson::card>
--}}

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('card')
        ->mod($attributes->get('size'));
@endphp

<{{ $el->getElement() }} {{ $attributes->except(['size'])->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
