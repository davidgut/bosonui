{{-- 
@description Form with automatic CSRF and method spoofing. By default submits via fetch (async). Set :async="false" for standard browser submission.

When async, response handling is automatic based on what the controller returns:

Redirect — return redirect('/dashboard') → the page navigates normally.
JSON with data — return response()->json(['data' => $model]) → all matching [data-field] elements on the page update in-place, the form resets, and the parent modal closes. No page reload. Nested data is supported via dot-notation: { data: { team: { name: "Acme" } } } matches [data-field="team.name"].
Validation (422) — Laravel's automatic 422 response with { errors: { email: ["The email is required."] } } populates matching <x-boson::error name="email" /> components inside the form.

@usage <x-boson::form action="/users" method="POST">...</x-boson::form>
@usage <x-boson::form :async="false" action="/login" method="POST">...</x-boson::form>
--}}

@props([
    'action',
    'method' => 'POST',
    'async' => true,
])
@php
    use DavidGut\Boson\Boson;

    $httpMethod = strtoupper($method);
    $needsSpoof = ! in_array($httpMethod, ['GET', 'POST']);

    $el = Boson::element('form')
        ->when($async, 'data', 'controller', 'form')
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
