{{-- 
@description Divider line. Add text="or" for centered label. Use vertical with explicit height for vertical dividers. Add subtle for lighter style.
@usage <x-boson::separator text="or" /> or <x-boson::separator vertical class="h-6" />
--}}

@props([
    'text' => null,
    'vertical' => false,
    'subtle' => false,
])
@php
    use DavidGut\Boson\Boson;

    $el = Boson::element()
        ->base('separator')
        ->when($vertical, 'mod', 'vertical')
        ->when(! $vertical, 'mod', 'horizontal')
        ->when($subtle, 'mod', 'subtle');
@endphp
<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    <div class="separator-line"></div>
    @if ($text && !$vertical)        <span class="separator-text">{{ $text }}</span>
        <div class="separator-line"></div>
    @endif
</{{ $el->getElement() }}>
