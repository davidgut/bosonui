{{-- 
@description Textarea. Use label="Label" and description="Helper" props. Shows validation errors when name is set. Standard attrs like rows, placeholder work.
@usage <x-boson::textarea name="bio" label="Bio" rows="4" placeholder="Tell us about yourself..." />
--}}

@props([
    'label' => null,
    'description' => null
])

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('textarea')->base('textarea');
@endphp

<div class="w-full">
    @if ($label)
        <x-boson::label :for="$attributes->get('id')" class="mb-2 block">
            {{ $label }}
        </x-boson::label>
    @endif

    <{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>{{ $slot }}</{{ $el->getElement() }}>

    @if ($description)
        <x-boson::description class="mt-1">
            {{ $description }}
        </x-boson::description>
    @endif

    @if ($attributes->has('name'))
        <x-boson::error :name="$attributes->get('name')" class="mt-1" />
    @endif
</div>
 