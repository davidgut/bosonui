{{-- 
@description Styled link. Add external for target="_blank" with noopener noreferrer. Override with referrer:policy="origin". Use turbo:* prefix for Turbo attributes (e.g. turbo:frame, turbo:action, turbo:prefetch).
@usage <x-boson::link href="https://example.com" external>Visit Site</x-boson::link>
@usage <x-boson::link href="/users" turbo:frame="main" turbo:action="replace">Users</x-boson::link>
--}}

@php
    use DavidGut\Boson\Boson;

    $turboAttrs = Boson::extract($attributes, 'turbo');
    $linkAttrs = Boson::except($attributes, 'turbo');

    $href = $linkAttrs->get('href', '#');
    $external = $linkAttrs->get('external', false);
    $turbo = $linkAttrs->get('turbo', true);
    $referrerPolicy = $linkAttrs->get('referrer:policy');

    $el = Boson::element('a')
        ->base('link')
        ->href($href)
        ->when(! $turbo, 'data', 'turbo', 'false')
        ->turbo($turboAttrs)
        ->only($external)
            ->external()
            ->referrerPolicy($referrerPolicy)
        ->end();
@endphp

<a {{ $linkAttrs->except(['href', 'external', 'turbo', 'referrer:policy'])->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</a>
