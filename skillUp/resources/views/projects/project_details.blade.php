@extends('layouts.app')

@section('title', 'Detalles del proyecto')

@section('content')
    <div x-data="{ selectedImage: null }">

        <x-heading level="h1" class="mb-10">{{ __('messages.project-details.title')  }}</x-heading>
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
            <x-tags>{{ __('messages.tags.' . strtolower($project->tags)) }}</x-tags>

            <x-heading level="h2" class="mt-6 mb-3">{{ $project->title }}</x-heading>
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

            
            @if ($project->image)
            <img src="{{ Storage::disk('s3')->url($project->image) }}" alt="Imagen del proyecto" class="w-[80%] mx-auto h-auto rounded-lg shadow-md">
            @endif
            
            <p class="mb-9 break-words">{{ $project->description }}</p>

            <div class="grid grid-cols-1 2md:flex 2md:justify-between mt-16">
                <div
                    class="flex gap-4 items-center justify-start 2md:justify-center mb-4 2md:mb-0 text-xs md:text-sm 2md:text-base">
                    <p class="flex gap-2">
                        <x-icon name="user" class="w-5 h-auto" />
                        <a href="{{ route('profile.public', ['id' => $project->author->id]) }}" class="text-themeBlue hover:underline font-semibold">
                            {{ $project->author->name . ' ' . $project->author->last_name }}
                        </a>
                    </p>
                    <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                            class="w-4 h-auto" />{{ $project->views }}</p>
                    <p class="flex items-center justify-center gap-1">
                        <label class="text-yellow-400"><x-icon name="star" class="w-3 md:w-4 h-auto" /></label>
                        {{ $project->averageRating() ? number_format($project->averageRating(), 1) : 'N/A' }}
                    </p>
                    @auth
                        <form id="rating-form" action="{{ route('projects.rate', $project->id) }}" method="POST"
                            class="flex gap-1">
                            @csrf
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="submit" name="rating" value="{{ $i }}"
                                    class="text-3xl focus:outline-none transition transform hover:scale-110 cursor-pointer hover:text-yellow-400
                                                                                {{ $project->getRatingByUser(auth()->id()) && $project->getRatingByUser(auth()->id())->rating >= $i ? 'text-yellow-400' : 'text-gray-400' }}"
                                    aria-label="Valorar con {{ $i }} estrella{{ $i > 1 ? 's' : '' }}">
                                    <x-icon name="star" class="w-3 md:w-4 h-auto" />
                                </button>
                            @endfor
                        </form>

                    @endauth
                </div>
                <div class="flex flex-col justify-end [&>p]:text-black dark:[&>p]:text-themeLightGray">
                    <p class="text-sm text-gray-500">{{ __('messages.project-details.published') . $project->created_at }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ __('messages.project-details.created-at') . $project->creation_date }}
                    </p>
                </div>
            </div>

        </x-card>


        <x-card>
            <x-heading level="h2" class="mt-2 mb-3">{{ __('messages.project-details.project-files')  }}</x-heading>

            @if($project->link)
                <p class="text-xs md:tex-sm lg:text-base"><strong>{{ __('messages.project-details.link')  }}</strong> <a href="{{ $project->link }}"
                        target="_blank">{{ $project->link }}</a></p>
            @endif

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
                                        class="text-xs md:tex-sm lg:text-base block bg-themeGrape/80 p-1 lg:p-3 rounded shadow text-center dark:bg-themeDarkGray hover:bg-themeGrape">
                                        {{ __('messages.project-details.see-image') }}
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $img->path) }}" download
                                        class="text-xs md:tex-sm lg:text-base block bg-themeGrape/80 p-1 lg:p-3 rounded shadow text-center hover:bg-themeGrape">
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

            @include('comments.comment_section', ['commentable' => $project, 'type' => 'project'])
        </x-card>

        <div class="flex gap-2 items-center mt-3">
            @if (auth()->id() === $project->author->id)
                <div x-data="{ open: false }" class="inline-block" x-cloak>
                    <button @click="open = true"
                    class="dark:bg-themeBgDark bg-white border-2 border-themeRed hover:bg-themeRed/20 text-themeRed font-semibold px-2 py-1 2md:px-4 2md:py-2 text-xs lg:text-sm rounded transition cursor-pointer">{{
                    __('messages.button.delete') }}</button>

                    <div x-show="open" x-cloak
                    class="fixed inset-0 bg-black/50 flex items-center justify-center px-10 z-50">
                        <div class="bg-white dark:bg-themeBgDark p-6 rounded shadow-lg w-full max-w-md"
                        @click.outside="open = false">
                            <x-heading level="h2"
                            class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{__('messages.admin.users.heading-confirm')}}</x-heading>
                            <p class="mb-4 text-gray-600 dark:text-gray-300 break-words">
                                {{__('messages.admin.projects.delete-text-1')}} <strong>{{ $project->name }}</strong>
                                {{ __('messages.admin.projects.delete-text-2') }}
                            </p>
                            <div class="flex justify-end gap-4">
                                <button @click="open = false"
                                    class="px-2 py-1 2md:px-4 2md:py-2 text-xs lg:text-sm bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">
                                    {{ __('messages.button.cancel') }}
                                </button>

                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-2 py-1 2md:px-4 2md:py-2 text-xs lg:text-sm bg-red-600 text-white rounded hover:bg-red-700 cursor-pointer">
                                        {{ __('messages.button.delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <a href="{{ route('projects.index') }}"
                class="px-2 py-1 2md:px-4 2md:py-2 text-xs lg:text-sm bg-themeBlue text-white hover:bg-themeHoverBlue flex items-center gap-2 w-max rounded transition duration-200 ease-in-out transform hover:scale-101">
                <x-icon name="arrow-left" class="w-5 h-auto" /> {{ __('messages.project-details.back')  }}
            </a>
        </div>

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