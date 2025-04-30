@extends('layouts.app')

@section('title', 'Proyectos')

@section('content')
    <h1>Proyectos</h1>

    <ul>
        @forelse ($projects as $project)
            <li>
                <strong>{{ $project->name }}</strong><br>
                <span><em>Categoría:</em> {{ $project->category }} | <em>Fecha:</em> {{ $project->creation_date }}</span><br>
                <p>{{ $project->description }}</p>

                @php
                    $favorite = auth()->user()->favorites()
                        ->where('type', 'proyecto')
                        ->where('reference_id', $project->id)
                        ->first();
                @endphp

                @if ($favorite)
                    <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">❌ Quitar de favoritos</button>
                    </form>
                @else
                    <form action="{{ route('favorites.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="proyecto">
                        <input type="hidden" name="reference_id" value="{{ $project->id }}">
                        <button type="submit">❤️ Añadir a favoritos</button>
                    </form>
                @endif


                <hr>

            </li>
        @empty
            <p>No hay proyectos disponibles.</p>
        @endforelse
        <div x-data="{ showModal: false }">
            <button @click="showModal = true">Crear proyecto</button>

            {{-- Modal de creación --}}
            <div x-show="showModal" style="margin-top: 1rem; border: 1px solid #ccc; padding: 1rem;">
                <h2>Nuevo proyecto</h2>

                <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <label>Título:</label>
                    <input type="text" name="name" required><br>

                    <label>Descripción:</label>
                    <textarea name="description" required></textarea><br>

                    <label>Etiquetas (tags):</label>
                    <input type="text" name="tags" required><br>

                    <label>Categoría general:</label>
                    <select name="sector_category" required>
                        <option value="Tecnología y desarrollo">Tecnología y desarrollo</option>
                        <option value="Diseño y comunicación">Diseño y comunicación</option>
                        <option value="Administración y negocio">Administración y negocio</option>
                        <option value="Comunicación">Comunicación</option>
                        <option value="Educación">Educación</option>
                        <option value="Ciencia y salud">Ciencia y salud</option>
                        <option value="Industria">Industria</option>
                        <option value="Otro">Otro</option>
                    </select><br>

                    <label>Fecha de creación:</label>
                    <input type="date" name="creation_date" required><br>

                    <label>Enlace (opcional):</label>
                    <input type="url" name="link"><br>

                    <label>Imagen destacada:</label>
                    <input type="file" name="image" accept="image/*"><br>

                    <label>Archivos adicionales:</label>
                    <input type="file" name="files[]" multiple><br>

                    <button type="submit">Guardar</button>
                    <button type="button" @click="showModal = false">Cancelar</button>
                </form>
            </div>
        </div>

    </ul>
@endsection