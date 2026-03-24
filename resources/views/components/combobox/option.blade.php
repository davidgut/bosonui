{{-- 
@description Option element for the combobox component.
@prefixes icon (icon:variant, icon:class), check (check:icon, check:variant, check:class)
@defaults icon variant is "micro" (inherits from icon component), override with icon:variant="outline"
@defaults check icon is "check", override with check:icon="check-circle"
@usage <x-boson::combobox.option value="1" icon="user">John Doe</x-boson::combobox.option>
--}}

@php
    use DavidGut\Boson\Boson;

    $iconAttrs = Boson::extract($attributes, 'icon');
    $checkAttrs = Boson::extract($attributes, 'check');
    $optionAttrs = Boson::except($attributes, ['icon', 'check']);

    $icon = $optionAttrs->get('icon');
    $value = $optionAttrs->get('value');
    $checkIcon = $checkAttrs->get('icon', 'check');

    $el = Boson::element()
        ->base('combobox-option')
        ->role('option')
        ->data('combobox-target', 'option')
        ->data('value', $value)
        ->data('label', trim($slot->toHtml()))
        ->tabindex('-1');
@endphp

<{{ $el->getElement() }} {{ $optionAttrs->except(['icon', 'value'])->merge($el->getMergeAttributes()) }}>
    @if ($icon)
        <x-boson::icon :name="$icon" {{ $iconAttrs->merge(['class' => 'combobox-option-icon']) }} />
    @endif
    
    <span class="flex-1">{{ $slot }}</span>
    
    <x-boson::icon :name="$checkIcon" {{ $checkAttrs->except('icon')->merge(['class' => 'combobox-option-check']) }} />
</{{ $el->getElement() }}>
