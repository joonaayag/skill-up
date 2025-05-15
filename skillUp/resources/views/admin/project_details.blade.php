@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold mb-4">Detalles del Proyecto</h1>

        <div class="bg-white rounded shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-2">{{ $project->name }}</h2>
            <p class="text-sm text-gray-600 mb-1"><strong>Autor:</strong> {{ $project->author->name ?? 'Sin autor' }}</p>
            <p class="text-sm text-gray-600 mb-1"><strong>Fecha de creación:</strong>
                {{ \Carbon\Carbon::parse($project->creation_date)->format('d/m/Y') }}</p>
            <p class="text-sm text-gray-600 mb-1"><strong>Categoría:</strong> {{ $project->general_category ?? '-' }}</p>
            <p class="text-sm text-gray-600 mb-1"><strong>Tags:</strong> {{ $project->tags ?? '-' }}</p>
            <p class="text-sm text-gray-600 mb-1"><strong>Valoración media:</strong>
                {{ number_format($project->averageRating() ?? 0, 1) }} / 5</p>

            @if ($project->link)
                <p class="text-sm text-blue-600 mt-2">
                    <a href="{{ $project->link }}" target="_blank" class="hover:underline">Ver enlace externo</a>
                </p>
            @endif

            <div class="mt-4">
                <h3 class="font-semibold mb-2">Descripción:</h3>
                <p>{{ $project->description }}</p>
            </div>

            @if ($project->image)
                <div class="mt-4">
                    <h3 class="font-semibold mb-2">Imagen destacada:</h3>
                    <img src="{{ asset('storage/' . $project->image) }}" alt="Imagen del proyecto"
                        class="w-full max-w-md rounded">
                </div>
            @endif

            <h2>Arcvhivos destacados------------</h2>

            @if ($project->images && $project->images->count())
                <div style="margin-bottom: 1.5rem;">
                    <strong>Archivos del proyecto:</strong>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                        @foreach ($project->images as $img)
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
        </div>
        <div class="flex justify-end gap-4 mb-6" x-data="{ showDelete: false, showEdit_{{ $project->id }}: false }">

            <button @click="showEdit_{{ $project->id }} = true" class="bg-yellow-500 text-white px-3 py-1 rounded">
                Editar
            </button>

            <!-- Modal de edición -->
            <div x-cloak x-show="showEdit_{{ $project->id }}"
                class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                <div class="bg-white w-full max-w-lg p-6 rounded shadow relative"
                    @click.outside="showEdit_{{ $project->id }} = false">
                    <h3 class="text-xl font-bold mb-4">Editar Proyecto</h3>

                    <form action="{{ route('admin.project.update', $project->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <label class="block text-sm">Título</label>
                        <input type="text" name="title" class="w-full border rounded px-3 py-2 mb-2"
                            value="{{ old('title', $project->title) }}" required>

                        <label class="block text-sm">Descripción</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2 mb-2"
                            required>{{ old('description', $project->description) }}</textarea>

                        <label class="block text-sm">Etiquetas (tags)</label>
                        <input type="text" name="tags" class="w-full border rounded px-3 py-2 mb-2"
                            value="{{ old('tags', $project->tags) }}" required>

                        <label class="block text-sm">Categoría</label>
                        <select name="sector_category" class="w-full border rounded px-3 py-2 mb-2" required>
                            @php
                                $categorias = [
                                    'Tecnología y desarrollo',
                                    'Diseño y comunicación',
                                    'Administración y negocio',
                                    'Comunicación',
                                    'Educación',
                                    'Ciencia y salud',
                                    'Industria',
                                    'Otro'
                                ];
                            @endphp
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria }}" {{ old('sector_category', $project->sector_category) == $categoria ? 'selected' : '' }}>
                                    {{ $categoria }}
                                </option>
                            @endforeach
                        </select>

                        <label class="block text-sm">Fecha de creación</label>
                        <input type="date" name="creation_date" class="w-full border rounded px-3 py-2 mb-2"
                            value="{{ old('creation_date', \Carbon\Carbon::parse($project->creation_date)->format('Y-m-d')) }}"
                            required>

                        <label class="block text-sm">Enlace (opcional)</label>
                        <input type="url" name="link" class="w-full border rounded px-3 py-2 mb-2"
                            value="{{ old('link', $project->link) }}">

                        <label class="block text-sm">Imagen destacada</label>
                        <input type="file" name="image" accept="image/*" class="mb-2">

                        <label class="block text-sm">Archivos adicionales</label>
                        <input type="file" name="files[]" multiple class="mb-4">

                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" @click="showEdit_{{ $project->id }} = false"
                                class="border px-4 py-2 rounded">Cancelar</button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar
                                cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            <button @click="showDelete = true" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                Eliminar
            </button>

            <div x-show="showDelete" x-cloak
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
                    <h2 class="text-lg font-semibold mb-4">¿Estás seguro?</h2>
                    <p class="mb-4">Esta acción eliminará el proyecto <strong>{{ $project->name }}</strong> de forma
                        permanente.</p>
                    <div class="flex justify-end gap-3">
                        <button @click="showDelete = false"
                            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
                        <form action="{{ route('admin.project.destroy', $project->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <a href="{{ route('admin.projects') }}"
            class="mt-3 px-2 py-2 bg-themeBlue text-white hover:bg-themeHoverBlue flex items-center gap-2 w-max rounded transition duration-200 ease-in-out transform hover:scale-101">
            <x-icon name="arrow-left" class="w-5 h-auto" />
            Volver
        </a>

    </div>
@endsection