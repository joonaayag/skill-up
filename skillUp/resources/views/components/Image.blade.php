@props([
    'src',
    'alt' => '',
    'class' => ''
])

<img src="{{ $src }}" alt="{{ $alt }}" loading="lazy" {{ $attributes->merge(['class' => 'rounded ' . $class]) }}>
