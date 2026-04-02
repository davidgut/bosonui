{{--
@description Tab list row. Wraps tab buttons. Set variant="segmented" or variant="pills" for alternate styles. Use size="sm" for smaller segmented tabs. Add class="px-4" for padded edges.
@variants segmented, pills
@sizes sm
@usage <x-boson::tabs.list><x-boson::tabs.tab name="profile">Profile</x-boson::tabs.tab></x-boson::tabs.list>
--}}

@php
    use DavidGut\Boson\Boson;

    $variant = $attributes->get('variant');
    $size = $attributes->get('size');

    $el = Boson::element()
        ->base('tabs-list')
        ->mod($variant)
        ->when($variant === 'segmented' && $size, 'mod', $variant . '-' . $size)
        ->role('tablist');
@endphp

<{{ $el->getElement() }} {{ $attributes->except(['variant', 'size'])->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
