@extends('layouts.app')

@section('title', __('messages.titles.admin-school-projects-details'))

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

            @if ($schoolProject->image)
                <img src="{{ Storage::disk('s3')->url($schoolProject->image) }}" alt="Imagen del proyecto" class="mx-auto w-2/3 h-auto mb-4 rounded-lg shadow-md">
            @endif
            

            <div class="grid grid-cols-1 2md:flex 2md:justify-between mt-16">
                <div class="flex gap-4 items-center justify-start 2md:justify-center mb-4 2md:mb-0 text-xs md:text-sm 2md:text-base">
                    <p class="flex gap-2">
                        <x-icon name="user" class="w-5 h-auto" />
                        <a href="{{ route('profile.index', ['id' => $schoolProject->teacher->id]) }}" class="text-themeBlue hover:underline font-semibold">
                            {{ $schoolProject->teacher->name . ' ' . $schoolProject->teacher->last_name }}
                        </a>
                    </p>
                    
                    <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                            class="w-4 h-auto" />{{ $schoolProject->views }}</p>
                    <div id="rating-container" class="flex items-center gap-3">
                        <p class="flex items-center justify-center gap-1" id="average-rating">
                            <label class="text-yellow-400"><x-icon name="star" class="w-3 md:w-4 h-auto" /></label>
                            <span id="rating-value">{{ $schoolProject->averageRating() ? number_format($schoolProject->averageRating(), 1) : 'N/A' }}</span>
                        </p>
                        @auth
                            <form id="rating-form" data-project-id="{{ $schoolProject->id }}" class="flex gap-1">
                                @csrf
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" data-rating="{{ $i }}"
                                        class="rating-star text-3xl focus:outline-none transition transform hover:scale-110 cursor-pointer hover:text-yellow-400
                                                   {{ $schoolProject->getRatingByUser(auth()->id()) && $schoolProject->getRatingByUser(auth()->id())->rating >= $i ? 'text-yellow-400' : 'text-gray-400' }}"
                                        aria-label="Valorar con {{ $i }} estrella{{ $i > 1 ? 's' : '' }}">
                                        <x-icon name="star" class="w-3 md:w-4 h-auto" />
                                    </button>
                                @endfor
                            </form>
                        @endauth
                    </div>
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
            <x-heading level="h2" class="mt-2 mb-3">{{ __('messages.project-details.project-files')  }}</x-heading>
            
            @if($schoolProject->link)
                <p class="text-xs md:tex-sm lg:text-base"><strong>{{ __('messages.project-details.link')  }}</strong> <a href="{{ $schoolProject->link }}"
                        target="_blank">{{ $schoolProject->link }}</a></p>
            @endif

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
                                    <a href="#" @click.prevent="selectedImage = '{{ Storage::disk('s3')->url($img->path) }}'"
                                        class="block h-8 sm:h-10 px-3 py-2 text-xs md:text-sm lg:text-base bg-themeGrape/90 rounded shadow text-center hover:bg-themeGrape">
                                        {{ __('messages.project-details.see-image') }}
                                    </a>
                                @else
                                    <a href="{{ Storage::disk('s3')->url($img->path) }}" download
                                        class="block bg-themeGrape/90 h-8 sm:h-10 px-3 py-2 text-xs md:text-sm lg:text-base rounded shadow  text-center hover:bg-themeGrape">
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

        

    </div>


    <div class="flex mt-3 gap-3">
        <div x-cloak x-data="{ showModal: false }"
            x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))"
            class="relative z-10">

            <button @click="showModal = true"
                class=" h-8 sm:h-10 px-3 bg-yellow-400 text-black shadow-lg hover:bg-yellow-500 transition cursor-pointer">
                {{ __('messages.button.edit')  }}
            </button>


            <x-modal>
                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.school-projects.edit-project') }}</x-heading>

                <form action="{{ route('admin.school_project.update', $schoolProject->id) }}" enctype="multipart/form-data"
                    class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0">
                    @method('PUT')
                    @csrf

                    <div>
                        <x-label for="title">{{ __('messages.admin.offers.label-title') }}<x-red-required /></x-label>
                        <x-inputtext type="text" name="title" id="title" value="{{ old('title', $schoolProject->title) }}"
                            required />
                    </div>

                    <div>
                        <x-label for="description">{{ __('messages.admin.offers.label-description') }}<x-red-required /></x-label>
                        <x-textarea name="description" id="description"
                            required>{{ old('description', $schoolProject->description) }}</x-textarea>
                    </div>

                    <div>
                        <x-label for="author">{{ __('messages.school-projects.label-author') }}<x-red-required /></x-label>
                        <x-inputtext type="text" name="author" id="author"
                            value="{{ old('author', $schoolProject->author) }}" required />
                    </div>

                    <div>
                        <x-label for="tags">{{ __('messages.admin.projects.table-tags') }}<x-red-required /></x-label>
                        <select name="tags" id="tags"
                            class="w-full  h-8 sm:h-10 px-3 py-2 dark:bg-themeBgDark rounded border border-themeLightGray" required>
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
                                {{ __('messages.tags.tecnología') }}
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
                        <x-label for="general_category">{{ __('messages.admin.offers.label-category') }}<x-red-required /></x-label>
                        <select name="general_category" required
                            class="w-full  h-8 sm:h-10 px-3 py-2 rounded border border-themeLightGray dark:bg-themeBgDark">
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
                        <x-label for="title">{{ __('messages.projects.label-date') }}<x-red-required /></x-label>
                        <x-inputdate name="creation_date" id="creation_date"
                            value="{{ old('creation_date', $schoolProject->creation_date) }}" required />
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.projects.label-link') }}</x-label>
                        <input type="url" name="link" class="w-full  h-8 sm:h-10 px-3 py-2 rounded border border-themeLightGray"
                            value="{{ old('link', $schoolProject->link) }}" />
                    </div>

                    <div>
                        <x-label for="title">{{ __('messages.projects.label-image') }}</x-label>
                        <div x-data="{ fileName: '', previewUrl: '' }" class="w-full">
                            <label for="image-upload"
                                class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/90 transition">
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
                        <x-label for="title">{{ __('messages.projects.label-additional') }}</x-label>
                        <div x-data="{ fileNames: [] }" class="w-full">
                            <label for="file-upload"
                                class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/90 transition">
                                <div class="flex gap-2">
                                    <x-icon name="clip" class="w-5 h-auto" />
                                    {{ __('messages.projects.label-upload') }}
                                </div> 
                            </label>
                            <input id="file-upload" name="files[]" type="file" multiple accept="file/*" class="hidden"
                                @change="fileNames = [...$event.target.files].map(f => f.name)" />

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
                            {{ __('messages.button.cancel') }}
                        </button>
                        <button type="submit"
                            class="px-4  bg-themeBlue text-white rounded hover:bg-themeBlue/80 transition cursor-pointer">
                            {{ __('messages.button.save-changes') }}
                        </button>
                    </div>
                </form>
            </x-modal>

        </div>

        <div x-cloak x-data="{ showDelete: false }"
            x-init="$watch('showDelete', val => document.body.classList.toggle('overflow-hidden', val))">

            <button @click="showDelete = true"
                class=" h-8 sm:h-10 px-3  text-center bg-red-600 text-white shadow-lg hover:bg-red-700 transition cursor-pointer">
                {{ __('messages.button.delete') }}
            </button>

            <x-modal :show="'showDelete'">
                <x-heading level="h3" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue"> {{ __('messages.applications.delete-confirm-1') . $schoolProject->title . ' ' .  __('messages.from') . $schoolProject->teacher->name }}?</x-heading>
                <form action="{{ route('admin.school_project.destroy', $schoolProject->id) }}" method="POST"
                    class="flex justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class=" h-8 sm:h-10 px-3 bg-white dark:bg-themeBgDark text-themeRed border-2 border-themeRed shadow-lg rounded-lg hover:bg-themeHoverRed/30 transition cursor-pointer">{{ __('messages.button.delete') }}</button>
                    <button type="button"
                        class=" h-8 sm:h-10 px-3 bg-themeLightGray text-gray-800 cursor-pointer hover:bg-gray-300 transition rounded-lg"
                        @click="showDelete = false">{{ __('messages.button.cancel') }}</button>
                </form>

            </x-modal>
        </div>

        <a href="{{ route('admin.school_projects') }}"
            class="  h-8 sm:h-10 px-3 py-2 bg-themeBlue text-white hover:bg-themeHoverBlue flex items-center gap-2 w-max rounded transition duration-200 ease-in-out transform hover:scale-101">
            <x-icon name="arrow-left" class="w-5 h-auto" />
            {{ __('messages.project-details.back')  }}
        </a>
    </div>

    
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('rating-form');
            const stars = document.querySelectorAll('.rating-star');
            const ratingValue = document.getElementById('rating-value');
            const messageDiv = document.getElementById('rating-message');

            if (!form) return; // Si no hay formulario (usuario no autenticado)

            const projectId = form.dataset.projectId;
            const csrfToken = form.querySelector('input[name="_token"]').value;

            // Agregar event listeners a todas las estrellas
            stars.forEach(star => {
                star.addEventListener('click', function (e) {
                    e.preventDefault();
                    const rating = parseInt(this.dataset.rating);
                    submitRating(rating);
                });
            });

            function submitRating(rating) {
                // Deshabilitar botones durante la petición
                stars.forEach(star => star.disabled = true);

                fetch(`/projects/${projectId}/rate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        rating: rating
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Actualizar el promedio mostrado
                            updateAverageRating(data.averageRating);

                            // Actualizar las estrellas seleccionadas
                            updateStarSelection(rating);

                        } else {
                            throw new Error(data.message || 'Error al enviar la calificación');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        stars.forEach(star => star.disabled = false);
                    });
            }

            function updateAverageRating(newAverage) {
                if (newAverage && newAverage > 0) {
                    ratingValue.textContent = parseFloat(newAverage).toFixed(1);
                } else {
                    ratingValue.textContent = 'N/A';
                }
            }

            function updateStarSelection(userRating) {
                stars.forEach((star, index) => {
                    const starValue = index + 1;
                    if (starValue <= userRating) {
                        star.classList.remove('text-gray-400');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-400');
                    }
                });
            }
        });
    </script>
@endsection