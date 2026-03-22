{{-- 
@description Checkbox. Set name, value, label, description. Use checkbox.group for grouping with variant="cards|pills|buttons|tiles". For arrays use name="items[]".
@usage <x-boson::checkbox.group label="Preferences" variant="pills"><x-boson::checkbox name="prefs[]" value="email" label="Email" /><x-boson::checkbox name="prefs[]" value="sms" label="SMS" /></x-boson::checkbox.group>
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
        ->base('checkbox');

    // Determine if we have custom slot content
    $hasCustomContent = ! $slot->isEmpty();
@endphp

<label class="checkbox">
    <input
        type="checkbox"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        {{ $attributes->merge($el->getMergeAttributes()) }}
    >

    @if ($hasCustomContent)
        {{-- Custom slot content for composable usage --}}
        {{ $slot }}
    @else
        {{-- Default rendering with indicator, label, description --}}
        <x-boson::checkbox.indicator />

        @if ($image)
            <x-boson::img :src="$image" class="checkbox-image" />
        @elseif ($emoji)
            <span class="checkbox-emoji">{{ $emoji }}</span>
        @elseif ($icon)
            <x-boson::icon :name="$icon" variant="micro" class="checkbox-icon" />
        @endif

        @if ($label || $description)
            <div class="checkbox-content">
                @if ($label)
                    <span class="checkbox-label">{{ $label }}</span>
                @endif
                @if ($description)
                    <span class="checkbox-description">{{ $description }}</span>
                @endif
            </div>
        @endif
    @endif
</label>
