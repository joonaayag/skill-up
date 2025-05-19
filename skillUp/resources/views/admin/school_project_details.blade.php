@extends('layouts.app')

@section('content')
    <div x-data="{ selectedImage: null }">

        <x-heading level="h1" class="mb-10">{{ __('messages.project-details.title-school')  }}</x-heading>
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
            <x-tags>{{ __('messages.tags.' . strtolower(string: $schoolProject->tags)) }}</x-tags>

            <x-heading level="h2" class="mt-6 mb-3">{{ $schoolProject->title }}</x-heading>
            @php
                $categoryMap = [
                    'Administración y negocio' => 'option-admin',
                    'Ciencia y salud' => 'option-science',
                    'Comunicación' => 'option-comunication',
                    'Diseño y comunicación' => 'option-design',
                    'Educación' => 'option-education',
                    'Industria' => 'option-industry',
                    'Otro' => 'option-other',
                    'Tecnología y desarrollo' => 'option-tec',
                ];

                $categoryKey = $categoryMap[$schoolProject->general_category] ?? null;
            @endphp

            @if ($categoryKey)
                <x-heading level="h4" class="mb-4">
                    {{ __('messages.projects.' . $categoryKey) }}
                </x-heading>
            @endif


            <p class="mb-9 break-words">{{ $schoolProject->description }}</p>


            @if($schoolProject->image)
                <img src="{{ asset('storage/' . $schoolProject->image) }}" alt="Imagen del proyecto"
                    class="mx-auto w-2/3 h-auto mb-4 rounded-lg shadow-md">
            @endif

            <div class="flex justify-between mt-16">
                <div class="flex gap-4 items-center justify-center">
                    <p>{{ __('messages.project-details.author') }}
                        <strong>{{ $schoolProject->teacher->name . ' ' . $schoolProject->teacher->last_name }}</strong></p>
                    <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                            class="w-4 h-auto" />{{ $schoolProject->views }}</p>
                    <p class="flex items-center justify-center gap-1">
                        <label class="text-yellow-400"><x-icon name="star" class="w-4 h-auto" /></label>
                        {{ $schoolProject->averageRating() ? number_format($schoolProject->averageRating(), 1) : 'N/A' }}
                    </p>
                    @auth
                        <form id="rating-form" action="{{ route('projects.rate', $schoolProject->id) }}" method="POST"
                            class="flex gap-1">
                            @csrf
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="submit" name="rating" value="{{ $i }}"
                                    class="text-3xl focus:outline-none transition transform hover:scale-110 cursor-pointer hover:text-yellow-400
                                                                    {{ $schoolProject->getRatingByUser(auth()->id()) && $schoolProject->getRatingByUser(auth()->id())->rating >= $i ? 'text-yellow-400' : 'text-gray-400' }}"
                                    aria-label="Valorar con {{ $i }} estrella{{ $i > 1 ? 's' : '' }}">
                                    <x-icon name="star" class="w-4 h-auto" />
                                </button>
                            @endfor
                        </form>

                    @endauth
                </div>
                <div class="flex flex-col justify-end [&>p]:text-black dark:[&>p]:text-themeLightGray">
                    <p class="text-sm text-gray-500">
                        {{ __('messages.project-details.published') . $schoolProject->created_at }}</p>
                    <p class="text-sm text-gray-500">
                        {{ __('messages.project-details.created-at') . $schoolProject->creation_date }}</p>
                </div>
            </div>

        </x-card>


        <x-card>
            @if($schoolProject->link)
                <p><strong>{{ __('messages.project-details.link')  }}</strong> <a href="{{ $schoolProject->link }}"
                        target="_blank">{{ $schoolProject->link }}</a></p>
            @endif

            <x-heading level="h2" class="mt-2 mb-3">{{ __('messages.project-details.project-files')  }}</x-heading>
            @if ($schoolProject->images && $schoolProject->images->count())
                <div class="mb-6">
                    <div class="flex flex-wrap gap-2.5 mt-2.5">
                        @foreach ($schoolProject->images as $img)
                            @php
                                $extension = pathinfo($img->path, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp
                            <div class="flex-1 min-w-28">
                                @if ($isImage)
                                    <a href="#" @click.prevent="selectedImage = '{{ asset('storage/' . $img->path) }}'"
                                        class="block bg-gray-100 p-3 rounded shadow text-sm text-center dark:bg-themeDarkGray hover:bg-gray-200">
                                        {{ __('messages.project-details.see-image') }}
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $img->path) }}" download
                                        class="block bg-gray-100 p-3 rounded shadow text-sm text-center hover:bg-gray-200">
                                        📄 {{ __('messages.project-details.download-file')  }} ({{ $extension }})
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="mb-6">{{ __('messages.project-details.no-files')  }}</p>
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

            @include('comments.comment_section', ['commentable' => $schoolProject, 'type' => 'school-project'])
        </x-card>

        <a href="{{ route('projects.index') }}"
            class="mt-3 px-2 py-2 bg-themeBlue text-white hover:bg-themeHoverBlue flex items-center gap-2 w-max rounded transition duration-200 ease-in-out transform hover:scale-101">
            <x-icon name="arrow-left" class="w-5 h-auto" /> {{ __('messages.project-details.back')  }}</a>

    </div>


    <div class="flex mt-3 gap-3">
        <div x-cloak x-data="{ showModal: false }"
            x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))"
            class="relative z-10">

            <button @click="showModal = true"
                class=" p-2 bg-yellow-400 text-black shadow-lg hover:bg-yellow-500 transition cursor-pointer">
                {{ __('messages.button.edit')  }}
            </button>


            <x-modal>
                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Editar
                    proyecto</x-heading>

                <form action="{{ route('admin.school_project.update', $schoolProject->id) }}" enctype="multipart/form-data"
                    class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0">
                    @method('PUT')
                    @csrf

                    <div>
                        <x-label for="title">Título:</x-label>
                        <x-inputtext type="text" name="title" id="title" value="{{ old('title', $schoolProject->title) }}"
                            required />
                    </div>

                    <div>
                        <x-label for="description">Descripción:</x-label>
                        <x-textarea name="description" id="description"
                            required>{{ old('description', $schoolProject->description) }}</x-textarea>
                    </div>

                    <div>
                        <x-label for="author">Autor:</x-label>
                        <x-inputtext type="text" name="author" id="author"
                            value="{{ old('author', $schoolProject->author) }}" required />
                    </div>

                    <div>
                        <x-label for="tags">Etiquetas (tags)</x-label>
                        <select name="tags" id="tags"
                            class="w-full px-3 py-2 dark:bg-themeBgDark rounded border border-themeLightGray" required>
                            <option value="TFG" {{ old('tags') == 'TFG' ? 'selected' : '' }}>
                                {{ __('messages.tags.tfg') }}
                            </option>
                            <option value="TFM" {{ old('tags') == 'TFM' ? 'selected' : '' }}>
                                {{ __('messages.tags.tfm') }}
                            </option>
                            <option value="Tesis" {{ old('tags') == 'Tesis' ? 'selected' : '' }}>
                                {{ __('messages.tags.tesis') }}
                            </option>
                            <option value="Individual" {{ old('tags') == 'Individual' ? 'selected' : '' }}>
                                {{ __('messages.tags.individual') }}
                            </option>
                            <option value="Grupal" {{ old('tags') == 'Grupal' ? 'selected' : '' }}>
                                {{ __('messages.tags.grupal') }}
                            </option>
                            <option value="Tecnología" {{ old('tags') == 'Tecnología' ? 'selected' : '' }}>
                                {{ __('messages.tags.tecnologia') }}
                            </option>
                            <option value="Ciencias" {{ old('tags') == 'Ciencias' ? 'selected' : '' }}>
                                {{ __('messages.tags.ciencias') }}
                            </option>
                            <option value="Artes" {{ old('tags') == 'Artes' ? 'selected' : '' }}>
                                {{ __('messages.tags.artes') }}
                            </option>
                            <option value="Ingeniería" {{ old('tags') == 'Ingeniería' ? 'selected' : '' }}>
                                {{ __('messages.tags.ingenieria') }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <x-label for="general_category">Categoría general:</x-label>
                        <select name="general_category" required
                            class="w-full px-3 py-2 rounded border border-themeLightGray dark:bg-themeBgDark">
                            <option value="Administración y negocio">{{ __('messages.school-projects.option-admin') }}
                            </option>
                            <option value="Ciencia y salud">{{ __('messages.school-projects.option-science') }}</option>
                            <option value="Comunicación">{{ __('messages.school-projects.option-comunication') }}</option>
                            <option value="Diseño y comunicación">{{ __('messages.school-projects.option-design') }}
                            </option>
                            <option value="Educación">{{ __('messages.school-projects.option-education') }}</option>
                            <option value="Industria">{{ __('messages.school-projects.option-industry') }}</option>
                            <option value="Otro">{{ __('messages.school-projects.option-other') }}</option>
                            <option value="Tecnología y desarrollo">{{ __('messages.school-projects.option-tec') }}</option>
                        </select>
                    </div>

                    <div>
                        <x-label for="title">Fecha de creación:</x-label>
                        <x-inputdate name="creation_date" id="creation_date"
                            value="{{ old('creation_date', $schoolProject->creation_date) }}" required />
                    </div>

                    <div>
                        <x-label for="title">Enlace (Opcional):</x-label>
                        <input type="url" name="link" class="w-full px-3 py-2 rounded border border-themeLightGray"
                            value="{{ old('link', $schoolProject->link) }}" />
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
            x-init="$watch('showDelete', val => document.body.classList.toggle('overflow-hidden', val))">

            <button @click="showDelete = true"
                class=" p-2 bg-red-600 text-white shadow-lg hover:bg-red-700 transition cursor-pointer">
                {{ __('messages.button.delete') }}
            </button>

            <x-modal :show="'showDelete'">
                <x-heading level="h3" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Seguro
                    deses eliminar {{ $schoolProject->title }} de {{ $schoolProject->teacher->name }}?</x-heading>
                <form action="{{ route('admin.school_project.destroy', $schoolProject->id) }}" method="POST"
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

        <a href="{{ route('admin.school_projects') }}"
            class=" px-2 py-2 bg-themeBlue text-white hover:bg-themeHoverBlue flex items-center gap-2 w-max rounded transition duration-200 ease-in-out transform hover:scale-101">
            <x-icon name="arrow-left" class="w-5 h-auto" />
            {{ __('messages.project-details.back')  }}
        </a>
    </div>

    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('rating-form');

            if (form) {
                const inputs = form.querySelectorAll('input, select');

                inputs.forEach(input => {
                    input.addEventListener('change', () => {
                        form.submit();
                    });

                    if (input.tagName === 'INPUT') {
                        input.addEventListener('keyup', () => {
                            clearTimeout(input._timeout);
                            input._timeout = setTimeout(() => form.submit(), 100);
                        });
                    }
                });
            }
        });

    </script>
@endsection