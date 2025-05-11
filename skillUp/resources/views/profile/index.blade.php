@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
    <x-heading level="h1" class="mb-10">Tu perfil</x-heading>
    <div x-data="{ showModal: false }"
        x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))"
        class="relative z-10 max-w-xl mx-auto bg-white rounded-xl shadow-md overflow-hidden mt-10 border-2 border-themeLightGray">

        <div class="relative">
            <img src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : 'https://i.pinimg.com/736x/b6/ef/40/b6ef40f2cd4436568d718f150abefca6.jpg' }}"
                alt="Fondo" class="w-full h-40 object-cover" id="bannerImage">

            <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                <img src="{{ auth()->user()->foto_perfil ? asset('storage/' . auth()->user()->foto_perfil) : 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Windows_10_Default_Profile_Picture.svg/2048px-Windows_10_Default_Profile_Picture.svg.png' }}"
                    alt="Perfil" id="profileImage"
                    class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg">
            </div>
        </div>

        <div class="pt-12 pb-6 px-6 text-center">
            <h2 class="text-2xl font-bold">{{ auth()->user()->name }} {{ auth()->user()->last_name }}</h2>
            <span
                class="inline-block mt-2 px-3 py-1 text-sm text-white bg-green-500 rounded-full">{{ ucfirst(auth()->user()->role) }}</span>

            @php
                $details = auth()->user()->detail;
            @endphp

            <div class="mt-6 text-left space-y-4">
                <div class="flex">
                    <div class="w-1/2">
                        <label for="name" class="text-md font-bold">Nombre</label>
                        <div class=" text-gray-900 ml-2">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="w-1/2">
                        <label for="name" class="text-md font-bold">Apellido</label>
                        <div class=" text-gray-900 ml-2">{{ auth()->user()->last_name }}</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/2">
                        <label for="email" class="text-md font-bold">Email</label>
                        <div class="text-gray-900 ml-2">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <div>
                    <label for="email" class="text-md font-bold">Descripcion</label>
                    <div class="text-gray-900 text-sm mt-1 ml-2">
                        {{ auth()->user()->description ?? 'No has especificado ninguna descripcion' }}
                    </div>
                </div>
                @if (auth()->user()->role === 'alumno')
                    <div class="flex">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Fecha de nacimiento</label>
                            <div class="w-1/2 text-gray-900">{{ $details?->birth_date ?? 'No especificado' }}</div>
                        </div>
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Curso actual</label>
                            <div class="w-1/2 text-gray-900">{{ $details?->current_course ?? 'No especificado' }}</div>
                        </div>
                    </div>
                    <div class="flex">
                        <label for="email" class="text-md font-bold">Centro educativo</label>
                        <div class="w-1/2 text-gray-900">{{ $details?->educational_center ?? 'No especificado' }}</div>
                    </div>
                @elseif (auth()->user()->role === 'profesor')
                    <div class="flex">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Especialización</label>
                            <div class="w-1/2 text-gray-900">{{ $details?->specialization ?? 'No especificado' }}</div>
                        </div>
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Departamento</label>
                            <div class="w-1/2 text-gray-900">{{ $details?->department ?? 'No especificado' }}</div>
                        </div>
                    </div>
                    <div class="flex">

                    </div>
                @elseif (auth()->user()->role === 'empresa')
                    <div class="flex">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">CIF</label>
                            <div class="w-1/2 text-gray-900">{{ $details?->cif ?? 'No especificado' }}</div>
                        </div>
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Dirección</label>
                            <div class="w-1/2 text-gray-900">{{ $details?->address ?? 'No especificado' }}</div>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Sector</label>
                            <div class="w-1/2 text-gray-900">{{ $details?->sector ?? 'No especificado' }}</div>
                        </div>
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">Sitio web</label>
                            <div class="w-1/2 text-gray-900">{{ $details?->website ?? 'No especificado' }}</div>
                        </div>
                    </div>
                @endif

                <label for="email" class="text-md font-bold">Curriculum</label>
                @if(auth()->user()->cv)
                    <div class="text-gray-900 text-sm mt-1">
                        <p>📄 <a href="{{ asset('storage/' . auth()->user()->cv) }}" target="_blank"
                                class="underline text-blue-600">
                                Ver mi CV
                            </a></p>
                    </div>
                @else
                    <p class="text-gray-500 ml-2">No has subido ningún CV aún.</p>
                @endif
            </div>
            
            <button @click="showModal = true"
                class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                Editar perfil
            </button>
        </div>
        <div x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))">
            <x-modal>
                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Editar perfil</x-heading>
                <form action="{{ route('user.update', auth()->id()) }}" method="POST" enctype="multipart/form-data"
                    class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
                    @csrf
                    @method('PUT')

                    <div class="relative mb-8">
                        <img id="bannerPreview"
                            src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e' }}"
                            class="w-full h-40 object-cover cursor-pointer" alt="Banner">
                        <input type="file" name="banner" id="bannerInput" accept="image/*" class="hidden">

                        <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                            <img id="fotoPerfilPreview"
                                src="{{ auth()->user()->foto_perfil ? asset('storage/' . auth()->user()->foto_perfil) : 'https://randomuser.me/api/portraits/men/32.jpg' }}"
                                class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg cursor-pointer"
                                alt="Foto de perfil">
                            <input type="file" name="foto_perfil" id="fotoPerfilInput" accept="image/*" class="hidden">
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

                    @if ($user->role === 'alumno')
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

                    @if ($user->role === 'profesor')
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

                    @if ($user->role === 'empresa')
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

                    <div class="mt-4">
                        <label class="block text-sm font-medium">Subir Cv</label>
                        <input type="file" name="cv" accept=".pdf">
                    </div>

                    <div class="mt-6 flex justify-end">
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