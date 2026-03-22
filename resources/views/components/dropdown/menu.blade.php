@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('dropdown-menu')
        ->attribute('role', 'menu')
        ->attribute('data-dropdown-target', 'menu')
        ->attribute('aria-orientation', 'vertical')
        ->attribute('tabindex', '-1');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>