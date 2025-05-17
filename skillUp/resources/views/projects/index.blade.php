@extends('layouts.app')

@section('title', 'Proyectos')

@section('content')
    <x-heading level="h1" class="mb-10">{{ __('messages.projects.title') }}</x-heading>

    <form id="project-filter-form" method="GET" action="{{ route('projects.index') }}" class="mb-16 space-x-5 h-12 w-full [&>input]:h-full [&>select]:h-full
              [&>select]:bg-white [&>input]:bg-white [&>input]:rounded-lg [&>select]:rounded-lg [&>input]:border-2 [&>input]:border-themeLightGray
              [&>select]:border-2 [&>select]:border-themeLightGray [&>select]:px-4 [&>input]:px-4 [&>input]:outline-0 dark:[&>select]:text-themeLightGray [&>input]:placeholder:text-black
              dark:[&>input]:text-themeLightGray dark:[&>input]:placeholder:text-themeLightGray [&>select]:placeholder:text-themeLightGray dark:[&>input]:bg-themeBgDark
              dark:[&>select]:bg-themeBgDark ">

        <input type="text" name="title" placeholder="{{ __('messages.projects.placeholder-title') }}"
            value="{{ request('title') }}">
        <input type="text" name="author" placeholder="{{ __('messages.projects.placeholder-author') }}"
            value="{{ request('author') }}">

        <select name="category">
            <option value="" {{ request('category') === null ? 'selected' : '' }}>{{ __('messages.projects.category') }}</option>
            <option value="Administración y negocio" {{ request('category') === 'Administración y negocio' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-admin') }}</option>
            <option value="Ciencia y salud" {{ request('category') === 'Ciencia y salud' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-science') }}</option>
            <option value="Comunicación" {{ request('category') === 'Comunicación' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-comunication') }}</option>
            <option value="Diseño y comunicación" {{ request('category') === 'Diseño y comunicación' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-design') }}</option>
            <option value="Educación" {{ request('category') === 'Educación' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-education') }}</option>
            <option value="Industria" {{ request('category') === 'Industria' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-industry') }}</option>
            <option value="Otro" {{ request('category') === 'Otro' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-other') }}</option>
            <option value="Tecnología y desarrollo" {{ request('category') === 'Tecnología y desarrollo' ? 'selected' : '' }}>
                {{ __('messages.school-projects.option-tec') }}</option>
        </select>

        <select name="order">
            <option value="">{{ __('messages.projects.order-by') }}</option>
            <option value="title" {{ request('order') === 'title' ? 'selected' : '' }}>
                {{ __('messages.projects.order-title') }}</option>
            <option value="creation_date" {{ request('order') === 'creation_date' ? 'selected' : '' }}>
                {{ __('messages.projects.order-date') }}</option>
            <option value="general_category" {{ request('order') === 'general_category' ? 'selected' : '' }}>
                {{ __('messages.projects.order-category') }}
            </option>
        </select>

        <select name="direction">
            <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Asc</option>
            <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Desc</option>
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
                <x-card class="h-full hover:border-themeBlue hover:scale-101 transition">
                    <li class="flex flex-col h-full ">
                        <x-tags class="mb-2">{{ $project->tags }}</x-tags>
                        <x-heading level="h3" class="mb-1">{{ $project->title }}</x-heading>
                        <span class="mb-6">{{ $project->general_category }}</span>

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
            <p>{{ __('messages.projects.no-projects') }}.</p>
        @endforelse
    </ul>

    <div x-cloak x-data="{ showModal: false }"
        x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))" class="relative z-10">

        <button @click="showModal = true"
            class="fixed bottom-6 right-6 p-2 bg-themeBlue text-white rounded-full shadow-lg hover:bg-themeHoverBlue transition cursor-pointer">
            <x-icon size="6" name="plus" />
        </button>


        <x-modal>
            <x-heading level="h2"
                class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.projects.new-project') }}</x-heading>

            <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0">
                @csrf

                <div>
                    <x-label for="title">{{ __('messages.projects.label-title') }}</x-label>
                    <x-inputtext type="text" name="title" id="title" required />
                </div>

                <div>
                    <x-label for="description">{{ __('messages.projects.label-description') }}</x-label>
                    <x-textarea name="description" id="description" required></x-textarea>
                </div>

                <div>
                    <x-label for="tags">{{ __('messages.projects.label-tags') }}</x-label>
                    <x-inputtext type="text" name="tags" id="tags" required />
                </div>

                <div>
                    <x-label for="general_category">{{ __('messages.projects.category') }}:</x-label>
                    <select name="sector_category" required
                        class="w-full px-3 py-2 dark:bg-themeBgDark rounded border border-themeLightGray">
                        <option value="Administración y negocio">{{ __('messages.projects.option-admin') }}</option>
                        <option value="Ciencia y salud">{{ __('messages.projects.option-science') }}</option>
                        <option value="Comunicación">{{ __('messages.projects.option-comunication') }}</option>
                        <option value="Diseño y comunicación">{{ __('messages.projects.option-design') }}</option>
                        <option value="Educación">{{ __('messages.projects.option-education') }}</option>
                        <option value="Industria">{{ __('messages.projects.industry') }}</option>
                        <option value="Otro">{{ __('messages.projects.option-other') }}</option>
                        <option value="Tecnología y desarrollo">{{ __('messages.projects.option-tec') }}</option>
                    </select>
                </div>

                <div>
                    <x-label for="title">{{ __('messages.projects.label-date') }}</x-label>
                    <x-inputdate name="creation_date" id="creation_date" required />
                </div>

                <div>
                    <x-label for="title">{{ __('messages.projects.label-link') }}</x-label>
                    <input type="url" name="link" class="w-full px-3 py-2 rounded border border-themeLightGray" />
                </div>

                <div>
                    <x-label for="title">{{ __('messages.projects.label-image') }}</x-label>
                    <div x-data="{ fileName: '', previewUrl: '' }" class="w-full" x-cloak>
                        <label for="image-upload"
                            class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/90 transition">
                            🖼️ {{ __('messages.projects.label-highlight') }}
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
                            class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/90 transition">
                            📎 {{ __('messages.projects.label-upload') }}
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

    <br>

    <x-heading level="h1" class="mb-10">{{ __('messages.school-projects.title') }}</x-heading>
    <ul class="grid grid-cols-3 gap-10">
        @forelse ($schoolProjects as $school)
            <a href="{{ route('school.projects.show', $school->id) }}">
                <x-card class="h-full hover:border-themeBlue hover:scale-101 transition">
                    <li class="flex flex-col">
                        <x-tags class="mb-2">{{ $school->tags }}</x-tags>
                        <x-heading level="h3" class="mb-1">{{ $school->title }}</x-heading>
                        <span>{{ $school->general_category }}</span>
                        <p class=" text-sm break-words mb-1.5">{{ Str::limit($school->description, 100) }}</p>

                        @php
                            $favorite = auth()->user()->favorites()
                                ->where('type', 'proyecto')
                                ->where('reference_id', $school->id)
                                ->first();
                        @endphp

                        <div class="flex flex-row justify-between items-center mt-auto">
                            <div class="flex flex-row gap-3 items-center justify-center">
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
                                        <input type="hidden" name="type" value="proyecto">
                                        <input type="hidden" name="reference_id" value="{{ $school->id }}">
                                        <button type="submit"
                                            class="text-themeRed hover:scale-110 transition-transform duration-200 cursor-pointer">
                                            <x-icon name="heart" class="w-5 h-auto" />
                                        </button>
                                    </form>
                                @endif
                                <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                                        class="w-4 h-auto" />{{ $school->views }}</p>
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
            <p>{{ __('messages.school-projects.no-projects') }}.</p>
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