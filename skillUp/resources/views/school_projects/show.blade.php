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

    <h2>Arcvhivos destacados------------</h2>

    @if ($schoolProject->images && $schoolProject->images->count())
        <div style="margin-bottom: 1.5rem;">
            <strong>Archivos del proyecto:</strong>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                @foreach ($schoolProject->images as $img)
                    @php
                        $extension = pathinfo($img->path, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp

                    <div style="flex: 1 0 120px;">
                        @if ($isImage)
                            <img src="{{ asset('storage/' . $img->path) }}" alt="Imagen del proyecto"
                                style="width: 100%; max-width: 200px; border-radius: 8px; object-fit: cover;">
                        @else
                            <a href="{{ asset('storage/' . $img->path) }}" download
                                class="block bg-gray-100 p-3 rounded shadow text-sm text-center hover:bg-gray-200">
                                📄 Descargar archivo ({{ $extension }})
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <p class="flex items-center justify-center gap-1"><x-icon name="graphic" class="w-4 h-auto" />{{ $schoolProject->views }}</p>
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

    <p><a href="{{ route('projects.index') }}" class="mt-3 px-2 py-2 bg-themeBlue text-white hover:bg-themeHoverBlue flex items-center gap-2 w-max rounded transition duration-200 ease-in-out transform hover:scale-101">
            <x-icon name="arrow-left" class="w-5 h-auto" /> Volver</a></p>

    @include('comments.comment_section', ['commentable' => $schoolProject, 'type' => 'school-project'])
@endsection