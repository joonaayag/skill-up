@extends('layouts.app')

@section('title', 'Detalles del proyecto')

@section('content')
    <h1>{{ $project->name }}</h1>

    {{-- Imagen destacada (si existe) --}}
    @if($project->image)
        <img src="{{ asset('storage/' . $project->image) }}" alt="Imagen del proyecto" style="max-width: 300px; margin-bottom: 1rem;">
    @endif

    {{-- Enlace externo (opcional) --}}
    @if($project->link)
        <p><strong>Enlace:</strong> <a href="{{ $project->link }}" target="_blank">{{ $project->link }}</a></p>
    @endif

    <p><strong>Tags:</strong> {{ $project->tags ?? '-' }}</p>
    <p><strong>Categoría general:</strong> {{ $project->general_category ?? '-' }}</p>
    <p><strong>Fecha de creación:</strong> {{ $project->creation_date }}</p>

    <p><strong>Descripción:</strong><br>{{ $project->description }}</p>

    {{-- Archivos adicionales (si los manejas como imágenes o rutas) --}}
    @if(isset($project->files) && is_array($project->files))
        <div>
            <strong>Archivos adjuntos:</strong>
            <ul>
                @foreach ($project->files as $file)
                    <li><a href="{{ asset('storage/' . $file) }}" target="_blank">Descargar archivo</a></li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ url()->previous() }}">← Volver</a>
@endsection
