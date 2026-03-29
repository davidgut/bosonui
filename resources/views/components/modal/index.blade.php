{{-- 
@description Modal dialog. Set name="modalName" and open with modal.trigger name="modalName". Close via X button, Escape key, or backdrop click.
@usage <x-boson::modal name="confirm">Content here</x-boson::modal>
@usage <x-boson::modal.trigger name="confirm"><x-boson::button>Open</x-boson::button></x-boson::modal.trigger>
--}}

@props([
    'name',
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('modal-content')
        ->attribute('role', 'document');
@endphp

<div 
    class="modal-overlay" 
    data-modal 
    data-name="{{ $name }}" 
    role="dialog" 
    aria-modal="true"
    tabindex="-1"
>
    <div class="fixed inset-0" data-modal-close></div> {{-- Backdrop click target --}}
    
    <{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
        <x-boson::button variant="ghost" size="sm" square="true" class="modal-close" data-modal-close>
            <x-boson::icon name="x-mark" variant="mini" />
        </x-boson::button>
        {{ $slot }}
    </{{ $el->getElement() }}>
</div>
