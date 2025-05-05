@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-2xl font-bold mb-6">Gestión de Ofertas</h1>

        <table class="w-full border text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Nombre</th>
                    <th class="p-2 border">Subtítulo</th>
                    <th class="p-2 border">Sector</th>
                    <th class="p-2 border">Categoría</th>
                    <th class="p-2 border">Estado</th>
                    <th class="p-2 border">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($offers as $offer)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2 border">{{ $offer->id }}</td>
                        <td class="p-2 border">{{ $offer->name }}</td>
                        <td class="p-2 border">{{ $offer->subtitle }}</td>
                        <td class="p-2 border">{{ $offer->sector_category }}</td>
                        <td class="p-2 border">{{ $offer->general_category }}</td>
                        <td class="p-2 border">{{ ucfirst($offer->state) }}</td>
                        <td class="p-2 border flex gap-2">
                            <!-- Botón Editar -->
                            <div x-data="{ openEdit: false }">
                                <button @click="openEdit = true" class="text-blue-500 hover:underline">Editar</button>

                                <div x-show="openEdit" x-cloak
                                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                    <div class="bg-white p-6 rounded w-full max-w-lg shadow-lg overflow-auto max-h-[90vh]">
                                        <h2 class="text-lg font-bold mb-4">Editar Oferta</h2>
                                        <form action="{{ route('admin.offers.update', $offer->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <label>Título:</label>
                                            <input type="text" name="name" value="{{ $offer->name }}" required><br>

                                            <label>Subtítulo:</label>
                                            <input type="text" name="subtitle" value="{{ $offer->subtitle }}"><br>

                                            <label>Descripción:</label>
                                            <textarea name="description" required>{{ $offer->description }}</textarea><br>

                                            <label>Categoría del sector:</label>
                                            <input type="text" name="sector_category" value="{{ $offer->sector_category }}"
                                                required><br>

                                            <label>Categoría general:</label>
                                            <input type="text" name="general_category" value="{{ $offer->general_category }}"
                                                required><br>

                                            <label>Estado:</label>
                                            <select name="state" required>
                                                <option value="abierta" {{ $offer->state === 'abierta' ? 'selected' : '' }}>
                                                    Abierta</option>
                                                <option value="cerrada" {{ $offer->state === 'cerrada' ? 'selected' : '' }}>
                                                    Cerrada</option>
                                            </select><br>

                                            <label>Logo:</label>
                                            <input type="text" name="logo" value="{{ $offer->logo }}"><br>

                                            <button type="submit">Guardar cambios</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón Eliminar -->
                            <div x-data="{ openDelete: false }">
                                <button @click="openDelete = true" class="text-red-500 hover:underline">Eliminar</button>

                                <div x-show="openDelete" x-cloak
                                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                    <div class="bg-white p-6 rounded w-full max-w-md shadow-lg">
                                        <h2 class="text-lg font-bold mb-4">¿Eliminar oferta?</h2>
                                        <p class="mb-4">Esta acción eliminará la oferta <strong>{{ $offer->name }}</strong>.</p>
                                        <div class="flex justify-end gap-2">
                                            <button @click="openDelete = false"
                                                class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                                            <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center">No hay ofertas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection