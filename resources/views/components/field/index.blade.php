{{-- 
@description Form field wrapper. Groups label, input, description, error with consistent spacing.
@usage <x-boson::field><x-boson::label for="name">Name</x-boson::label><x-boson::input id="name" name="name" /><x-boson::error name="name" /></x-boson::field>
--}}

<div {{ $attributes->merge(['class' => 'field']) }}>
    {{ $slot }}
</div>