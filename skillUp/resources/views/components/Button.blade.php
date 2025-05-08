@props([
    'href' => null,
    'type' => 'submit',
    'variant' => 'grape',
    'class' => '',
])

@php
    $base = 'inline-flex items-center px-4 py-2 rounded-md font-semibold text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';

    $variants = [
        'grape' => 'text-white bg-themeGrape hover:opacity-90 focus:ring-themeGrape',
        'blue' => 'text-white bg-themeBlue hover:opacity-90 focus:ring-themeBlue',
        'secondary' => 'bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-400',
    ];

    $variantClasses = $variants[$variant] ?? $variants['grape'];
    $finalClasses = "$base $variantClasses $class";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $finalClasses]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $finalClasses]) }}>
        {{ $slot }}
    </button>
@endif
