@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
    <x-heading level="h1" class="mb-10">Tu perfil</x-heading>
    <div x-data="{ showModal: false }"
        x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))"
        class="relative z-10 max-w-xl mx-auto bg-white dark:bg-themeBgDark rounded-xl shadow-md overflow-hidden mt-10 border-2 border-themeLightGray">

        <div class="relative">
            <img src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : asset('images/defaultBanner.jpg') }}"
                alt="Fondo" class="w-full h-40 object-cover" id="bannerImage">

            <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                <img src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : asset('images/defaultProfile.png') }}"
                    alt="Perfil" id="profileImage"
                    class="h-24 w-24 rounded-full border-4 border-themeBlue dark:border-white object-cover shadow-lg">
            </div>
        </div>

        <div class="pt-12 pb-6 px-6 text-center ">
            <h2 class="text-2xl font-bold">{{ auth()->user()->name }} {{ auth()->user()->last_name }}</h2>
            <span
                class="inline-block mt-2 px-3 py-1 text-sm text-white bg-green-500 rounded-full">{{ auth()->user()->role }}</span>
            @php
                $details = auth()->user()->detail;
            @endphp

            <div class="mt-6 text-left space-y-4 dark:[&>div>div>div]:text-themeLightGray">
                <div class="flex gap-2">
                    <div class="w-1/2 ">
                        <label for="name" class="text-md font-bold">Nombre</label>
                        <div class=" text-dark ml-2">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="w-1/2">
                        <label for="name" class="text-md font-bold">Apellido</label>
                        <div class=" text-dark ml-2">{{ auth()->user()->last_name }}</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/2">
                        <label for="email" class="text-md font-bold">Email</label>
                        <div class="text-dark ml-2">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <div>
                    <label for="email" class="text-md font-bold">Descripción</label>
                    <div class="text-dark text-sm mt-1 ml-2">
                        {{ auth()->user()->description ?? 'No has especificado ninguna descripción' }}
                    </div>
                </div>
                @if (auth()->user()->role === 'Alumno')
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Fecha de nacimiento</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->birth_date ?? 'No especificado' }}</div>
                        </div>
                        <div class="w-1/2 gap-2">
                            <label for="email" class="text-md font-bold">Curso actual</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->current_course ?? 'No especificado' }}</div>
                        </div>
                    </div>
                    <div class="">
                        <label for="email" class="text-md font-bold">Centro educativo</label>
                        <div class="w-1/2 text-dark ml-2">{{ $details?->educational_center ?? 'No especificado' }}</div>
                    </div>
                @elseif (auth()->user()->role === 'Profesor')
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Especialización</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->specialization ?? 'No especificado' }}</div>
                        </div>
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Departamento</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->department ?? 'No especificado' }}</div>
                        </div>
                    </div>
                    <div class="flex">

                    </div>
                @elseif (auth()->user()->role === 'Empresa')
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">CIF</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->cif ?? 'No especificado' }}</div>
                        </div>
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Dirección</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->address ?? 'No especificado' }}</div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Sector</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->sector ?? 'No especificado' }}</div>
                        </div>
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Sitio web</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->website ?? 'No especificado' }}</div>
                        </div>
                    </div>
                @endif

                <label for="email" class="text-md font-bold">Currículum</label>
                @if(auth()->user()->cv)
                    <div class="text-dark text-sm mt-1">
                        <p>📄 <a href="{{ asset('storage/' . auth()->user()->cv) }}" target="_blank"
                                class="underline text-blue-600">
                                Ver mi CV
                            </a></p>
                    </div>
                @else
                    <p class="text-gray-500 ml-2">No has subido ningún CV aún.</p>
                @endif
            </div>

            <div>
                <button @click="showModal = true"
                    class="mt-6 bg-themeBlue border-2 border-themeBlue hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition cursor-pointer">
                    Editar perfil
                </button>
                <div x-cloak x-data="{ open: false }" class="inline-block">
                    <button @click="open = true" class="mt-6 bg-white border-2 border-themeRed hover:bg-themeRed/20 text-themeRed font-semibold py-2 px-4 rounded transition cursor-pointer">Eliminar</button>

                    <div x-show="open" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                        <div class="bg-white dark:bg-themeBgDark p-6 rounded shadow-lg w-full max-w-md"
                            @click.outside="open = false">
                            <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Cerrar sesión</x-heading>
                            <p class="mb-4 text-gray-600 dark:text-gray-300 break-words">
                                ¿Estás seguro de que deseas cerrar sesión, <strong>{{ auth()->user()->name }} {{ auth()->user()->last_name }}</strong>?
                            </p>
                            <div class="flex justify-end gap-4">
                                <button @click="open = false"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                    Cancelar
                                </button>

                                <form action="{{ route('user.logout', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div x-cloak x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))">
            <x-modal>
                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Editar perfil</x-heading>
                <form action="{{ route('user.update', auth()->id()) }}" method="POST" enctype="multipart/form-data"
                    class="max-w-2xl mx-auto p-6 rounded shadow [&>div>div>input]:border-themeLightGray [&>div>input]:border-themeLightGray [&>div>textarea]:border-themeLightGray">
                    @csrf
                    @method('PUT')

                    <div class="relative mb-8">
                        <img id="bannerPreview"
                            src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : asset('images/defaultBanner.jpg') }}"
                            class="w-full h-40 object-cover cursor-pointer" alt="Banner">
                        <input type="file" name="banner" id="bannerInput" accept="image/*" class="hidden">

                        <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                            <img id="fotoPerfilPreview"
                                src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : asset('images/defaultProfile.png') }}"
                                class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg cursor-pointer"
                                alt="Foto de perfil">
                            <input type="file" name="profile" id="fotoPerfilInput" accept="image/*" class="hidden">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Nombre</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Apellidos</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium">Descripción</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2"
                            rows="4">{{ old('description', $user->description) }}</textarea>
                    </div>

                    @if ($user->role === 'Alumno')
                        <div class="mt-4">
                            <label class="block text-sm font-medium">Fecha de nacimiento</label>
                            <input type="date" name="birth_date"
                                value="{{ old('birth_date', $user->detail->birth_date ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">Curso actual</label>
                            <input type="text" name="current_course"
                                value="{{ old('current_course', $user->detail->current_course ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">Centro educativo</label>
                            <input type="text" name="educational_center"
                                value="{{ old('educational_center', $user->detail->educational_center ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                    @endif

                    @if ($user->role === 'Profesor')
                        <div class="mt-4">
                            <label class="block text-sm font-medium">Especialización</label>
                            <input type="text" name="specialization"
                                value="{{ old('specialization', $user->detail->specialization ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">Departamento</label>
                            <input type="text" name="department"
                                value="{{ old('department', $user->detail->department ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                    @endif

                    @if ($user->role === 'Empresa')
                        <div class="mt-4">
                            <label class="block text-sm font-medium">CIF</label>
                            <input type="text" name="cif" value="{{ old('cif', $user->detail->cif ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">Dirección</label>
                            <input type="text" name="address" value="{{ old('address', $user->detail->address ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">Sector</label>
                            <input type="text" name="sector" value="{{ old('sector', $user->detail->sector ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">Sitio web</label>
                            <input type="url" name="website" value="{{ old('website', $user->detail->website ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                    @endif

                    <div class="mt-4" x-data="{ cvName: '' }">
                        <label class="block text-sm font-medium">Subir Cv</label>

                        <label for="cv-upload"
                            class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/80 transition">
                            📄 Subir CV
                            <input id="cv-upload" type="file" name="cv" accept=".pdf" class="hidden"
                                @change="cvName = $event.target.files.length ? $event.target.files[0].name : ''">
                        </label>

                        <template x-if="cvName">
                            <p class="mt-2 text-sm text-black dark:text-themeLightGray" x-text="cvName"></p>
                        </template>
                    </div>


                    <div class="mt-6 flex justify-end gap-4">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </x-modal>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const bannerImage = document.getElementById('bannerImage');
            const profileImage = document.getElementById('profileImage');

            const bannerInput = document.getElementById('bannerInput');
            const bannerPreview = document.getElementById('bannerPreview');

            const fotoPerfilInput = document.getElementById('fotoPerfilInput');
            const fotoPerfilPreview = document.getElementById('fotoPerfilPreview');

            bannerPreview.addEventListener('click', () => bannerInput.click());
            fotoPerfilPreview.addEventListener('click', () => fotoPerfilInput.click());

            bannerInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        bannerPreview.src = e.target.result;
                        bannerImage.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });

            fotoPerfilInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        fotoPerfilPreview.src = e.target.result;
                        profileImage.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>

@endsection