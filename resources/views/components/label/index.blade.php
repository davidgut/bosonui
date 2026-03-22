{{-- 
@description Form label. Set for="inputId" to link. Automatically dims when sibling input is disabled.
@usage <x-boson::label for="email">Email Address</x-boson::label>
--}}

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('label')->base('label');
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>