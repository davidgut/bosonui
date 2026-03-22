{{-- 
@description Validation error. Set name="fieldName" matching the input. Auto-shows Laravel validation errors. Hidden when no errors.
@usage <x-boson::error name="email" />
--}}

@props([
    'name',
])
@php
    use DavidGut\Boson\Boson;

    $hasError = $errors->has($name);

    $el = Boson::element('p')
        ->base('error')
        ->when(! $hasError, 'mod', 'hidden')
        ->data('boson-error', $name);
@endphp

<p {{ $attributes->merge($el->getMergeAttributes()) }}>
    @error($name)
        {{ $message }}
    @enderror
</p>
