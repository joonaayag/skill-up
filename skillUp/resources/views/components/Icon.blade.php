@props([
    'name',
    'class' => '',
])

@php
    $path = public_path("icons/{$name}.svg");
@endphp

@if (file_exists($path))
    @php
        $svg = file_get_contents($path);

        // Eliminar clase existente, si la hay
        $svg = preg_replace('/class="[^"]*"/', '', $svg);

        // Inyectar clase personalizada
        $svg = preg_replace(
            '/<svg([^>]+)>/',
            '<svg$1 class="' . $class . '">',
            $svg
        );
    @endphp

    {!! $svg !!}
@else
    <span class="text-red-500">[Icono "{{ $name }}" no encontrado]</span>
@endif
