{{-- 
@description Async form. Set action="/url" and method="POST|PUT|DELETE". CSRF and method spoofing automatic. On 422, errors display in matching error components. Submit button disables during submission.
@usage <x-boson::form action="/users" method="POST"><x-boson::input name="email" label="Email" /><x-boson::button type="submit">Submit</x-boson::button></x-boson::form>
--}}

@props([
    'action',
    'method' => 'POST',
])
@php
    use DavidGut\Boson\Boson;

    $httpMethod = strtoupper($method);
    $needsSpoof = ! in_array($httpMethod, ['GET', 'POST']);

    $el = Boson::element('form')
        ->data('boson-form', true)
        ->attribute('action', $action)
        ->method($method);
@endphp

<form {{ $attributes->merge($el->getMergeAttributes()) }}>
    @csrf
    @if ($needsSpoof)
        @method($method)
    @endif
    {{ $slot }}
</form>
