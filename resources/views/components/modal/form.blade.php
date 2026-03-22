{{-- 
@description Modal containing a form. Set name="modalName", action="/url", and optional method="PUT|DELETE|PATCH" (defaults to POST). Combines modal and form components.
@usage <x-boson::modal.form name="create-user" action="/users">...</x-boson::modal.form>
@usage <x-boson::modal.form name="edit-user" action="/users/1" method="PUT">...</x-boson::modal.form>
--}}

@props([
    'name',
    'action',
    'method' => 'POST',
])

<x-boson::modal :name="$name">
    <x-boson::form :action="$action" :method="$method" {{ $attributes }}>
        {{ $slot }}
    </x-boson::form>
</x-boson::modal>
