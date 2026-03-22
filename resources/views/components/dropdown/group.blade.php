@props([
    'heading' => null,
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('dropdown-group')
        ->attribute('role', 'group');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
@if ($heading)
    <div class="dropdown-group-heading">
        {{ $heading }}
    </div>
@endif

    {{ $slot }}
</{{ $el->getElement() }}>
