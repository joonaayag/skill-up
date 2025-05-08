@props([
    'type' => 'submit',
    'variant' => 'primary', // opciones: primary, secondary, danger, etc.
    'class' => '',
])

@php
    $baseClasses = 'inline-flex items-center px-4 py-2 rounded-md font-semibold text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';

    $variantClasses = match($variant) {
        'primary' => 'bg-themeGrape text-white hover:bg-themeGrape/90 focus:ring-themeGrape/50 cursor-pointer',
        'secondary' => 'bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-400',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        default => '',
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "$baseClasses $variantClasses $class"]) }}>
    {{ $slot }}
</button>
