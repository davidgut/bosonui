{{--
@description Profile button with avatar and optional name. Wraps the avatar component. Commonly used as a dropdown trigger. Pass avatar:* to forward attributes to the inner avatar.
@props avatar, name, size (xs|sm|md|lg|xl), shape (rounded|circle), chevron (default: true), icon:trailing
@prefixes avatar, icon
@usage
<x-boson::profile avatar="/user.png" name="Jane Doe" />
<x-boson::profile avatar="/user.png" shape="circle" :chevron="false" />
<x-boson::profile avatar="/user.png" size="lg" avatar:color="auto" name="John Smith" />
<x-boson::dropdown>
    <x-boson::profile avatar="/user.png" name="Jane Doe" />
    <x-boson::dropdown.menu>...</x-boson::dropdown.menu>
</x-boson::dropdown>
--}}

@php
    use DavidGut\Boson\Boson;

    $avatarAttrs = Boson::extract($attributes, 'avatar');
    $iconAttrs = Boson::extract($attributes, 'icon');
    $profileAttrs = Boson::except($attributes, ['avatar', 'icon']);

    $avatar = $profileAttrs->get('avatar');
    $name = $profileAttrs->get('name');
    $chevron = $profileAttrs->get('chevron', true);
    $size = $profileAttrs->get('size', 'sm');
    $shape = $profileAttrs->get('shape', 'rounded');
    $trailingIcon = $iconAttrs->get('trailing', 'chevron-down');


    $el = Boson::element(null, 'button')
        ->base('profile')
        ->size($size)
        ->mod($shape)
        ->attribute('type', 'button')
        ->data('dropdown-target', 'trigger');
@endphp

<{{ $el->getElement() }} {{ $profileAttrs->except(['avatar', 'name', 'chevron', 'size', 'shape'])->merge($el->getMergeAttributes()) }}>
    <x-boson::avatar :src="$avatar" :name="$name" :attributes="$avatarAttrs->merge(['size' => $size, 'shape' => $shape])" />

    @if ($name)
        <span class="profile-name">{{ $name }}</span>
    @endif

    @if ($chevron)
        <x-boson::icon :name="$trailingIcon" variant="outline" class="profile-chevron" />
    @endif
</{{ $el->getElement() }}>
