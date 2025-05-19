@extends('layouts.app')

@section('content')

    <div x-data="{ selectedImage: null }" x-cloak>

        <x-heading level="h1" class="mb-10">Detalles del proyecto </x-heading>
        @if ($errors->any())
            <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="text-black dark:text-white">- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-card class="mb-12">
            <x-tags>{{ $project->tags }}</x-tags>

            <x-heading level="h2" class="mt-6 mb-3">{{ $project->title }}</x-heading>
            <x-heading level="h4" class="mb-4">{{ $project->general_category }}</x-heading>


            <p class="mb-9 break-words">{{ $project->description }}</p>


            @if($project->image)
                <img src="{{ asset('storage/' . $project->image) }}" alt="Imagen del proyecto"
                    class="mx-auto w-2/3 h-auto mb-4 rounded-lg shadow-md">
            @endif

            <div class="flex justify-between mt-16">
                <div class="flex gap-4 items-center justify-center">
                    <p>Autor: <strong>{{ $project->author->name . ' ' . $project->author->last_name }}</strong></p>
                    <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                            class="w-4 h-auto" />{{ $project->views }}</p>
                    <p>
                        <label>★</label>
                        {{ $project->averageRating() ? number_format($project->averageRating(), 1) : 'Sin calificaciones' }}
                    </p>
                </div>
                <div class="flex flex-col justify-end [&>p]:text-black dark:[&>p]:text-themeLightGray">
                    <p class="text-sm text-gray-500">Publicado el: {{ $project->created_at }}</p>
                    <p class="text-sm text-gray-500">Realizado el: {{ $project->creation_date }}</p>
                </div>
            </div>

        </x-card>


        <x-card>
            @if($project->link)
                <p><strong>Enlace:</strong> <a href="{{ $project->link }}" target="_blank">{{ $project->link }}</a></p>
            @endif

            <x-heading level="h2" class="mt-2 mb-3">Archivos del proyecto</x-heading>
            @if ($project->images && $project->images->count())
                <div class="mb-6">
                    <div class="flex flex-wrap gap-2.5 mt-2.5">
                        @foreach ($project->images as $img)
                            @php
                                $extension = pathinfo($img->path, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp
                            <div class="flex-1 min-w-28">
                                @if ($isImage)
                                    <a href="#" @click.prevent="selectedImage = '{{ asset('storage/' . $img->path) }}'"
                                        class="block bg-gray-100 p-3 rounded shadow text-sm text-center dark:bg-themeDarkGray hover:bg-gray-200">
                                        Ver imagen
                                    </a>
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
            @else
                <p class="mb-6">No hay archivos por el momento</p>
            @endif

            <!-- Image Modal -->
            <template x-if="selectedImage">
                <div x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 p-4"
                    @click.self="selectedImage = null">
                    <div class="max-w-full max-h-full relative">
                        <img :src="selectedImage" class="max-w-full max-h-[90vh] object-contain" alt="Selected Image" />
                        <button @click="selectedImage = null"
                            class="absolute top-2 right-2 bg-white rounded-full p-2 text-black hover:bg-gray-200">
                            ✕
                        </button>
                    </div>
                </div>
            </template>

            @include('comments.comment_section', ['commentable' => $project, 'type' => 'project'])
        </x-card>

    </div>

    <div class="flex mt-3 gap-3">
        <div x-cloak x-data="{ showModal: false }"
            x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))"
            class="relative z-10">

            <button @click="showModal = true"
                class=" p-2 bg-yellow-400 text-black shadow-lg hover:bg-yellow-500 transition cursor-pointer">
                Editar
            </button>


            <x-modal>
                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Editar
                    proyecto</x-heading>

                <form action="{{ route('admin.project.update', $project->id) }}" enctype="multipart/form-data"
                    class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0">
                    @method('PUT')
                    @csrf

                    <div>
                        <x-label for="title">Título:</x-label>
                        <x-inputtext type="text" name="title" id="title" value="{{ old('title', $project->title) }}"
                            required />
                    </div>

                    <div>
                        <x-label for="description">Descripción:</x-label>
                        <x-textarea name="description" id="description"
                            required>{{ old('description', $project->description) }}</x-textarea>
                    </div>

                    <div>
                        <x-label for="tags">Etiquetas (tags)</x-label>
                        <select name="tags" id="tags"
                            class="w-full px-3 py-2 dark:bg-themeBgDark rounded border border-themeLightGray" required>
                            <option value="TFG" {{ old('tags') == 'TFG' ? 'selected' : '' }}>{{ __('messages.tags.tfg') }}
                            </option>
                            <option value="TFM" {{ old('tags') == 'TFM' ? 'selected' : '' }}>{{ __('messages.tags.tfm') }}
                            </option>
                            <option value="Tesis" {{ old('tags') == 'Tesis' ? 'selected' : '' }}>
                                {{ __('messages.tags.tesis') }}</option>
                            <option value="Individual" {{ old('tags') == 'Individual' ? 'selected' : '' }}>
                                {{ __('messages.tags.individual') }}</option>
                            <option value="Grupal" {{ old('tags') == 'Grupal' ? 'selected' : '' }}>
                                {{ __('messages.tags.grupal') }}</option>
                            <option value="Tecnología" {{ old('tags') == 'Tecnología' ? 'selected' : '' }}>
                                {{ __('messages.tags.tecnologia') }}</option>
                            <option value="Ciencias" {{ old('tags') == 'Ciencias' ? 'selected' : '' }}>
                                {{ __('messages.tags.ciencias') }}</option>
                            <option value="Artes" {{ old('tags') == 'Artes' ? 'selected' : '' }}>
                                {{ __('messages.tags.artes') }}</option>
                            <option value="Ingeniería" {{ old('tags') == 'Ingeniería' ? 'selected' : '' }}>
                                {{ __('messages.tags.ingenieria') }}</option>
                        </select>
                    </div>

                    <div>
                        <x-label for="general_category">Categoría general:</x-label>
                        <select name="sector_category" required
                           class="w-full px-3 py-2 dark:bg-themeBgDark rounded border border-themeLightGray">
                            <option value="Administración y negocio" {{ old('sector_category', $project->general_category) == 'Administración y negocio' ? 'selected' : '' }}>Administración
                                y negocio</option>
                            <option value="Ciencia y salud" {{ old('sector_category', $project->general_category) == 'Ciencia y salud' ? 'selected' : '' }}>Ciencia y salud</option>
                            <option value="Comunicación" {{ old('sector_category', $project->general_category) == 'Comunicación' ? 'selected' : '' }}>Comunicación</option>
                            <option value="Diseño y comunicación" {{ old('sector_category', $project->general_category) == 'Diseño y comunicación' ? 'selected' : '' }}>Diseño y
                                comunicación</option>
                            <option value="Educación" {{ old('sector_category', $project->general_category) == 'Educación' ? 'selected' : '' }}>Educación</option>
                            <option value="Industria" {{ old('sector_category', $project->general_category) == 'Industria' ? 'selected' : '' }}>Industria</option>
                            <option value="Otro" {{ old('sector_category', $project->general_category) == 'Otro' ? 'selected' : '' }}>Otro</option>
                            <option value="Tecnología y desarrollo" {{ old('sector_category', $project->general_category) == 'Tecnología y desarrollo' ? 'selected' : '' }}>Tecnología y
                                desarrollo</option>
                        </select>
                    </div>

                    <div>
                        <x-label for="title">Fecha de creación:</x-label>
                        <x-inputdate name="creation_date" id="creation_date"
                            value="{{ old('creation_date', $project->creation_date) }}" required />
                    </div>

                    <div>
                        <x-label for="title">Enlace (Opcional):</x-label>
                        <input type="url" name="link" class="w-full px-3 py-2 rounded border border-themeLightGray"
                            value="{{ old('link', $project->link) }}" />
                    </div>

                    <div>
                        <x-label for="title">Imagen destacada:</x-label>
                        <div x-data="{ fileName: '', previewUrl: '' }" class="w-full">
                            <label for="image-upload"
                                class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/90 transition">
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
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">📄 <span x-text="fileName"></span>
                                </p>
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
                                class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/90 transition">
                                📎 Subir archivos
                                <input id="file-upload" name="files[]" type="file" multiple accept="file/*" class="hidden"
                                    @change="fileNames = [...$event.target.files].map(f => f.name)" />
                            </label>

                            <template x-if="fileNames.length > 0">
                                <ul
                                    class="mt-2 text-sm text-black dark:text-themeLightGray space-y-1 list-disc list-inside">
                                    <template x-for="name in fileNames" :key="name">
                                        <li x-text="name"></li>
                                    </template>
                                </ul>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-themeBlue text-white rounded hover:bg-themeBlue/80 transition cursor-pointer">
                            Guardar
                        </button>
                    </div>
                </form>
            </x-modal>

        </div>

        <div x-cloak x-data="{ showDelete: false }"
            x-init="$watch('showDelete', val => document.body.classList.toggle('overflow-hidden', val))"
            class="relative z-10">

            <button @click="showDelete = true"
                class=" p-2 bg-red-600 text-white shadow-lg hover:bg-red-700 transition cursor-pointer">
                Eliminar
            </button>

            <x-modal :show="'showDelete'">
                <x-heading level="h3" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Seguro
                    deses eliminar {{ $project->title }} de {{ $project->author->name }}?</x-heading>
                <form action="{{ route('admin.project.destroy', $project->id) }}" method="POST"
                    class="flex justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="p-2 bg-white dark:bg-themeBgDark text-themeRed border-2 border-themeRed shadow-lg rounded-lg hover:bg-themeHoverRed/30 transition cursor-pointer">Eliminar</button>
                    <button type="button"
                        class="px-4 py-2 bg-themeLightGray text-gray-800 cursor-pointer hover:bg-gray-300 transition rounded-lg"
                        @click="showDelete = false">Cancelar</button>
                </form>

            </x-modal>
        </div>

        <a href="{{ route('admin.projects') }}"
            class=" px-2 py-2 bg-themeBlue text-white hover:bg-themeHoverBlue flex items-center gap-2 w-max rounded transition duration-200 ease-in-out transform hover:scale-101">
            <x-icon name="arrow-left" class="w-5 h-auto" />
            Volver
        </a>
    </div>

    </div>
@endsection