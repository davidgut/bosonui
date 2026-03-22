{{-- 
@description Helper text. Use inside field component below input for hints/instructions.
@usage <x-boson::description>Must be at least 8 characters.</x-boson::description>
--}}

<p {{ $attributes->merge(['class' => 'description']) }}>
    {{ $slot }}
</p>