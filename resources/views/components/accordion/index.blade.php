{{--
@description Accordion container. Wrap items in accordion.item, each with accordion.heading and accordion.content. Use exclusive to allow only one open at a time, transition for smooth animations.
@usage <x-boson::accordion><x-boson::accordion.item><x-boson::accordion.heading>Title</x-boson::accordion.heading><x-boson::accordion.content>Body</x-boson::accordion.content></x-boson::accordion.item></x-boson::accordion>
--}}

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('accordion')
        ->data('controller', 'accordion')
        ->when($attributes->get('exclusive'), 'data', 'accordion-exclusive', 'true')
        ->when($attributes->get('transition'), 'data', 'accordion-transition', 'true');
@endphp

<{{ $el->getElement() }} {{ $attributes->except(['exclusive', 'transition'])->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
