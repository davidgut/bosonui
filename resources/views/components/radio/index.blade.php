{{-- 
@description Radio button. Set name, value, label, description. Use radio.group for grouping with variant="cards|pills|buttons|tiles". Add icon, emoji, or image props for visual content.
@usage <x-boson::radio.group name="plan" label="Select plan" variant="cards"><x-boson::radio value="basic" label="Basic" /><x-boson::radio value="pro" label="Pro" /></x-boson::radio.group>
--}}

@props([
    'value' => null,
    'checked' => false,
    'label' => null,
    'description' => null,
    'icon' => null,
    'image' => null,
    'emoji' => null,
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('input')
        ->base('radio');

    // Determine if we have custom slot content
    $hasCustomContent = ! $slot->isEmpty();
@endphp

<label class="radio">
    <input
        type="radio"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        {{ $attributes->merge($el->getMergeAttributes()) }}
    >

    @if ($hasCustomContent)
        {{-- Custom slot content for composable usage --}}
        {{ $slot }}
    @else
        {{-- Default rendering with indicator, label, description --}}
        <x-boson::radio.indicator />

        @if ($image)
            <x-boson::img :src="$image" class="radio-image" />
        @elseif ($emoji)
            <span class="radio-emoji">{{ $emoji }}</span>
        @elseif ($icon)
            <x-boson::icon :name="$icon" variant="micro" class="radio-icon" />
        @endif

        @if ($label || $description)
            <div class="radio-content">
                @if ($label)
                    <span class="radio-label">{{ $label }}</span>
                @endif
                @if ($description)
                    <span class="radio-description">{{ $description }}</span>
                @endif
            </div>
        @endif
    @endif
</label>
