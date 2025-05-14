@extends('layouts.app')

@section('title', 'Detalles del proyecto')

@section('content')
    <div x-data="{ selectedImage: null }">

        <x-heading level="h1" class="mb-10">Proyecto </x-heading>

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
                    <p class="flex items-center justify-center gap-1"><x-icon name="graphic" class="w-4 h-auto" />{{ $project->views }}</p>
                    <p>
                        <label>★</label>
                        {{ $project->averageRating() ? number_format($project->averageRating(), 1) : 'Sin calificaciones' }}
                    </p>
                    @auth
                        <form id="rating-form" action="{{ route('projects.rate', $project->id) }}" method="POST">
                            @csrf
                            <div class="rating-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ $project->getRatingByUser(auth()->id()) && $project->getRatingByUser(auth()->id())->rating == $i ? 'checked' : '' }}>
                                    <label for="star{{ $i }}">★</label>
                                @endfor
                            </div>
                        </form>
                    @endauth
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

        <a href="{{ route('projects.index') }}">← Volver</a>

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