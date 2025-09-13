@props(['active' => false])

@php
$classes = $active
            ? 'text-white bg-red-600 hover:bg-red-500 rounded-lg mb-1'
            : 'text-gray-700 bg-gray-100 rounded-lg md:bg-gray-50 mb-1 hover:bg-red-50 hover:text-red-700 transform hover:scale-102 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>