@extends('layouts.app')

@section('title', 'Tus proyectos')

@section('content')
    <x-heading level="h1" class="mb-10">Tus proyectos</x-heading>
    <form id="own-projects-filter-form" method="GET" action="{{ route('projects.ownProjects') }}"
        class="mb-16 space-x-5 h-12 w-full [&>input]:h-full [&>select]:h-full
          [&>select]:bg-white [&>input]:bg-white [&>input]:rounded-lg [&>select]:rounded-lg [&>input]:border-2 [&>input]:border-themeLightGray
            [&>select]:border-2 [&>select]:border-themeLightGray [&>select]:px-4 [&>input]:px-4 [&>input]:outline-0 dark:[&>select]:text-themeLightGray
             dark:[&>input]:text-themeLightGray [&>input]:placeholder:text-themeLightGray [&>select]:placeholder:text-themeLightGray dark:[&>input]:bg-themeBgDark dark:[&>select]:bg-themeBgDark">
        <input type="text" name="title" placeholder="Título" value="{{ request('title') }}">
        <input type="text" name="description" placeholder="Descripción" value="{{ request('description') }}">

        <select name="category">
            <option value="">Categoría</option>
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
            <option value="">Ordenar por</option>
            <option value="name" @selected(request('order') == 'name')>Nombre</option>
            <option value="creation_date" @selected(request('order') == 'creation_date')>Fecha</option>
            <option value="general_category" @selected(request('order') == 'general_category')>Categoría</option>
        </select>
    </form>

    <ul class="grid grid-cols-3 gap-10">

        @forelse ($userProjects as $project)
            <a href="{{ route('projects.show', $project->id) }}">
                <x-card class="h-full">
                    <li class="flex flex-col h-full ">
                        <x-tags class="mb-2">{{ $project->tags }}</x-tags>
                        <x-heading level="h3" class="mb-1">{{ $project->title }}</x-heading>
                        <span>{{ $project->general_category }}</span>
                        <p class=" text-sm mb-1.5">{{ Str::limit($project->description, 100) }}</p>

                        <div class="flex flex-row justify-between items-center mt-auto">
                            <div class="flex flex-row gap-3">
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

    <div x-data="{ showModal: false }"
        x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))" class="relative z-10">

        <button @click="showModal = true"
            class="fixed bottom-6 right-6 p-2 bg-themeBlue text-white rounded-full shadow-lg hover:bg-themeHoverBlue transition">
            <x-icon name="plus" />
        </button>


        <x-modal>
            <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Nuevo proyecto</x-heading>

            <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0">
                @csrf

                <div>
                    <x-label for="title">Título:</x-label>
                    <x-inputtext type="text" name="title" id="title" required />
                </div>

                <div>
                    <x-label for="title">Descripción:</x-label>
                    <x-textarea name="description" id="description" required></x-textarea>
                </div>

                <div>
                    <x-label for="title">Etiquetas (tags)</x-label>
                    <x-inputtext type="text" name="tags" id="tags" required />
                </div>

                <div>
                    <x-label for="title">Categoría general:</x-label>
                    <select name="sector_category" required class="w-full px-3 py-2 rounded border border-themeLightGray">
                        <option value="Tecnología y desarrollo">Tecnología y desarrollo</option>
                        <option value="Diseño y comunicación">Diseño y comunicación</option>
                        <option value="Administración y negocio">Administración y negocio</option>
                        <option value="Comunicación">Comunicación</option>
                        <option value="Educación">Educación</option>
                        <option value="Ciencia y salud">Ciencia y salud</option>
                        <option value="Industria">Industria</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div>
                    <x-label for="title">Fecha de creación:</x-label>
                    <x-inputdate name="creation_date" id="creation_date" required />
                </div>

                <div>
                    <x-label for="title">Enlace (Opcional):</x-label>
                    <input type="url" name="link" class="w-full px-3 py-2 rounded border border-themeLightGray" />
                </div>

                <div>
                    <x-label for="title">Imagen destacada:</x-label>
                    <div x-data="{ fileName: '', previewUrl: '' }" class="w-full">
                        <label for="image-upload"
                            class="flex items-center justify-center w-full px-4 py-2 bg-themeBlue text-white font-medium rounded cursor-pointer hover:bg-themeHoverBlue transition">
                            🖼️ Subir imagen destacada
                            <input id="image-upload" type="file" name="image" accept="image/*" class="hidden" @change="
                            fileName = $event.target.files[0]?.name || '';
                            if ($event.target.files[0]) {
                                const reader = new FileReader();
                                reader.onload = e => previewUrl = e.target.result;
                                reader.readAsDataURL($event.target.files[0]);
                            }" />
                        </label>

                        <template x-if="fileName">
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">📄 <span x-text="fileName"></span></p>
                        </template>

                        <template x-if="previewUrl">
                            <img :src="previewUrl" alt="Vista previa"
                                class="mt-3 max-h-48 rounded border border-gray-300 shadow" />
                        </template>
                    </div>

                </div>

                <div>
                    <x-label for="title">Archivos adicionales</x-label>
                    <div x-data="{ fileNames: [] }" class="w-full">
                        <label for="file-upload"
                            class="flex items-center justify-center w-full px-4 py-2 bg-themeBlue text-white font-medium rounded cursor-pointer hover:bg-themeHoverBlue transition">
                            📎 Subir archivos
                            <input id="file-upload" name="files[]" type="file" multiple accept="file/*" class="hidden"
                                @change="fileNames = [...$event.target.files].map(f => f.name)" />
                        </label>

                        <template x-if="fileNames.length > 0">
                            <ul class="mt-2 text-sm text-black dark:text-themeLightGray space-y-1 list-disc list-inside">
                                <template x-for="name in fileNames" :key="name">
                                    <li x-text="name"></li>
                                </template>
                            </ul>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="submit"
                        class="px-4 py-2 bg-themeGrape/80 text-white rounded hover:bg-themeGrape transition cursor-pointer">
                        Guardar
                    </button>
                    <button type="button" @click="showModal = false"
                        class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                        Cancelar
                    </button>
                </div>
            </form>
            </form>
        </x-modal>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('own-projects-filter-form');

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