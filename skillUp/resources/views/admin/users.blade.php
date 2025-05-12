@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Gestión de Usuarios</h1>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>¡Ups! Algo salió mal.</strong>
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-x-auto rounded-lg shadow">
            <table
                class="min-w-full bg-white dark:bg-themeDarkGray text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 border dark:border-gray-700">ID</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Nombre</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Apellido</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Email</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Rol</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->id }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->name }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->last_name }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->email }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ ucfirst($user->role) }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700 space-x-2 whitespace-nowrap">

                                <div x-data="{ openEdit: false, role: '{{ $user->role }}' }" class="inline-block">
                                    <button @click="openEdit = true"
                                        class="text-themeBlue hover:underline font-medium">Editar</button>

                                    <div x-show="openEdit" @click.outside="openEdit = false" x-cloak
                                        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                                        <div class="bg-white dark:bg-themeBgDark p-6 rounded-lg shadow-lg w-full max-w-3xl overflow-auto max-h-[90vh] relative">

                                            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Editar Usuario
                                            </h2>
                                            <form action="{{ route('admin.user.update', auth()->id()) }}" method="POST"
                                                enctype="multipart/form-data"
                                                class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
                                                @csrf
                                                @method('PUT')

                                                <div class="relative mb-8">
                                                    <img id="bannerPreview"
                                                        src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e' }}"
                                                        class="w-full h-40 object-cover cursor-pointer" alt="Banner">
                                                    <input type="file" name="banner" id="bannerInput" accept="image/*"
                                                        class="hidden">

                                                    <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                                                        <img id="fotoPerfilPreview"
                                                            src="{{ auth()->user()->foto_perfil ? asset('storage/' . auth()->user()->foto_perfil) : 'https://randomuser.me/api/portraits/men/32.jpg' }}"
                                                            class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg cursor-pointer"
                                                            alt="Foto de perfil">
                                                        <input type="file" name="foto_perfil" id="fotoPerfilInput"
                                                            accept="image/*" class="hidden">
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
                                                        <input type="text" name="last_name"
                                                            value="{{ old('last_name', $user->last_name) }}"
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
                                                        <input type="text" name="cif"
                                                            value="{{ old('cif', $user->detail->cif ?? '') }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                    <div class="mt-4">
                                                        <label class="block text-sm font-medium">Dirección</label>
                                                        <input type="text" name="address"
                                                            value="{{ old('address', $user->detail->address ?? '') }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                    <div class="mt-4">
                                                        <label class="block text-sm font-medium">Sector</label>
                                                        <input type="text" name="sector"
                                                            value="{{ old('sector', $user->detail->sector ?? '') }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                    <div class="mt-4">
                                                        <label class="block text-sm font-medium">Sitio web</label>
                                                        <input type="url" name="website"
                                                            value="{{ old('website', $user->detail->website ?? '') }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                @endif

                                                <div class="mt-4">
                                                    <label class="block text-sm font-medium">Subir Cv</label>
                                                    <input type="file" name="cv" accept=".pdf">
                                                </div>

                                                <div class="mt-6 flex justify-end">
                                                    <button type="submit"
                                                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                                        Guardar cambios
                                                    </button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>

                                <div x-data="{ open: false }" class="inline-block">
                                    <button @click="open = true"
                                        class="text-red-600 hover:underline font-medium">Eliminar</button>

                                    <div x-show="open" x-cloak
                                        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                                        <div class="bg-white dark:bg-themeBgDark p-6 rounded shadow-lg w-full max-w-md">
                                            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">¿Estás seguro?
                                            </h2>
                                            <p class="mb-4 text-gray-600 dark:text-gray-300">
                                                Esta acción eliminará al usuario <strong>{{ $user->name }}
                                                    {{ $user->last_name }}</strong> de forma permanente.
                                            </p>
                                            <div class="flex justify-end gap-4">
                                                <button @click="open = false"
                                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                                    Cancelar
                                                </button>

                                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-5 text-center text-gray-500 dark:text-gray-300">
                                No hay usuarios registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection