@extends('layouts.app')

@section('title', 'Tus proyectos')

@section('content')
    <x-heading level="h1" class="mb-10">{{ __('messages.your-projects.title') }}</x-heading>
    <form id="own-projects-filter-form" method="GET" action="{{ route('projects.ownProjects') }}"
    class="space-y-2 lg:mb-9 space-x-5 h-8 sm:h-10 lg:h-12 w-full
        [&>input]:h-full [&>select]:h-full
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

        <input type="text" name="title" placeholder="{{ __('messages.your-projects.placeholder-title') }}"
            value="{{ request('title') }}">
        <input type="text" name="description" placeholder="{{ __('messages.your-projects.placeholder-description') }}"
            value="{{ request('description') }}">

        <select name="category" class="cursor-pointer">
            <option value="">{{ __('messages.your-projects.category') }}</option>
            <option value="Administración y negocio" @selected(request('category') == 'Administración y negocio')>
                {{ __('messages.your-projects.option-admin') }}
            </option>
            <option value="Ciencia y salud" @selected(request('category') == 'Ciencia y salud')>
                {{ __('messages.your-projects.option-science') }}</option>
            <option value="Comunicación" @selected(request('category') == 'Comunicación')>
                {{ __('messages.your-projects.option-communication') }}</option>
            <option value="Diseño y comunicación" @selected(request('category') == 'Diseño y comunicación')>
                {{ __('messages.your-projects.option-design') }}</option>
            <option value="Educación" @selected(request('category') == 'Educación')>
                {{ __('messages.your-projects.option-education') }}</option>
            <option value="Industria" @selected(request('category') == 'Industria')>
                {{ __('messages.your-projects.option-industry') }}</option>
            <option value="Otro" @selected(request('category') == 'Otro')>{{ __('messages.your-projects.option-other') }}
            </option>
            <option value="Tecnología y desarrollo" @selected(request('category') == 'Tecnología y desarrollo')>
                {{ __('messages.your-projects.option-tec') }}</option>
        </select>

        <select name="order" class="cursor-pointer">
            <option value="">{{ __('messages.your-projects.order-by') }}</option>
            <option value="name" @selected(request('order') == 'name')>{{ __('messages.your-projects.order-name') }}</option>
            <option value="creation_date" @selected(request('order') == 'creation_date')>
                {{ __('messages.your-projects.order-date') }}</option>
            <option value="general_category" @selected(request('order') == 'general_category')>
                {{ __('messages.your-projects.order-category') }}</option>
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

    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 w-full mt-36 2md:mt-28 lg:mt-20 xl:mt-0">

        @forelse ($userProjects as $project)
            <a href="{{ route('projects.show', $project->id) }}">
                <x-card class="h-full border border-transparent hover:border-themeBlue shadow-md hover:shadow-lg hover:scale-[1.01] transition-all duration-300 ease-in-out bg-white dark:bg-themeDark text-gray-800 dark:text-white">
                    <li class="flex flex-col h-full ">
                        <x-tags class="mb-2">{{ $project->tags }}</x-tags>
                        <x-heading level="h3" class="mb-1">{{ $project->title }}</x-heading>
                        <span>{{ $project->general_category }}</span>
                        <p class=" text-sm mb-1.5">{{ Str::limit($project->description, 100) }}</p>

                        <div class="flex flex-row justify-between items-center mt-auto">
                            <div class="flex flex-row gap-3">
                                <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                                        class="w-4 h-auto" />{{ $project->views }}</p>
                                <p>
                                    {{ $project->averageRating() ? number_format($project->averageRating(), 1) : __('messages.your-projects.no-califications') }}
                                </p>
                            </div>
                            <span class="text-sm">{{ $project->author->name . ' ' . $project->author->last_name  }}</span>
                        </div>

                    </li>
                </x-card>
            </a>
        @empty
            <p>{{ __('messages.your-projects.no-projects') }}</p>
        @endforelse
    </ul>

    <div x-data="{ showModal: false }" x-cloak
        x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))" class="relative z-10">

        <button @click="showModal = true"
            class="fixed bottom-6 right-6 p-2 bg-themeBlue text-white rounded-full shadow-lg hover:bg-themeHoverBlue cursor-pointer transition">
            <x-icon name="plus" />
        </button>


        <x-modal>
            <x-heading level="h2"
                class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.your-projects.new-project') }}</x-heading>

            <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0">
                @csrf

                <div>
                    <x-label for="title">{{ __('messages.your-projects.label-title') }}</x-label>
                    <x-inputtext type="text" name="title" id="title" required />
                </div>

                <div>
                    <x-label for="title">{{ __('messages.your-projects.label-description') }}</x-label>
                    <x-textarea name="description" id="description" required></x-textarea>
                </div>

                <div>
                    <x-label for="title">{{ __('messages.your-projects.label-tags') }}</x-label>
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
                            {{ __('messages.tags.tecnologia') }}</option>
                        <option value="Ciencias" {{ old('tags') == 'Ciencias' ? 'selected' : '' }}>
                            {{ __('messages.tags.ciencias') }}</option>
                        <option value="Artes" {{ old('tags') == 'Artes' ? 'selected' : '' }}>{{ __('messages.tags.artes') }}
                        </option>
                        <option value="Ingeniería" {{ old('tags') == 'Ingeniería' ? 'selected' : '' }}>
                            {{ __('messages.tags.ingenieria') }}</option>
                    </select>
                </div>

                <div>
                    <x-label for="title">{{ __('messages.your-projects.label-category') }}</x-label>
                    <select name="sector_category" required
                        class="w-full h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base rounded border dark:bg-themeBgDark border-themeLightGray">
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
                    <x-label for="title">{{ __('messages.your-projects.label-date') }}</x-label>
                    <x-inputdate name="creation_date" id="creation_date" required />
                </div>

                <div>
                    <x-label for="title">{{ __('messages.your-projects.label-link') }}</x-label>
                    <input type="url" name="link"
                        class="w-full h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base rounded border border-themeLightGray" />
                </div>

                <div>
                    <x-label for="title">{{ __('messages.your-projects.label-image') }}</x-label>
                    <div x-data="{ fileName: '', previewUrl: '' }" class="w-full" x-cloak>
                        <label for="image-upload"
                            class="flex items-center justify-center w-full h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/80 transition">
                            {{ __('messages.your-projects.label-highlight') }}
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
                    <x-label for="title">{{ __('messages.your-projects.label-additional') }}</x-label>
                    <div x-data="{ fileNames: [] }" class="w-full" x-cloak>
                        <label for="file-upload"
                            class="flex items-center justify-center w-full h-8 sm:h-10 px-3 py-2 text-xs md:tex-sm lg:text-base bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/80 transition">
                            {{ __('messages.your-projects.label-upload') }}
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