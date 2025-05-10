@extends('layouts.app')

@section('title', 'Proyectos')

@section('content')
    <x-heading level="h1" class="mb-10">Proyectos</x-heading>

    <form id="project-filter-form" method="GET" action="{{ route('projects.index') }}" class="mb-16 space-x-5 h-12 w-full [&>input]:h-full [&>select]:h-full
                                    [&>select]:bg-white [&>input]:bg-white [&>input]:rounded-lg [&>select]:rounded-lg [&>input]:border-2 [&>input]:border-themeLightGray
                                     [&>select]:border-2 [&>select]:border-themeLightGray [&>select]:px-4 [&>input]:px-4 [&>input]:outline-0
                                    ">
        <input type="text" name="title" placeholder="Título" value="{{ request('title') }}">
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
            <option value="">Ordenar por</option>
            <option value="title">Titulo</option>
            <option value="creation_date">Fecha</option>
            <option value="general_category">Categoría</option>
        </select>
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

    <ul class="grid grid-cols-3 gap-10">
        @forelse ($projects as $project)
            <a href="{{ route('projects.show', $project->id) }}">
                <x-card class="h-full">
                    <li class="flex flex-col h-full ">
                        <x-tags class="mb-2">{{ $project->tags }}</x-tags>
                        <x-heading level="h3" class="mb-1">{{ $project->title }}</x-heading>
                        <span>{{ $project->general_category }}</span>
                        <p class=" text-sm mb-1.5">{{ Str::limit($project->description, 100) }}</p>

                        @php
                            $favorite = auth()->user()->favorites()
                                ->where('type', 'proyecto')
                                ->where('reference_id', $project->id)
                                ->first();
                        @endphp

                        <div class="flex flex-row justify-between items-center  ">
                            <div class="flex flex-row gap-3">
                                @if ($favorite)
                                    <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">❌</button>
                                    </form>
                                @else
                                    <form action="{{ route('favorites.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="proyecto">
                                        <input type="hidden" name="reference_id" value="{{ $project->id }}">
                                        <button type="submit">❤️</button>
                                    </form>
                                @endif
                                <p>👁️{{ $project->views }}</p>
                                <p>
                                    {{ $project->averageRating() ? number_format($project->averageRating(), 1) : 'Sin calificaciones' }}
                                </p>
                            </div>
                            <span class="text-sm">{{ $project->author->name . ' ' . $project->author->last_name  }}</span>
                        </div>

                    </li>
                </x-card>
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

    <x-heading level="h1" class="mb-10">Proyectos de escolares</x-heading>
    <ul class="grid grid-cols-3 gap-10">
        @forelse ($schoolProjects as $school)
            <a href="{{ route('school.projects.show', $school->id) }}">
                <x-card class="h-full">
                    <li class="flex flex-col">
                        <x-tags class="mb-2">{{ $school->tags }}</x-tags>
                        <x-heading level="h3" class="mb-1">{{ $school->title }}</x-heading>
                        <span>{{ $school->general_category }}</span>
                        <p class=" text-sm mb-1.5">{{ Str::limit($school->description, 100) }}</p>

                        @php
                            $favorite = auth()->user()->favorites()
                                ->where('type', 'proyecto')
                                ->where('reference_id', $school->id)
                                ->first();
                        @endphp

                        <div class="flex flex-row justify-between items-center  ">
                            <div class="flex flex-row gap-3">
                                @if ($favorite)
                                    <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">❌</button>
                                    </form>
                                @else
                                    <form action="{{ route('favorites.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="proyecto">
                                        <input type="hidden" name="reference_id" value="{{ $school->id }}">
                                        <button type="submit">❤️</button>
                                    </form>
                                @endif
                                <p>👁️{{ $school->views }}</p>
                                <p>
                                    {{ $school->averageRating() ? number_format($school->averageRating(), 1) : 'Sin calificaciones' }}
                                </p>
                            </div>
                            <span class="text-sm">
                                {{ $school->teacher?->name . ' ' . $school->teacher?->last_name }}
                            </span>

                        </div>

                    </li>
                </x-card>
                </li>
            </a>
        @empty
            <p>No hay proyectos escolares disponibles.</p>
        @endforelse
    </ul>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('project-filter-form');

            if (form) {
                const inputs = form.querySelectorAll('input, select');

                inputs.forEach(input => {
                    input.addEventListener('change', () => {
                        form.submit();
                    });

                    if (input.tagName === 'INPUT') {
                        input.addEventListener('keyup', () => {
                            clearTimeout(input._timeout);
                            input._timeout = setTimeout(() => form.submit(), 1000);
                        });
                    }
                });
            }
        });

    </script>

@endsection