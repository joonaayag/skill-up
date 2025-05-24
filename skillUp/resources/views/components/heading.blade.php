@props([
    'level' => 'h2',
    'class' => '',
])

@php
    $defaultClasses = match($level) {
        'h1' => 'text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight',
        'h2' => 'text-xl sm:text-2xl md:text-3xl font-bold',
        'h3' => 'text-lg sm:text-xl md:text-2xl font-semibold',
        'h4' => 'text-2md sm:text-lg md:text-xl font-medium',
        default => 'text-lg font-normal',
    };
@endphp

<{{ $level }} {{ $attributes->merge(['class' => "$defaultClasses $class"]) }}>
    {{ $slot }}
</{{ $level }}>
