@extends('layouts.app')

@section('title', $schoolProject->name)

@section('content')
    <h1>{{ $schoolProject->name }}</h1>

    @if ($schoolProject->image)
        <img src="{{ asset('storage/' . $schoolProject->image) }}" alt="Imagen del proyecto" style="max-width: 400px;">
    @endif

    <p><strong>Descripción:</strong><br>{{ $schoolProject->description }}</p>

    <p><strong>Etiquetas:</strong> {{ $schoolProject->tags }}</p>
    <p><strong>Categoría:</strong> {{ $schoolProject->sector_category }}</p>
    <p><strong>Fecha de creación:</strong> {{ $schoolProject->creation_date }}</p>

    @if ($schoolProject->link)
        <p><strong>Enlace:</strong> <a href="{{ $schoolProject->link }}" target="_blank">{{ $schoolProject->link }}</a></p>
    @endif

    <h3>Valorar este proyecto</h3>
    <p>Calificación actual:
        {{ $schoolProject->averageRating() ? number_format($schoolProject->averageRating(), 1) : 'Sin calificaciones' }}</p>

    @auth
        <form action="{{ route('school-projects.rate', $schoolProject->id) }}" method="POST">
            @csrf
            <div class="rating-stars">
                @for ($i = 1; $i <= 5; $i++)
                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ $schoolProject->getRatingByUser(auth()->id()) && $schoolProject->getRatingByUser(auth()->id())->rating == $i ? 'checked' : '' }}>
                    <label for="star{{ $i }}">★</label>
                @endfor
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $schoolProject->getRatingByUser(auth()->id()) ? 'Actualizar valoración' : 'Enviar valoración' }}
            </button>
        </form>
    @endauth

    <p><a href="{{ route('projects.index') }}">← Volver</a></p>

    @include('comments.comment_section', ['commentable' => $schoolProject, 'type' => 'school-project'])
@endsection