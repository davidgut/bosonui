{{--
@description Navlist group with optional heading. Use expandable for collapsible groups with chevron toggle. Set icon for heading icon.
@usage <x-boson::navlist.group expandable heading="Account"><x-boson::navlist.item href="#">Profile</x-boson::navlist.item></x-boson::navlist.group>
--}}

@props([
    'heading' => null,
    'expandable' => false,
    'icon' => null,
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('navlist-group')
        ->when($expandable, 'data', 'navlist-group', 'expandable')
        ->when($expandable, 'data', 'expanded', 'true');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    @if ($heading)
        @if ($expandable)
            <button type="button" class="navlist-group-heading navlist-group-heading-expandable" data-navlist-group-target="trigger">
                @if ($icon)
                    <x-boson::icon :name="$icon" variant="outline" class="navlist-item-icon" />
                @endif
                <span>{{ $heading }}</span>
                <x-boson::icon name="chevron-down" variant="outline" class="navlist-group-chevron" />
            </button>
        @else
            <div class="navlist-group-heading">
                @if ($icon)
                    <x-boson::icon :name="$icon" variant="outline" class="navlist-item-icon" />
                @endif
                <span>{{ $heading }}</span>
            </div>
        @endif
    @endif

    <div class="navlist-group-items">
        {{ $slot }}
    </div>
</{{ $el->getElement() }}>
