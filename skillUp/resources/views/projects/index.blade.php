@extends('layouts.app')

@section('title', 'Proyectos')

@section('content')
    <h1>Proyectos</h1>

    <form method="GET" action="{{ route('projects.index') }}" class="mb-6 space-y-2">
        <input type="text" name="name" placeholder="Título" value="{{ request('name') }}">
        <input type="text" name="description" placeholder="Descripción" value="{{ request('description') }}">
        <input type="text" name="author" placeholder="Autor" value="{{ request('author') }}">

        <select name="category">
            <option value="">-- Categoría --</option>
            <option value="Tecnología y desarrollo">Tecnología y desarrollo</option>
            <option value="Diseño y comunicación">Diseño y comunicación</option>
            <option value="Administración y negocio">Administración y negocio</option>
            <option value="Comunicación">Comunicación</option>
            <option value="Educación">Educación</option>
            <option value="Ciencia y salud">Ciencia y salud</option>
            <option value="Industria">Industria</option>
            <option value="Otro">Otro</option>
        </select>

        <select name="order">
            <option value="">-- Ordenar por --</option>
            <option value="name">Nombre</option>
            <option value="creation_date">Fecha</option>
            <option value="general_category">Categoría</option>
        </select>

        <button type="submit">Buscar</button>
    </form>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <ul>
        @forelse ($projects as $project)
            <a href="{{ route('projects.show', $project->id) }}">
                <li>
                    <strong>{{ $project->name }}</strong><br>
                    <span><em>Categoría:</em> {{ $project->general_category }} | <em>Fecha:</em>
                        {{ $project->creation_date }}</span><br>
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
                    <p class="text-sm text-gray-500">👁️ {{ $project->views }} visitas</p>

                    <p>Calificación actual: {{ $project->averageRating() ? number_format($project->averageRating(), 1) : 'Sin calificaciones' }}</p>
    
                    <hr>
                </li>
            </a>
        @empty
            <p>No hay proyectos disponibles.</p>
        @endforelse
    </ul>

    <div x-data="{ showModal: false }">
        <button @click="showModal = true">Crear proyecto</button>

        <div x-show="showModal" style="margin-top: 1rem; border: 1px solid #ccc; padding: 1rem;">
            <h2>Nuevo proyecto</h2>

            <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <label>Título:</label>
                <input type="text" name="title" required><br>

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

                <label>Archivos adicionalaes:</label>
                <input type="file" name="files[]" multiple accept="file/*"><br>

                <button type="submit">Guardar</button>
                <button type="button" @click="showModal = false">Cancelar</button>
            </form>
        </div>
    </div>

    <hr><br>

    <h2>Proyectos Escolares</h2>
    <ul>
        @forelse ($schoolProjects as $school)
            <a href="{{ route('school.projects.show', $school->id) }}">
                <li>
                    <strong>{{ $school->title }}</strong><br>
                    <span><em>Categoría:</em> {{ $school->general_category }} | <em>Fecha:</em>
                        {{ $school->creation_date }}</span><br>
                    <p>{{ $school->description }}</p>

                    @php
                        $favorite = auth()->user()->favorites()
                            ->where('type', 'proyecto')
                            ->where('reference_id', $school->id)
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
                            <input type="hidden" name="reference_id" value="{{ $school->id }}">
                            <button type="submit">❤️ Añadir a favoritos</button>
                        </form>
                    @endif
                    <p class="text-sm text-gray-500">👁️ {{ $school->views }} visitas</p>

                    <p>Calificación actual: {{ $school->averageRating() ? number_format($school->averageRating(), 1) : 'Sin calificaciones' }}</p>
    
                    <hr>
                </li>
            </a>
        @empty
            <p>No hay proyectos escolares disponibles.</p>
        @endforelse
    </ul>
@endsection