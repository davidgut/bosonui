@props([
    'variant' => null,
    'heading' => null,
    'text' => null,
    'duration' => null,
])
@php
    $icons = [
        'success' => 'check-circle',
        'warning' => 'exclamation-triangle',
        'danger' => 'x-circle',
    ];
    $icon = $variant ? ($icons[$variant] ?? null) : null;
@endphp

<div 
    class="toast" 
    @if($variant) data-variant="{{ $variant }}" @endif
    @if($duration !== null) data-duration="{{ $duration }}" @endif
    data-boson-toast
    role="alert"
>
    <div class="toast-body">
        @if ($icon)
            <x-boson::icon :name="$icon" variant="micro" class="toast-icon" />
        @endif
        
        <div class="toast-content">
            @if ($heading)
                <p class="toast-heading">{{ $heading }}</p>
            @endif
            <p class="toast-text">{{ $text ?? $slot }}</p>
        </div>
    </div>
    
    <div class="toast-actions">
        <button type="button" class="btn btn-ghost btn-sm btn-square" data-toast-close>
            <x-boson::icon name="x-mark" variant="mini" />
        </button>
    </div>
</div>