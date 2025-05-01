@extends('layouts.app')

@section('title', 'Tus proyectos')

@section('content')
    <form method="GET" action="{{ route('projects.ownProjects') }}" class="mb-6 space-y-2">
        <input type="text" name="name" placeholder="Título" value="{{ request('name') }}">
        <input type="text" name="description" placeholder="Descripción" value="{{ request('description') }}">

        <select name="category">
            <option value="">-- Categoría --</option>
            <option value="Tecnología y desarrollo" @selected(request('category') == 'Tecnología y desarrollo')>Tecnología y
                desarrollo</option>
            <option value="Diseño y comunicación" @selected(request('category') == 'Diseño y comunicación')>Diseño y
                comunicación</option>
            <option value="Administración y negocio" @selected(request('category') == 'Administración y negocio')>
                Administración y negocio</option>
            <option value="Comunicación" @selected(request('category') == 'Comunicación')>Comunicación</option>
            <option value="Educación" @selected(request('category') == 'Educación')>Educación</option>
            <option value="Ciencia y salud" @selected(request('category') == 'Ciencia y salud')>Ciencia y salud</option>
            <option value="Industria" @selected(request('category') == 'Industria')>Industria</option>
            <option value="Otro" @selected(request('category') == 'Otro')>Otro</option>
        </select>

        <select name="order">
            <option value="">-- Ordenar por --</option>
            <option value="name" @selected(request('order') == 'name')>Nombre</option>
            <option value="creation_date" @selected(request('order') == 'creation_date')>Fecha</option>
            <option value="general_category" @selected(request('order') == 'general_category')>Categoría</option>
        </select>

        <button type="submit">Filtrar</button>
    </form>
    <ul>
        @forelse ($userProjects as $project)
            <a href="{{ route('projects.show', $project->id) }}">
                <li>
                    <strong>{{ $project->name }}</strong><br>
                    <span><em>Categoría:</em> {{ $project->category }} | <em>Fecha:</em>
                        {{ $project->creation_date }}</span><br>
                    <p>{{ $project->description }}</p>
                    <hr>
                </li>
            </a>
        @empty
            <p>No tienes proyectos disponibles.</p>
        @endforelse
    </ul>

    <div x-data="{ showModal: false }" class="mb-6">
        <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
            + Crear nuevo proyecto
        </button>

        <!-- Modal -->
        <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white w-full max-w-lg p-6 rounded shadow relative" @click.away="showModal = false">
                <h3 class="text-xl font-bold mb-4">Nuevo Proyecto</h3>

                <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <label class="block text-sm">Título</label>
                    <input type="text" name="name" class="w-full border rounded px-3 py-2 mb-2" required>

                    <label class="block text-sm">Descripción</label>
                    <textarea name="description" class="w-full border rounded px-3 py-2 mb-2" required></textarea>

                    <label class="block text-sm">Etiquetas (tags)</label>
                    <input type="text" name="tags" class="w-full border rounded px-3 py-2 mb-2" required>

                    <label class="block text-sm">Categoría</label>
                    <select name="sector_category" class="w-full border rounded px-3 py-2 mb-2" required>
                        <option value="Tecnología y desarrollo">Tecnología y desarrollo</option>
                        <option value="Diseño y comunicación">Diseño y comunicación</option>
                        <option value="Administración y negocio">Administración y negocio</option>
                        <option value="Comunicación">Comunicación</option>
                        <option value="Educación">Educación</option>
                        <option value="Ciencia y salud">Ciencia y salud</option>
                        <option value="Industria">Industria</option>
                        <option value="Otro">Otro</option>
                    </select>

                    <label class="block text-sm">Fecha de creación</label>
                    <input type="date" name="creation_date" class="w-full border rounded px-3 py-2 mb-2" required>

                    <label class="block text-sm">Enlace (opcional)</label>
                    <input type="url" name="link" class="w-full border rounded px-3 py-2 mb-2">

                    <label class="block text-sm">Imagen destacada</label>
                    <input type="file" name="image" accept="image/*" class="mb-2">

                    <label class="block text-sm">Archivos adicionales</label>
                    <input type="file" name="files[]" multiple class="mb-4">

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showModal = false" class="border px-4 py-2 rounded">Cancelar</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection