@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
    <div x-data="{ open: false }" class="max-w-xl mx-auto bg-white rounded-xl shadow-md overflow-hidden mt-10">
        <div class="relative">
            <img src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e' }}"
                alt="Fondo" class="w-full h-40 object-cover" id="bannerImage">

            <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                <img src="{{ auth()->user()->foto_perfil ? asset('storage/' . auth()->user()->foto_perfil) : 'https://randomuser.me/api/portraits/men/32.jpg' }}"
                    alt="Perfil" id="profileImage"
                    class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg">
            </div>
        </div>

        <div class="pt-16 pb-6 px-6 text-center">
            <h2 class="text-2xl font-bold">{{ auth()->user()->name }} {{ auth()->user()->last_name }}</h2>
            <span
                class="inline-block mt-2 px-3 py-1 text-sm text-white bg-green-500 rounded-full">{{ auth()->user()->role }}</span>

            <div class="mt-6 text-left space-y-4">
                <div class="flex">
                    <div class="w-1/2 font-medium text-gray-700">Nombre</div>
                    <div class="w-1/2 text-gray-900">{{ auth()->user()->name }}</div>
                </div>
                <div class="flex">
                    <div class="w-1/2 font-medium text-gray-700">Apellido</div>
                    <div class="w-1/2 text-gray-900">{{ auth()->user()->last_name }}</div>
                </div>
                <div class="flex">
                    <div class="w-1/2 font-medium text-gray-700">Email</div>
                    <div class="w-1/2 text-gray-900">{{ auth()->user()->email }}</div>
                </div>
                <div>
                    <div class="font-medium text-gray-700">Descripción</div>
                    <div class="text-gray-900 text-sm mt-1">
                        {{ auth()->user()->description }}
                    </div>
                </div>
            </div>

            <button @click="open = true"
                class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                Editar perfil
            </button>
        </div>

        <!-- Modal -->
        <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white w-full max-w-lg p-6 rounded shadow relative" @click.away="open = false">
                <h3 class="text-xl font-bold mb-4">Editar Perfil</h3>
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

                    <!-- Campos de texto -->
                    <div class="grid grid-cols-2 gap-4 mt-12">
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

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Guardar cambios
                        </button>
                    </div>
                </form>


            </div>
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