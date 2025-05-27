@extends('layouts.app')

@section('title', 'Proyectos Escolares')

@section('content')
    <x-heading level="h1" class="mb-10">{{ __('messages.school-projects.title') }}</x-heading>

    @if ($errors->any())
        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-black dark:text-white">- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 2md:grid-cols-[250px_1fr] gap-20 p-4 dark:text-themeLightGray">
        <aside class="bg-white dark:bg-themeBgDark rounded-lg border-2 border-themeLightGray shadow px-4 py-5 space-y-4">
            <form id="filters" action="{{ route('school.projects.index') }}" method="get">

                <!-- Filtros principales -->
                <div class="flex flex-wrap gap-2 mb-4 text-xs md:text-sm lg:text-base
                    [&>input]:bg-white dark:[&>input]:bg-themeBgDark
                    [&>select]:bg-white dark:[&>select]:bg-themeBgDark
                    [&>input]:text-gray-800 dark:[&>input]:text-themeLightGray
                    [&>select]:text-gray-800 dark:[&>select]:text-themeLightGray
                    [&>input]:placeholder:text-black dark:[&>input]:placeholder:text-themeLightGray
                    [&>select]:placeholder:text-themeLightGray
                    [&>input]:rounded-lg [&>select]:rounded-lg
                    [&>input]:border-2 [&>input]:border-themeLightGray
                    [&>select]:border-2 [&>select]:border-themeLightGray
                    [&>input]:px-4 [&>select]:px-4
                    [&>input]:outline-0 [&>select]:outline-0

                    [&>input]:transition-all [&>select]:transition-all
                    [&>input]:duration-300 [&>select]:duration-300
                    [&>input]:ease-in-out [&>select]:ease-in-out
                    [&>input]:hover:shadow-md [&>select]:hover:shadow-md
                    [&>input]:focus:ring-2 [&>select]:focus:ring-2
                    [&>input]:focus:ring-themeBlue [&>select]:focus:ring-themeBlue
                    [&>input]:focus:border-themeBlue [&>select]:focus:border-themeBlue">

                    <x-heading level="h3" class="mb-2.5 w-full">{{ __('messages.school-projects.filters') }}</x-heading>

                    <input type="text" name="title" placeholder="{{ __('messages.school-projects.placeholder-title') }}"
                        value="{{ request('title') }}" class="w-full py-2" />

                    <input type="text" name="author" placeholder="{{ __('messages.school-projects.placeholder-author') }}"
                        value="{{ request('author') }}" class="w-full py-2" />

                    <select name="order" class="w-full py-2 cursor-pointer">
                        <option>{{ __('messages.school-projects.order-by') }}</option>
                        <option value="title" @selected(request('order') == 'title')>
                            {{ __('messages.school-projects.order-title') }}
                        </option>
                        <option value="created_at" @selected(request('order') == 'created_at')>
                            {{ __('messages.school-projects.order-date') }}
                        </option>
                    </select>

                    <select name="direction" class="w-full py-2 cursor-pointer">
                        <option value="asc" @selected(request('direction') == 'asc')>Asc</option>
                        <option value="desc" @selected(request('direction') == 'desc')>Desc</option>
                    </select>
                </div>

                <!-- Año académico -->
                <div class="mb-5">
                    <x-heading level="h3" class="mb-2.5">{{ __('messages.school-projects.academic-year') }}</x-heading>
                    <ul class="space-y-1 text-xs md:text-sm lg:text-base">
                        @foreach (['2024-2025', '2023-2024', '2022-2023', '2021-2022'] as $year)
                            <li>
                                <label
                                    class="flex items-center gap-2 text-gray-800 dark:text-themeLightGray cursor-pointer hover:text-themeBlue transition-colors duration-200">
                                    <input type="checkbox" name="academic_year[]" value="{{ $year }}"
                                        @checked(is_array(request('academic_year')) && in_array($year, request('academic_year')))
                                        class="accent-themeBlue focus:ring-themeBlue transition duration-200">
                                    {{ $year }}
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Categorías -->
                <div>
                    <x-heading level="h3" class="mb-2.5">{{ __('messages.school-projects.categorys') }}</x-heading>
                    <ul class="space-y-1 text-xs md:text-sm lg:text-base">
                        @foreach ([
                            'TFG' => 'TFG',
                            'TFM' => 'TFM',
                            'Tesis' => 'Tesis',
                            'Individual' => 'Individual',
                            'Grupal' => __('messages.school-projects.groupally'),
                            'Tecnología' => __('messages.school-projects.technology'),
                            'Ciencias' => __('messages.school-projects.sciences'),
                            'Artes' => __('messages.school-projects.arts'),
                            'Ingeniería' => __('messages.school-projects.engineering'),
                        ] as $value => $label)
                            <li>
                                <label
                                    class="flex items-center gap-2 text-gray-800 dark:text-themeLightGray cursor-pointer hover:text-themeBlue transition-colors duration-200">
                                    <input type="checkbox" name="tags[]" value="{{ $value }}"
                                        @checked(is_array(request('tags')) && in_array($value, request('tags')))
                                        class="accent-themeBlue focus:ring-themeBlue transition duration-200">
                                    {{ $label }}
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </form>

        </aside>

        <main>

            <div class="bg-white border-2 border-themeLightGray dark:bg-themeBgDark rounded-lg shadow p-4">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b text-xs md:tex-sm lg:text-base">
                            <th class="py-2">{{ __('messages.school-projects.table-name') }}</th>
                            <th class="py-2">{{ __('messages.school-projects.table-dateC') }}</th>
                            <th class="py-2">{{ __('messages.school-projects.table-dateP') }}</th>
                            <th class="py-2">{{ __('messages.school-projects.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($projects->count())
                            @foreach($projects as $project)
                                <tr class="border-b [&>td]:text-xs [&>td]:md:tex-sm [&>td]lg:text-base">
                                    <td class="py-2">
                                        <div class="font-semibold">{{ $project->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $project->author }}</div>
                                    </td>
                                    <td class="py-2">{{ $project->creation_date }}</td>
                                    <td class="py-2">{{ explode(' ', $project->created_at)[0] }}</td>
                                    <td class="py-2 space-x-2 space-y-1" x-data="{ showDelete: false, showEdit: false }" x-cloak
                                        x-init="
                                                             $watch('showDelete', val => document.body.classList.toggle('overflow-hidden', val));
                                                             $watch('showEdit', val => document.body.classList.toggle('overflow-hidden', val));">

                                        <button @click="showDelete = true"
                                            class="px-2 py-1 2md:px-4 2md:py-2 bg-red-600 text-white rounded hover:bg-red-700 transition cursor-pointer">
                                            {{ __('messages.button.delete') }}
                                        </button>

                                        <x-modal @click.outside="showDelete = false" :show="'showDelete'">
                                            <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">
                                                {{ __('messages.school-projects.delete-confirm') }}
                                            </x-heading>
                                            <form action="{{ route('school.projects.destroy', $project->id) }}" method="POST"
                                                class="flex justify-center gap-3">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition cursor-pointer">
                                                    {{ __('messages.school-projects.delete-yes') }}
                                                </button>

                                                <button type="button" @click="showDelete = false"
                                                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition cursor-pointer">
                                                    {{ __('messages.button.cancel') }}
                                                </button>
                                            </form>
                                        </x-modal>

                                        <button @click="showEdit = true"
                                            class="px-2 py-1 2md:px-4 2md:py-2 bg-themeBlue/80 hover:bg-themeBlue text-white rounded transition cursor-pointer">
                                            {{ __('messages.button.edit') }}
                                        </button>

                                        <x-modal @click.outside="showEdit = false" :show="'showEdit'">
                                            <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">
                                                {{ __('messages.school-projects.edit-project') }}
                                            </x-heading>
                                            <form action="{{ route('school.projects.update', $project->id) }}" id="mi-form-update" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')

                                                <div id="form-errors-{{ $project->id }}" class="bg-red-300/70 border border-red-500 text-black dark:text-white p-4 mb-4 rounded hidden">
                                                    <ul id="error-list-{{ $project->id }}" class="list-disc list-inside"></ul>
                                                </div>


                                                <x-label for="title">{{ __('messages.school-projects.label-title') }}</x-label>
                                                <x-inputtext type="text" name="title" id="title"
                                                    value="{{ old('title', $project->title) }}" required />
                                                @error('title')
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror

                                                <x-label for="author">{{ __('messages.school-projects.label-author') }}</x-label>
                                                <x-inputtext type="text" name="author" id="author"
                                                    value="{{ old('author', $project->author) }}" required />
                                                @error('author')
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror

                                                <x-label
                                                    for="date_created">{{ __('messages.school-projects.label-date') }}</x-label>
                                                <input type="date" name="creation_date" id="date_created"
                                                    value="{{ old('creation_date', $project->creation_date) }}"
                                                    class="w-full mb-2 border px-2 py-1 rounded" required>
                                                @error('creation_date')
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror

                                                <x-label
                                                    for="description">{{ __('messages.school-projects.label-description') }}</x-label>
                                                <textarea name="description" id="description"
                                                    class="w-full mb-2 border px-2 py-1 rounded resize-none"
                                                    required>{{ old('description', $project->description) }}</textarea>
                                                @error('description')
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror

                                                <x-label for="tags">{{ __('messages.school-projects.label-tags') }}</x-label>
                                                <select name="tags" id="tags"
                                                    class="w-full px-3 py-2 dark:bg-themeBgDark rounded border border-themeLightGray"
                                                    required>
                                                    <option value="TFG" {{ old('tags', $project->tags) == 'TFG' ? 'selected' : '' }}>
                                                        {{ __('messages.tags.tfg') }}</option>
                                                    <option value="TFM" {{ old('tags', $project->tags) == 'TFM' ? 'selected' : '' }}>
                                                        {{ __('messages.tags.tfm') }}</option>
                                                    <option value="Tesis" {{ old('tags', $project->tags) == 'Tesis' ? 'selected' : '' }}>{{ __('messages.tags.tesis') }}</option>
                                                    <option value="Individual" {{ old('tags', $project->tags) == 'Individual' ? 'selected' : '' }}>{{ __('messages.tags.individual') }}</option>
                                                    <option value="Grupal" {{ old('tags', $project->tags) == 'Grupal' ? 'selected' : '' }}>{{ __('messages.tags.grupal') }}</option>
                                                    <option value="Tecnología" {{ old('tags', $project->tags) == 'Tecnología' ? 'selected' : '' }}>{{ __('messages.tags.tecnología') }}</option>
                                                    <option value="Ciencias" {{ old('tags', $project->tags) == 'Ciencias' ? 'selected' : '' }}>{{ __('messages.tags.ciencias') }}</option>
                                                    <option value="Artes" {{ old('tags', $project->tags) == 'Artes' ? 'selected' : '' }}>{{ __('messages.tags.artes') }}</option>
                                                    <option value="Ingeniería" {{ old('tags', $project->tags) == 'Ingeniería' ? 'selected' : '' }}>{{ __('messages.tags.ingenieria') }}</option>
                                                </select>
                                                @error('tags')
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror

                                                <x-label
                                                    for="general_category">{{ __('messages.school-projects.label-category') }}</x-label>
                                                <select name="general_category" id="general_category"
                                                    class="w-full px-3 py-2 dark:bg-themeBgDark rounded border border-themeLightGray">
                                                    <option value="Administración y negocio" {{ old('general_category', $project->general_category) == 'Administración y negocio' ? 'selected' : '' }}>
                                                        {{ __('messages.projects.option-admin') }}
                                                    </option>
                                                    <option value="Ciencia y salud" {{ old('general_category', $project->general_category) == 'Ciencia y salud' ? 'selected' : '' }}>
                                                        {{ __('messages.projects.option-science') }}
                                                    </option>
                                                    <option value="Comunicación" {{ old('general_category', $project->general_category) == 'Comunicación' ? 'selected' : '' }}>
                                                        {{ __('messages.projects.option-comunication') }}
                                                    </option>
                                                    <option value="Diseño y comunicación" {{ old('general_category', $project->general_category) == 'Diseño y comunicación' ? 'selected' : '' }}>
                                                        {{ __('messages.projects.option-design') }}
                                                    </option>
                                                    <option value="Educación" {{ old('general_category', $project->general_category) == 'Educación' ? 'selected' : '' }}>
                                                        {{ __('messages.projects.option-education') }}
                                                    </option>
                                                    <option value="Industria" {{ old('general_category', $project->general_category) == 'Industria' ? 'selected' : '' }}>
                                                        {{ __('messages.projects.option-industry') }}
                                                    </option>
                                                    <option value="Otro" {{ old('general_category', $project->general_category) == 'Otro' ? 'selected' : '' }}>
                                                        {{ __('messages.projects.option-other') }}
                                                    </option>
                                                    <option value="Tecnología y desarrollo" {{ old('general_category', $project->general_category) == 'Tecnología y desarrollo' ? 'selected' : '' }}>
                                                        {{ __('messages.projects.option-tec') }}
                                                    </option>
                                                </select>

                                                <x-label for="url">{{ __('messages.school-projects.label-link') }}</x-label>
                                                <input type="url" name="link" value="{{ $project->link }}"
                                                    class="w-full mb-2 border px-2 py-1 rounded">

                                                <x-label for="title">{{ __('messages.school-projects.label-highlight') }}</x-label>
                                                <div x-data="{ fileName: '', previewUrl: '' }" x-cloak class="w-full">
                                                    <label for="image-upload-{{ $project->id }}"
                                                        class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape/80 text-white font-medium rounded cursor-pointer hover:bg-themeGrape transition">
                                                        {{ __('messages.school-projects.label-high-upload') }}
                                                        <input id="image-upload-{{ $project->id }}" type="file" name="image"
                                                            accept="image/*" class="hidden" @change="
                                                                                        fileName = $event.target.files[0]?.name || '';
                                                                                        if ($event.target.files[0]) {
                                                                                         const reader = new FileReader();
                                                                                         reader.onload = e => previewUrl = e.target.result;
                                                                                         reader.readAsDataURL($event.target.files[0]);
                                                                                         }" />
                                                    </label>

                                                    <template x-if="fileName">
                                                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">📄
                                                            <span x-text="fileName"></span>
                                                        </p>
                                                    </template>

                                                    <template x-if="previewUrl">
                                                        <img :src="previewUrl" alt="Vista previa"
                                                            class="mt-3 max-h-48 rounded border border-gray-300 shadow" />
                                                    </template>
                                                </div>

                                                <x-label for="title">{{ __('messages.school-projects.label-additional') }}</x-label>
                                                <div x-data="{ fileNames: [] }" x-cloak class="w-full">
                                                    <label for="file-upload-{{ $project->id }}"
                                                        class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape/80 text-white font-medium rounded cursor-pointer hover:bg-themeGrape transition">
                                                        {{ __('messages.school-projects.label-upload') }}
                                                        <input id="file-upload-{{ $project->id }}" name="files[]" type="file"
                                                            multiple accept="file/*" class="hidden"
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

                                                <div class="flex justify-end gap-3 mt-4">
                                                    <button type="button" @click="showEdit = false"
                                                        class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                                                        {{ __('messages.button.cancel') }}
                                                    </button>
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-themeBlue text-white rounded hover:bg-themeBlue/80 transition cursor-pointer">
                                                        {{ __('messages.button.save') }}
                                                    </button>
                                                </div>

                                                <script>
                                                    document.querySelector('form[action="{{ route('school.projects.update', $project->id) }}"]').addEventListener('submit', function (event) {
                                                        const data = {
                                                            title: document.getElementById('title')?.value.trim() || '',
                                                            author: document.getElementById('author')?.value.trim() || '',
                                                            creation_date: document.getElementById('date_created')?.value || '',
                                                            description: document.getElementById('description')?.value.trim() || '',
                                                            tags: document.getElementById('tags')?.value || '',
                                                            general_category: document.getElementById('general_category')?.value || '',
                                                            link: document.querySelector('input[name="link"]')?.value || '',
                                                            image: document.getElementById('image-upload-{{ $project->id }}')?.files[0] || null,
                                                            files: document.getElementById('file-upload-{{ $project->id }}')?.files ? Array.from(document.getElementById('file-upload-{{ $project->id }}').files) : []
                                                        };

                                                        const errors = {};

                                                        // Título
                                                        if (!data.title) {
                                                            errors.title = "{{ __('messages.errors.title.required') }}";
                                                        } else if (data.title.length > 40) {
                                                            errors.title = "{{ __('messages.errors.title.max') }}";
                                                        }

                                                        // Autor
                                                        if (!data.author) {
                                                            errors.author = "{{ __('messages.errors.author.required') }}";
                                                        } else if (data.author.length > 50) {
                                                            errors.author = "{{ __('messages.errors.author.max') }}";
                                                        }

                                                        // Fecha
                                                        if (!data.creation_date) {
                                                            errors.creation_date = "{{ __('messages.errors.creation_date.required') }}";
                                                        } else if (isNaN(Date.parse(data.creation_date))) {
                                                            errors.creation_date = "{{ __('messages.errors.creation_date.date') }}";
                                                        }

                                                        // Descripción
                                                        if (!data.description) {
                                                            errors.description = "{{ __('messages.errors.description.required') }}";
                                                        }

                                                        // Tags
                                                        const validTags = ['TFG', 'TFM', 'Tesis', 'Individual', 'Grupal', 'Tecnología', 'Ciencias', 'Artes', 'Ingeniería'];
                                                        if (!data.tags) {
                                                            errors.tags = "{{ __('messages.errors.tags.required') }}";
                                                        } else if (!validTags.includes(data.tags)) {
                                                            errors.tags = "{{ __('messages.errors.tags.in') }}";
                                                        }

                                                        // Categoría general
                                                        const validCategories = [
                                                            'Administración y negocio',
                                                            'Ciencia y salud',
                                                            'Comunicación',
                                                            'Diseño y comunicación',
                                                            'Educación',
                                                            'Industria',
                                                            'Otro',
                                                            'Tecnología y desarrollo'
                                                        ];
                                                        if (!data.general_category) {
                                                            errors.general_category = "{{ __('messages.errors.sector.required') }}";
                                                        } else if (!validCategories.includes(data.general_category)) {
                                                            errors.general_category = "{{ __('messages.errors.sector.in') }}";
                                                        }

                                                        // Link
                                                        if (data.link) {
                                                            try {
                                                                new URL(data.link);
                                                                if (data.link.length > 255) {
                                                                    errors.link = "{{ __('messages.errors.link.max') }}";
                                                                }
                                                            } catch (_) {
                                                                errors.link = "{{ __('messages.errors.link.url') }}";
                                                            }
                                                        }

                                                        // Imagen
                                                        if (data.image) {
                                                            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                                                            if (!allowedTypes.includes(data.image.type)) {
                                                                errors.image = "{{ __('messages.errors.image.image') }}";
                                                            }
                                                            const extension = data.image.name.split('.').pop().toLowerCase();
                                                            if (!['jpeg', 'png', 'jpg', 'gif'].includes(extension)) {
                                                                errors.image = "{{ __('messages.errors.image.mimes') }}";
                                                            }
                                                            if (data.image.size > 4096 * 1024) {
                                                                errors.image = "{{ __('messages.errors.image.max') }}";
                                                            }
                                                        }

                                                        // Archivos adicionales
                                                        data.files.forEach((file, i) => {
                                                            if (!(file instanceof File)) {
                                                                errors[`file_${i}`] = "{{ __('messages.errors.file.file') }}";
                                                            } else if (file.size > 4096 * 1024) {
                                                                errors[`file_${i}`] = "{{ __('messages.errors.file.max') }}";
                                                            }
                                                        });

                                                        // Mostrar errores
                                                        const errorBox = document.getElementById('form-errors-{{ $project->id }}');
                                                        const errorList = document.getElementById('error-list-{{ $project->id }}');
                                                        errorList.innerHTML = '';

                                                        if (Object.keys(errors).length > 0) {
                                                            event.preventDefault();
                                                            errorBox.classList.remove('hidden');
                                                            Object.values(errors).forEach(msg => {
                                                                const li = document.createElement('li');
                                                                li.textContent = msg;
                                                                errorList.appendChild(li);
                                                            });
                                                        } else {
                                                            errorBox.classList.add('hidden');
                                                        }
                                                    });
                                                </script>

                                            </form>
                                        </x-modal>
                                    </td>
                            @endforeach
                        @else
                                <tr>
                                    <td colspan="4" class="py-4 text-sm md:text-base">{{ __('messages.school-projects.no-projects') }}</td>
                                </tr>
                            @endif
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div x-data="{ showModal: false }" x-cloak
        x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))" class="relative z-10">

        <button @click="showModal = true"
            class="fixed bottom-6 right-6 p-2 bg-themeBlue text-white rounded-full shadow-lg hover:bg-themeHoverBlue transition cursor-pointer">
            <x-icon name="plus" />
        </button>


        <x-modal>
            <x-heading level="h2"
                class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.school-projects.heading-new-project') }}</x-heading>

            <form action="{{ route('school.projects.store') }}" method="POST" id="mi-form" enctype="multipart/form-data"
                class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0">
                @csrf

                <div id="form-errors" class="bg-red-300/70 border border-red-500 text-black dark:text-white p-4 mb-4 rounded hidden">
                    <ul id="error-list" class="list-disc list-inside"></ul>
                </div>


                <div>
                    <x-label for="title">{{ __('messages.school-projects.label-title') }}</x-label>
                    <x-inputtext type="text" name="title" id="title" required />
                </div>

                <div>
                    <x-label for="author">{{ __('messages.school-projects.label-author') }}</x-label>
                    <x-inputtext type="text" name="author" id="author" required />
                </div>

                <div>
                    <x-label for="title">{{ __('messages.school-projects.label-description') }}</x-label>
                    <x-textarea name="description" id="description" required></x-textarea>
                </div>

                <div>
                    <x-label for="tags">{{ __('messages.school-projects.label-tags') }}</x-label>
                    <select name="tags" id="tags"
                        class="w-full h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base dark:bg-themeBgDark rounded border border-themeLightGray cursor-pointer"
                        required>
                        <option value="TFG" {{ old('tags') == 'TFG' ? 'selected' : '' }}>
                            {{ __('messages.tags.tfg') }}</option>
                        <option value="TFM" {{ old('tags') == 'TFM' ? 'selected' : '' }}>
                            {{ __('messages.tags.tfm') }}</option>
                        <option value="Tesis" {{ old('tags') == 'Tesis' ? 'selected' : '' }}>{{ __('messages.tags.tesis') }}</option>
                        <option value="Individual" {{ old('tags') == 'Individual' ? 'selected' : '' }}>{{ __('messages.tags.individual') }}</option>
                        <option value="Grupal" {{ old('tags') == 'Grupal' ? 'selected' : '' }}>{{ __('messages.tags.grupal') }}</option>
                        <option value="Tecnología" {{ old('tags') == 'Tecnología' ? 'selected' : '' }}>{{ __('messages.tags.tecnología') }}</option>
                        <option value="Ciencias" {{ old('tags') == 'Ciencias' ? 'selected' : '' }}>{{ __('messages.tags.ciencias') }}</option>
                        <option value="Artes" {{ old('tags') == 'Artes' ? 'selected' : '' }}>{{ __('messages.tags.artes') }}</option>
                        <option value="Ingeniería" {{ old('tags') == 'Ingeniería' ? 'selected' : '' }}>{{ __('messages.tags.ingenieria') }}</option>
                    </select>
                </div>

                <div>
                    <x-label for="general_category">{{ __('messages.school-projects.label-category') }}</x-label>
                    <select name="general_category" required class="w-full h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base dark:bg-themeBgDark rounded border border-themeLightGray cursor-pointer">
                        <option value="Administración y negocio">{{ __('messages.school-projects.option-admin') }}</option>
                        <option value="Ciencia y salud">{{ __('messages.school-projects.option-science') }}</option>
                        <option value="Comunicación">{{ __('messages.school-projects.option-comunication') }}</option>
                        <option value="Diseño y comunicación">{{ __('messages.school-projects.option-design') }}</option>
                        <option value="Educación">{{ __('messages.school-projects.option-education') }}</option>
                        <option value="Industria">{{ __('messages.school-projects.option-industry') }}</option>
                        <option value="Otro">{{ __('messages.school-projects.option-other') }}</option>
                        <option value="Tecnología y desarrollo">{{ __('messages.school-projects.option-tec') }}</option>
                    </select>
                </div>

                <div>
                    <x-label for="title">{{ __('messages.school-projects.table-dateC') }}</x-label>
                    <x-inputdate name="creation_date" id="creation_date" required />
                </div>

                <div>
                    <x-label for="title">{{ __('messages.school-projects.label-link') }}</x-label>
                    <input type="url" name="link" class="w-full h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base rounded border border-themeLightGray" />
                </div>

                <div>
                    <x-label for="title">{{ __('messages.school-projects.label-highlight') }}</x-label>
                    <div x-data="{ fileName: '', previewUrl: '' }" x-cloak class="w-full">
                        <label for="image-upload"
                            class="flex items-center justify-center w-full px-2 py-1 md:px-4 md:py-2 bg-themeBlue text-white font-medium
                             rounded cursor-pointer hover:bg-themeHoverBlue transition">
                            {{ __('messages.school-projects.label-high-upload') }}
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
                    <x-label for="title">{{ __('messages.school-projects.label-additional') }}</x-label>
                    <div x-data="{ fileNames: [] }" x-cloak class="w-full">
                        <label for="file-upload"
                            class="flex items-center justify-center w-full h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base bg-themeBlue text-white font-medium rounded cursor-pointer hover:bg-themeHoverBlue transition">
                            {{ __('messages.school-projects.label-upload') }}
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
                    <button type="button" @click="showModal = false"
                        class="h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                        {{ __('messages.button.cancel') }}
                    </button>
                    <button type="submit"
                        class="h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base bg-themeBlue text-white rounded hover:bg-themeBlue/80 transition cursor-pointer">
                        {{ __('messages.button.save') }}
                    </button>
                </div>
            </form>
        </x-modal>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('filters');

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

        document.getElementById('mi-form').addEventListener('submit', function (event) {
            const data = {
                title: document.getElementById('title')?.value.trim() || '',
                author: document.getElementById('author')?.value.trim() || '',
                creation_date: document.getElementById('creation_date')?.value || '',
                description: document.getElementById('description')?.value.trim() || '',
                tags: document.getElementById('tags')?.value || '',
                general_category: document.getElementsByName('general_category')[0]?.value || '',
                image: document.getElementById('image-upload')?.files[0] || null,
                files: document.getElementById('file-upload')?.files ? Array.from(document.getElementById('file-upload').files) : []
            };

            const errors = {};

            // Título
            if (!data.title) {
                errors.title = "{{ __('messages.errors.title.required') }}";
            } else if (data.title.length > 40) {
                errors.title = "{{ __('messages.errors.title.max') }}";
            }

            // Autor
            if (!data.author) {
                errors.author = "{{ __('messages.errors.author.required') }}";
            } else if (data.author.length > 50) {
                errors.author = "{{ __('messages.errors.author.max') }}";
            }

            // Fecha
            if (!data.creation_date) {
                errors.creation_date = "{{ __('messages.errors.creation_date.required') }}";
            } else if (isNaN(Date.parse(data.creation_date))) {
                errors.creation_date = "{{ __('messages.errors.creation_date.date') }}";
            }

            // Descripción
            if (!data.description) {
                errors.description = "{{ __('messages.errors.description.required') }}";
            }

            // Tags
            const validTags = ['TFG', 'TFM', 'Tesis', 'Individual', 'Grupal', 'Tecnología', 'Ciencias', 'Artes', 'Ingeniería'];
            if (!data.tags || !validTags.includes(data.tags)) {
                errors.tags = "{{ __('messages.errors.tags.in') }}";
            }

            // Categoría
            const validCategories = [
                'Administración y negocio',
                'Ciencia y salud',
                'Comunicación',
                'Diseño y comunicación',
                'Educación',
                'Industria',
                'Otro',
                'Tecnología y desarrollo'
            ];
            if (!data.general_category) {
                errors.general_category = "{{ __('messages.errors.sector.required') }}";
            } else if (!validCategories.includes(data.general_category)) {
                errors.general_category = "{{ __('messages.errors.sector.in') }}";
            }

            // Imagen
            if (data.image) {
                const allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!allowedImageTypes.includes(data.image.type)) {
                    errors.image = "{{ __('messages.errors.image.image') }}";
                }
                const extension = data.image.name.split('.').pop().toLowerCase();
                if (!['jpeg', 'png', 'jpg', 'gif'].includes(extension)) {
                    errors.image = "{{ __('messages.errors.image.mimes') }}";
                }
                if (data.image.size > 4096 * 1024) {
                    errors.image = "{{ __('messages.errors.image.max') }}";
                }
            }

            // Archivos
            if (data.files.length > 0) {
                data.files.forEach((file, i) => {
                    if (!(file instanceof File)) {
                        errors[`file_${i}`] = "{{ __('messages.errors.file.file') }}";
                    } else if (file.size > 4096 * 1024) {
                        errors[`file_${i}`] = "{{ __('messages.errors.file.max') }}";
                    }
                });
            }

            // Mostrar errores en el formulario
            const errorBox = document.getElementById('form-errors');
            const errorList = document.getElementById('error-list');
            errorList.innerHTML = '';

            if (Object.keys(errors).length > 0) {
                event.preventDefault();
                errorBox.classList.remove('hidden');

                Object.values(errors).forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    errorList.appendChild(li);
                });
            } else {
                errorBox.classList.add('hidden');
            }
        });

    </script>

@endsection