@props([
    'label' => null,
    'variant' => null,
    'size' => null,
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('checkbox-group')
        ->attribute('data-variant', $variant)
        ->attribute('data-size', $size);

    // Extract classes that should go to the items container
    $itemsClass = $attributes->get('class', '');
@endphp

<{{ $el->getElement() }} {{ $attributes->except('class')->merge($el->getMergeAttributes()) }}>
    @if ($label)
        <div class="checkbox-group-label">{{ $label }}</div>
    @endif

    <div class="checkbox-group-items {{ $itemsClass }}">
        {{ $slot }}
    </div>
</{{ $el->getElement() }}>
