@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
    <div x-data="{ open: false }" class="max-w-xl mx-auto bg-white rounded-xl shadow-md overflow-hidden mt-10">
        <!-- Header con imagen de fondo -->
        <div class="relative">
            <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e" alt="Fondo"
                class="w-full h-40 object-cover" id="bannerImage">
            <div class="absolute -bottom-10 left-1/2 transform -translate-x-1/2">
                <img class="h-24 w-24 rounded-full border-4 border-white object-cover"
                    src="https://randomuser.me/api/portraits/men/32.jpg" alt="Perfil" id="profileImage">
            </div>
        </div>

        <!-- Contenido -->
        <div class="pt-16 pb-6 px-6 text-center">
            <h2 class="text-2xl font-bold">{{ auth()->user()->name }} {{ auth()->user()->last_name }}</h2>
            <span class="inline-block mt-2 px-3 py-1 text-sm text-white bg-green-500 rounded-full">{{ auth()->user()->role }}</span>

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
                <form action="{{ route('user.update', auth()->id()) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <!-- Banner -->
                    <div class="mb-4 relative">
                        <img id="editBannerPreview" src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e"
                            alt="Banner" class="w-full h-40 object-cover rounded cursor-pointer"
                            onclick="document.getElementById('editBannerInput').click();">
                        <input type="file" id="editBannerInput" accept="image/*" class="hidden"
                            onchange="previewImage(event, 'editBannerPreview')">
                    </div>

                    <!-- Foto de perfil -->
                    <div class="mb-4 flex justify-center">
                        <img id="editProfilePreview" src="https://randomuser.me/api/portraits/men/32.jpg" alt="Perfil"
                            class="h-24 w-24 rounded-full object-cover border-4 border-white shadow cursor-pointer"
                            onclick="document.getElementById('editProfileInput').click();">
                        <input type="file" id="editProfileInput" accept="image/*" class="hidden"
                            onchange="previewImage(event, 'editProfilePreview')">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Nombre</label>
                            <input type="text" name="name" class="mt-1 w-full border rounded px-3 py-2" value={{ auth()->user()->name }}>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Apellido</label>
                            <input type="text" name="last_name" class="mt-1 w-full border rounded px-3 py-2" value={{ auth()->user()->last_name }}>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" class="mt-1 w-full border rounded px-3 py-2" value={{ auth()->user()->email }}>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium">Descripción</label>
                        <textarea name="description" class="mt-1 w-full border rounded px-3 py-2"
                            rows="3">Lorem ipsum dolor sit amet...</textarea>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="open = false" class="px-4 py-2 rounded border">Cancelar</button>
                        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection