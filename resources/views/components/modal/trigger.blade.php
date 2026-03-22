{{-- 
@description Trigger to open a modal. Set name="modalName" matching the target modal's name. Wrap any clickable element.
@usage <x-boson::modal.trigger name="confirm"><x-boson::button>Open Modal</x-boson::button></x-boson::modal.trigger>
@usage <x-boson::modal.trigger name="settings"><span class="underline cursor-pointer">Settings</span></x-boson::modal.trigger>
--}}

@props([
    'name',
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->attribute('data-modal-trigger', $name)
        ->class('inline-block'); // Minimal styling wrapper
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
