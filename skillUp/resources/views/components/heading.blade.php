@props([
    'level' => 'h2',
    'class' => '',
])

@php
    $defaultClasses = match($level) {
        'h1' => 'text-4xl font-extrabold tracking-tight',
        'h2' => 'text-3xl font-bold',
        'h3' => 'text-2xl font-semibold',
        'h4' => 'text-xl font-medium',
        default => 'text-lg font-normal',
    };
@endphp

<{{ $level }} {{ $attributes->merge(['class' => "$defaultClasses $class"]) }}>
    {{ $slot }}
</{{ $level }}>
