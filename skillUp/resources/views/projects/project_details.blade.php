@extends('layouts.app')

@section('title', 'Detalles del proyecto')

@section('content')
    <h1>{{ $project->name }}</h1>

    {{-- Imagen destacada (si existe) --}}
    @if($project->image)
        <img src="{{ asset('storage/' . $project->image) }}" alt="Imagen del proyecto"
            style="max-width: 300px; margin-bottom: 1rem;">
    @endif

    {{-- Enlace externo (opcional) --}}
    @if($project->link)
        <p><strong>Enlace:</strong> <a href="{{ $project->link }}" target="_blank">{{ $project->link }}</a></p>
    @endif

    <p><strong>Tags:</strong> {{ $project->tags ?? '-' }}</p>
    <p><strong>Categoría general:</strong> {{ $project->general_category ?? '-' }}</p>
    <p><strong>Fecha de creación:</strong> {{ $project->creation_date }}</p>

    <p><strong>Descripción:</strong><br>{{ $project->description }}</p>
    <p><strong>Link:</strong> <a href="{{ $project->link ?? '#' }}">{{ $project->link ?? '-' }}</a></p>

    <h2>IMAGENES DESTACADAS------------</h2>

    @if ($project->images && $project->images->count())
        <div style="margin-bottom: 1.5rem;">
            <strong>Galería de imágenes:</strong>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                @foreach ($project->images as $img)
                    <div style="flex: 1 0 120px;">
                        <img src="{{ asset('storage/' . $img->path) }}" alt="Imagen del proyecto"
                            style="width: 100%; max-width: 200px; border-radius: 8px; object-fit: cover;">
                    </div>
                @endforeach
            </div>
        </div>
    @endif


    <a href="{{ url()->previous() }}">← Volver</a>
@endsection