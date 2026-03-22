{{-- 
@description Text input. Use label="Label" and description="Helper" props. Shows validation errors when name is set. Add icons with icon="name" and icon:trailing="name".
@prefixes root (wrapper), container (input+icons), label, error, description, icon, icon:trailing
@usage <x-boson::input name="email" label="Email" icon="envelope" placeholder="you@example.com" />
--}}

@php
    use DavidGut\Boson\Boson;

    $iconAttrs = Boson::extract($attributes, 'icon');
    $iconTrailingAttrs = Boson::extract($iconAttrs, 'trailing');
    $labelAttrs = Boson::extract($attributes, 'label');
    $errorAttrs = Boson::extract($attributes, 'error');
    $descriptionAttrs = Boson::extract($attributes, 'description');
    $rootAttrs = Boson::extract($attributes, 'root');
    $containerAttrs = Boson::extract($attributes, 'container');
    $inputAttrs = Boson::except($attributes, ['icon', 'label', 'error', 'description', 'root', 'container']);

    $icon = $inputAttrs->get('icon');
    $iconTrailing = $iconAttrs->get('trailing');
    $label = $inputAttrs->get('label');
    $description = $inputAttrs->get('description');

    $input = Boson::element('input')
        ->base('input')
        ->when($icon, 'mod', 'has-icon')
        ->when($iconTrailing, 'mod', 'has-icon-trailing');
@endphp

<div {{ $rootAttrs->class('w-full') }}>
    @if ($label)
        <x-boson::label :for="$inputAttrs->get('id')" :attributes="$labelAttrs->merge(['class' => 'mb-2 block'])">
            {{ $label }}
        </x-boson::label>
    @endif

    <div {{ $containerAttrs->class('relative') }}>
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <x-boson::icon :name="$icon" :attributes="$iconAttrs->except(['trailing'])->merge(['variant' => 'mini', 'class' => 'input-icon'])" />
            </div>
        @endif

        <input {{ $inputAttrs->except(['icon', 'label', 'description'])->merge($input->getMergeAttributes()) }}>

        @if ($iconTrailing)
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <x-boson::icon :name="$iconTrailing" :attributes="$iconTrailingAttrs->merge(['variant' => 'mini', 'class' => 'input-icon'])" />
            </div>
        @endif
    </div>

    @if ($description)
        <x-boson::description :attributes="$descriptionAttrs->merge(['class' => 'mt-1'])">
            {{ $description }}
        </x-boson::description>
    @endif

    @if ($inputAttrs->has('name'))
        <x-boson::error :name="$inputAttrs->get('name')" :attributes="$errorAttrs->merge(['class' => 'mt-1'])" />
    @endif
</div>
