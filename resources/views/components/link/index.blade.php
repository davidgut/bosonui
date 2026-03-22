{{-- 
@description Styled link. Add external for target="_blank" with noopener noreferrer. Override with referrer:policy="origin".
@usage <x-boson::link href="https://example.com" external>Visit Site</x-boson::link>
--}}

@php
    use DavidGut\Boson\Boson;

    $href = $attributes->get('href', '#');
    $external = $attributes->get('external', false);
    $referrerPolicy = $attributes->get('referrer:policy');

    $el = Boson::element('a')
        ->base('link')
        ->href($href)
        ->only($external)
            ->external()
            ->referrerPolicy($referrerPolicy)
        ->end();
@endphp

<a {{ $attributes->except(['href', 'external', 'referrer:policy'])->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</a>
