@extends('layouts.app')

@section('content')
    <h1>{{ $schoolProject->title }}</h1>




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

    <li x-data="{ showDelete: false, showEdit: false }">
        <strong>{{ $schoolProject->title }}</strong> - {{ $schoolProject->author }} ({{ $schoolProject->creation_date }})

        <button @click="showDelete = true">Eliminar</button>
        <button @click="showEdit = true">Editar</button>

        <div x-cloak x-show="showDelete">
            <p>¿Seguro que deseas eliminar este proyecto?</p>
            <form action="{{ route('admin.school_project.destroy', $schoolProject->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit">Sí, eliminar</button>
                <button type="button" @click="showDelete = false">Cancelar</button>
            </form>
        </div>

        <div x-cloak x-show="showEdit">
            <form action="{{ route('admin.school_project.update', $schoolProject->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <label>Título:</label>
                <input type="text" name="title" value="{{ $schoolProject->title }}" required><br>

                <label>Autor:</label>
                <input type="text" name="author" value="{{ $schoolProject->author }}" required><br>

                <label>Fecha de creación:</label>
                <input type="date" name="creation_date" value="{{ $schoolProject->creation_date }}" required><br>

                <label>Descripción:</label>
                <textarea name="description" required>{{ $schoolProject->description }}</textarea><br>

                <label>Tags:</label>
                <input type="text" name="tags" value="{{ $schoolProject->tags }}"><br>


                <label>Categoría general:</label>
                <input type="text" name="general_category" value="{{ $schoolProject->general_category }}"><br>

                <label class="block text-sm">Enlace (opcional)</label>
                <input type="url" name="link" class="w-full border rounded px-3 py-2 mb-2"
                    value="{{ old('link', $schoolProject->link) }}">

                <label class="block text-sm">Imagen destacada</label>
                <input type="file" name="image" accept="image/*" class="mb-2">

                <label class="block text-sm">Archivos adicionales</label>
                <input type="file" name="files[]" multiple class="mb-4">

                <button type="submit">Guardar cambios</button>
                <button type="button" @click="showEdit = false">Cancelar</button>
            </form>
        </div>
    </li>
    <p class="text-sm text-gray-500">👁️ {{ $schoolProject->views }} visitas</p>
    <h3>Valorar este proyecto</h3>
    <p>Calificación actual:
        {{ $schoolProject->averageRating() ? number_format($schoolProject->averageRating(), 1) : 'Sin calificaciones' }}
    </p>

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