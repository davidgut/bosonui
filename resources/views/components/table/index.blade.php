{{-- 
@description Data table. Use table.head/table.body with table.row containing table.header or table.data cells. Add striped, hoverable, bordered props. Wrap in table.container for horizontal scroll.
@sizes sm, md, lg
@usage <x-boson::table striped><x-boson::table.head><x-boson::table.row><x-boson::table.header>Name</x-boson::table.header></x-boson::table.row></x-boson::table.head><x-boson::table.body>...</x-boson::table.body></x-boson::table>
--}}

@props([
    'striped' => false,
    'hoverable' => false,
    'bordered' => false,
    'size' => 'md',
])

@php
    use DavidGut\Boson\Boson;

    $el = Boson::element('table')
        ->base('table')
        ->when($striped, 'mod', 'striped')
        ->when($hoverable, 'mod', 'hover')
        ->when($bordered, 'mod', 'bordered')
        ->when($size !== 'md', 'mod', $size);
@endphp

<{{ $el->getElement() }} {{ $attributes->merge($el->getMergeAttributes()) }}>
    {{ $slot }}
</{{ $el->getElement() }}>
