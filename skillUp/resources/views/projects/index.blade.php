@extends('layouts.app')

@section('title', 'Proyectos')

@section('content')
    <x-heading level="h1" class="mb-10">{{ __('messages.projects.title') }}</x-heading>

    <form id="project-filter-form" method="GET" action="{{ route('projects.index') }}" class="h-8 sm:h-10 mb-36 text-sm space-y-2 lg:mb-16 lg:space-x-5 lg:h-12 w-full
            [&>input]:h-full [&>select]:h-full
            [&>select]:bg-white [&>input]:bg-white
            [&>input]:rounded-lg [&>select]:rounded-lg
            [&>input]:border-2 [&>input]:border-themeLightGray
            [&>select]:border-2 [&>select]:border-themeLightGray
            [&>select]:px-4 [&>input]:px-4
            [&>input]:outline-0
            dark:[&>select]:text-themeLightGray
            [&>input]:placeholder:text-black
            dark:[&>input]:text-themeLightGray dark:[&>input]:placeholder:text-themeLightGray
            [&>select]:placeholder:text-themeLightGray
            dark:[&>input]:bg-themeBgDark dark:[&>select]:bg-themeBgDark

            [&>input]:transition-all [&>select]:transition-all
            [&>input]:duration-300 [&>select]:duration-300
            [&>input]:ease-in-out [&>select]:ease-in-out
            [&>input]:hover:shadow-md [&>select]:hover:shadow-md
            [&>input]:focus:ring-2 [&>select]:focus:ring-2
            [&>input]:focus:ring-themeBlue [&>select]:focus:ring-themeBlue
            [&>input]:focus:border-themeBlue [&>select]:focus:border-themeBlue">

        <input type="text" name="title" placeholder="{{ __('messages.projects.placeholder-title') }}"
            value="{{ request('title') }}">

        <input type="text" name="author" placeholder="{{ __('messages.projects.placeholder-author') }}"
            value="{{ request('author') }}">

        <select name="category" class="cursor-pointer [&>option]:cursor-pointer">
            <option value="" {{ request('category') === null ? 'selected' : '' }}>{{ __('messages.projects.category') }}
            </option>
            <option value="Administración y negocio" {{ request('category') === 'Administración y negocio' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-admin') }}
            </option>
            <option value="Ciencia y salud" {{ request('category') === 'Ciencia y salud' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-science') }}
            </option>
            <option value="Comunicación" {{ request('category') === 'Comunicación' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-comunication') }}
            </option>
            <option value="Diseño y comunicación" {{ request('category') === 'Diseño y comunicación' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-design') }}
            </option>
            <option value="Educación" {{ request('category') === 'Educación' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-education') }}
            </option>
            <option value="Industria" {{ request('category') === 'Industria' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-industry') }}
            </option>
            <option value="Otro" {{ request('category') === 'Otro' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-other') }}
            </option>
            <option value="Tecnología y desarrollo" {{ request('category') === 'Tecnología y desarrollo' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-tec') }}
            </option>
        </select>

        <select name="order" class="cursor-pointer [&>option]:cursor-pointer">
            <option value="">{{ __('messages.projects.order-by') }}</option>
            <option value="title" {{ request('order') === 'title' ? 'selected' : '' }}>
                {{ __('messages.projects.order-title') }}
            </option>
            <option value="creation_date" {{ request('order') === 'creation_date' ? 'selected' : '' }}>
                {{ __('messages.projects.order-date') }}
            </option>
            <option value="general_category" {{ request('order') === 'general_category' ? 'selected' : '' }}>
                {{ __('messages.projects.order-category') }}
            </option>
        </select>

        <select name="direction" class="cursor-pointer">
            <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Asc</option>
            <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Desc</option>
        </select>

    </form>



    @if ($errors->any())
        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-black dark:text-white">- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 w-full">
        @forelse ($projects as $project)
            <a href="{{ route('projects.show', $project->id) }}">
                <x-card
                    class="h-full border border-transparent hover:border-themeBlue shadow-md hover:shadow-lg hover:scale-[1.01] transition-all duration-300 ease-in-out bg-white dark:bg-themeDark text-gray-800 dark:text-white">
                    <li class="flex flex-col h-full ">
                        <x-tags>{{ __('messages.tags.' . strtolower($project->tags)) }}</x-tags>
                        <x-heading level="h3" class="mb-1">{{ $project->title }}</x-heading>
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

                            $categoryKey = $categoryMap[$project->general_category] ?? null;
                        @endphp

                        @if ($categoryKey)
                            <x-heading level="h4" class="mb-4">
                                {{ __('messages.projects.' . $categoryKey) }}
                            </x-heading>
                        @endif

                        @php
                            $favorite = auth()->user()->favorites()
                                ->where('type', 'proyecto')
                                ->where('reference_id', $project->id)
                                ->first();
                        @endphp

                        <div class="flex flex-row justify-between items-center mt-auto">
                            <div class="flex flex-row gap-3 ">
                                @if ($favorite)
                                    <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-themeRed hover:scale-110 transition-transform duration-200 cursor-pointer"><x-icon
                                                name="filled-heart" class="w-5 h-auto" /></button>
                                    </form>
                                @else
                                    <form action="{{ route('favorites.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="proyecto">
                                        <input type="hidden" name="reference_id" value="{{ $project->id }}">
                                        <button type="submit"
                                            class="text-themeRed hover:scale-110 transition-transform duration-200 cursor-pointer">
                                            <x-icon name="heart" class="w-5 h-auto" />
                                        </button>
                                    </form>
                                @endif
                                <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                                        class="w-4 h-auto" />{{ $project->views }}</p>
                                <p class="flex justify-center text-center ">
                                    <x-icon name="star" class="w-4 h-auto text-yellow-400 mr-1" />
                                    {{ $project->averageRating() ? number_format($project->averageRating(), 1) : 'N/A' }}
                                </p>
                            </div>
                            <span class="text-sm">{{ $project->author->name . ' ' . $project->author->last_name  }}</span>
                        </div>

                    </li>
                </x-card>
            </a>
        @empty
            <p class="col-span-1 md:col-span-2 lg:col-span-3">{{ __('messages.projects.no-projects') }}.</p>
        @endforelse
    </ul>

    @if (auth()->user()->role !== 'Admin')
        <div x-cloak x-data="{ showModal: false }"
            x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))" class="relative z-10">

            <button @click="showModal = true"
                class="fixed bottom-6 right-6 p-2 bg-themeBlue text-white rounded-full shadow-lg hover:bg-themeHoverBlue transition cursor-pointer">
                <x-icon size="6" name="plus" />
            </button>


            <x-modal>
                <x-heading level="h2"
                    class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.projects.new-project') }}</x-heading>

                <form action="{{ route('projects.store') }}" id="mi-form" method="POST" enctype="multipart/form-data"
                    class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0 text-xs md:tex-sm lg:text-base">
                    @csrf

                    <div id="form-errors"
                        class="bg-red-300/70 border border-red-500 text-black dark:text-white p-4 mb-4 rounded hidden">
                        <ul id="error-list" class="list-disc list-inside"></ul>
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.projects.label-title') }}</x-label>
                        <x-inputtext type="text" name="title" id="title" value="{{ old('title') }}" required />
                    </div>

                    <div>
                        <x-label for="description">{{ __('messages.projects.label-description') }}</x-label>
                        <x-textarea name="description" id="description" required>{{ old('description') }}</x-textarea>
                    </div>

                    <div>
                        <x-label for="tags">{{ __('messages.projects.label-tags') }}</x-label>
                        <select name="tags" id="tags"
                            class="w-full h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base dark:bg-themeBgDark rounded border border-themeLightGray"
                            required>
                            <option value="TFG" {{ old('tags') == 'TFG' ? 'selected' : '' }}>{{ __('messages.tags.tfg') }}
                            </option>
                            <option value="TFM" {{ old('tags') == 'TFM' ? 'selected' : '' }}>{{ __('messages.tags.tfm') }}
                            </option>
                            <option value="Tesis" {{ old('tags') == 'Tesis' ? 'selected' : '' }}>{{ __('messages.tags.tesis') }}
                            </option>
                            <option value="Individual" {{ old('tags') == 'Individual' ? 'selected' : '' }}>
                                {{ __('messages.tags.individual') }}</option>
                            <option value="Grupal" {{ old('tags') == 'Grupal' ? 'selected' : '' }}>
                                {{ __('messages.tags.grupal') }}</option>
                            <option value="Tecnología" {{ old('tags') == 'Tecnología' ? 'selected' : '' }}>
                                {{ __('messages.tags.tecnología') }}</option>
                            <option value="Ciencias" {{ old('tags') == 'Ciencias' ? 'selected' : '' }}>
                                {{ __('messages.tags.ciencias') }}</option>
                            <option value="Artes" {{ old('tags') == 'Artes' ? 'selected' : '' }}>{{ __('messages.tags.artes') }}
                            </option>
                            <option value="Ingeniería" {{ old('tags') == 'Ingeniería' ? 'selected' : '' }}>
                                {{ __('messages.tags.ingenieria') }}</option>
                        </select>
                    </div>

                    <div>
                        <x-label for="general_category">{{ __('messages.projects.category') }}:</x-label>
                        <select name="sector_category" id="sector_category" required
                            class="w-full h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base dark:bg-themeBgDark rounded border border-themeLightGray">
                            <option value="Administración y negocio" {{ old('sector_category') == 'Administración y negocio' ? 'selected' : '' }}>{{ __('messages.projects.option-admin') }}</option>
                            <option value="Ciencia y salud" {{ old('sector_category') == 'Ciencia y salud' ? 'selected' : '' }}>
                                {{ __('messages.projects.option-science') }}</option>
                            <option value="Comunicación" {{ old('sector_category') == 'Comunicación' ? 'selected' : '' }}>
                                {{ __('messages.projects.option-comunication') }}</option>
                            <option value="Diseño y comunicación" {{ old('sector_category') == 'Diseño y comunicación' ? 'selected' : '' }}>{{ __('messages.projects.option-design') }}</option>
                            <option value="Educación" {{ old('sector_category') == 'Educación' ? 'selected' : '' }}>
                                {{ __('messages.projects.option-education') }}</option>
                            <option value="Industria" {{ old('sector_category') == 'Industria' ? 'selected' : '' }}>
                                {{ __('messages.projects.option-industry') }}</option>
                            <option value="Otro" {{ old('sector_category') == 'Otro' ? 'selected' : '' }}>
                                {{ __('messages.projects.option-other') }}</option>
                            <option value="Tecnología y desarrollo" {{ old('sector_category') == 'Tecnología y desarrollo' ? 'selected' : '' }}>{{ __('messages.projects.option-tec') }}</option>
                        </select>
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.projects.label-date') }}</x-label>
                        <x-inputdate name="creation_date" id="creation_date" required />
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.projects.label-link') }}</x-label>
                        <input type="url" name="link" id="link"
                            class="w-full h-8 sm:h-10 px-3 py-2 rounded border border-themeLightGray" />
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.projects.label-image') }}</x-label>
                        <div x-data="{ fileName: '', previewUrl: '' }" class="w-full" x-cloak>
                            <label for="image-upload"
                                class="flex items-center justify-center w-full h-8 sm:h-10 px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/90 transition">
                                <div class="flex gap-2">
                                    <x-icon name="image" class="w-5 h-auto" />
                                {{ __('messages.projects.label-highlight') }}
                                </div>
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
                        <x-label for="title">{{ __('messages.projects.label-additional') }}</x-label>
                        <div x-data="{ fileNames: [] }" class="w-full" x-cloak>
                            <label for="file-upload"
                                class="flex items-center justify-center w-fullh-8 sm:h-10 px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/90 transition">
                                <div class="flex gap-2">
                                    <x-icon name="clip" class="w-5 h-auto" />
                                    {{ __('messages.projects.label-upload') }}
                                </div> 
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
                            class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                            {{ __('messages.button.cancel') }}
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-themeBlue text-white rounded hover:bg-themeBlue/80 transition cursor-pointer">
                            {{ __('messages.button.save') }}
                        </button>
                    </div>
                </form>
                </form>
            </x-modal>

        </div>
    @endif

    <br>

    <x-heading level="h1" class="mb-10">{{ __('messages.navbar.school-projects') }}</x-heading>
    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 w-full">
        @forelse ($schoolProjects as $school)
            <a href="{{ route('school.projects.show', $school->id) }}">
                <x-card
                    class="h-full border border-transparent hover:border-themeBlue shadow-md hover:shadow-lg hover:scale-[1.01] transition-all duration-300 ease-in-out bg-white dark:bg-themeDark text-gray-800 dark:text-white">

                    <li class="flex flex-col h-full">
                        <x-tags>{{ __('messages.tags.' . strtolower(string: $school->tags)) }}</x-tags>
                        <x-heading level="h3" class="mb-1">{{ $school->title }}</x-heading>
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

                            $categoryKey = $categoryMap[$school->general_category] ?? null;
                        @endphp

                        @if ($categoryKey)
                            <span class="mb-6">{{ __('messages.projects.' . $categoryKey) }}</span>
                        @endif

                        @php
                            $favorite = auth()->user()->favorites()
                                ->where('type', 'proyectoEscolar')
                                ->where('reference_id', $school->id)
                                ->first();
                        @endphp

                        <div class="flex flex-row justify-between items-center mt-auto">
                            <div class="flex flex-row gap-4 items-center justify-center">
                                @if ($favorite)
                                    <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-themeRed hover:scale-110 transition-transform duration-200 cursor-pointer">
                                            <x-icon name="filled-heart" class="w-5 h-auto" />
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('favorites.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="proyectoEscolar">
                                        <input type="hidden" name="reference_id" value="{{ $school->id }}">
                                        <button type="submit"
                                            class="text-themeRed hover:scale-110 transition-transform duration-200 cursor-pointer">
                                            <x-icon name="heart" class="w-5 h-auto" />
                                        </button>
                                    </form>
                                @endif
                                <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                                        class="w-4 h-auto" />{{ $school->views }}</p>
                                <p class="flex justify-center text-center ">
                                    <x-icon name="star" class="w-4 h-auto text-yellow-400 mr-1" />
                                    {{ $school->averageRating() ? number_format($school->averageRating(), 1) : 'N/A' }}
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
            <p class="col-span-1 md:col-span-2 lg:col-span-3">{{ __('messages.school-projects.no-projects') }}.</p>
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

        function validateForm(data) {
            const errors = {};

            if (!data.title) {
                errors.title = "{{ __('messages.errors.title.required') }}";
            } else if (typeof data.title !== 'string') {
                errors.title = "{{ __('messages.errors.title.string') }}";
            } else if (data.title.length > 40) {
                errors.title = "{{ __('messages.errors.title.max') }}";
            }

            if (!data.description) {
                errors.description = "{{ __('messages.errors.description.required') }}";
            } else if (typeof data.description !== 'string') {
                errors.description = "{{ __('messages.errors.description.string') }}";
            } else if (data.description.length > 300) {
                errors.description = "{{ __('messages.errors.description.max') }}";
            }

            const validTags = ['TFG', 'TFM', 'Tesis', 'Individual', 'Grupal', 'Tecnología', 'Ciencias', 'Artes', 'Ingeniería'];
            if (!data.tags) {
                errors.tags = "{{ __('messages.errors.tags.required') }}";
            } else if (!validTags.includes(data.tags)) {
                errors.tags = "{{ __('messages.errors.tags.in') }}";
            }

            const validSectors = [
                'Administración y negocio',
                'Ciencia y salud',
                'Comunicación',
                'Diseño y comunicación',
                'Educación',
                'Industria',
                'Otro',
                'Tecnología y desarrollo'
            ];
            if (!data.sector_category) {
                errors.sector_category = "{{ __('messages.errors.sector.required') }}";
            } else if (!validSectors.includes(data.sector_category)) {
                errors.sector_category = "{{ __('messages.errors.sector.in') }}";
            }

            if (!data.creation_date) {
                errors.creation_date = "{{ __('messages.errors.creation_date.required') }}";
            } else if (isNaN(Date.parse(data.creation_date))) {
                errors.creation_date = "{{ __('messages.errors.creation_date.date') }}";
            }

            if (data.link) {
                try {
                    const url = new URL(data.link);
                    if (data.link.length > 255) {
                        errors.link = "{{ __('messages.errors.link.max') }}";
                    }
                } catch (e) {
                    errors.link = "{{ __('messages.errors.link.url') }}";
                }
            }

            if (data.image) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!allowedTypes.includes(data.image.type)) {
                    errors.image = "{{ __('messages.errors.image.image') }}";
                }
                if (data.image.size > 4096 * 1024) {
                    errors.image = "{{ __('messages.errors.image.max') }}";
                }
                const extension = data.image.name.split('.').pop().toLowerCase();
                if (!['jpeg', 'png', 'jpg', 'gif'].includes(extension)) {
                    errors.image = "{{ __('messages.errors.image.mimes') }}";
                }
            }

            if (data.files && Array.isArray(data.files)) {
                data.files.forEach((file, i) => {
                    if (!(file instanceof File)) {
                        errors[`files_${i}`] = "{{ __('messages.errors.file.file') }}";
                    } else if (file.size > 4096 * 1024) {
                        errors[`files_${i}`] = "{{ __('messages.errors.file.max') }}";
                    }
                });
            }

            return errors;
        }

        document.getElementById('mi-form').addEventListener('submit', function (event) {
            const formData = {
                title: document.getElementById('title')?.value || '',
                description: document.getElementById('description')?.value || '',
                tags: document.getElementById('tags')?.value || '',
                sector_category: document.getElementById('sector_category')?.value || '',
                creation_date: document.getElementById('creation_date')?.value || '',
                link: document.getElementById('link')?.value || '',
                image: document.getElementById('image')?.files[0] || null,
                files: document.getElementById('file-upload')?.files ? Array.from(document.getElementById('file-upload').files) : []
            };


            const errors = validateForm(formData);

            const errorBox = document.getElementById('form-errors');
            const errorList = document.getElementById('error-list');

            if (Object.keys(errors).length > 0) {
                event.preventDefault();

                errorList.innerHTML = '';
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