{{-- 
@description Toast notifications. Place <x-boson::toast /> once in layout. Use Toast::success("msg") in PHP or BosonToast.success("msg") in JS. Set position="top-center|bottom-end|...".
@positions bottom-end, bottom-center, bottom-start, top-end, top-center, top-start
@variants success, warning, danger
@usage Toast::success("Saved!") or BosonToast.danger({ heading: "Error", text: "Failed" })
--}}

@props([
    'position' => 'bottom-end',
])
@php
    $toasts = session('boson_toasts', []);
@endphp

<div 
    class="toast-container" 
    data-position="{{ $position }}"
    data-boson-toast-container
>
    @foreach ($toasts as $toast)
        <x-boson::toast.item 
            :variant="$toast['variant'] ?? null" 
            :heading="$toast['heading'] ?? null"
            :text="$toast['text']"
            :duration="$toast['duration'] ?? null"
        />
    @endforeach
</div>

