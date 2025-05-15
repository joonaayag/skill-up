@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Gestión de Comentarios</h1>

        <div class="overflow-x-auto rounded-lg shadow">
            <table
                class="min-w-full bg-white dark:bg-themeDarkGray text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 border dark:border-gray-700">ID</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Nombre</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Proyecto</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Proyecto escolar</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Contenido</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Fecha publicación</th>
                        <th class="px-4 py-3 border dark:border-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($comments as $comment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->id }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->user->name }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->project->title ?? 'No pertenece a un proyecto' }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->project->school_project->title ?? 'No' }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->content }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $comment->created_at }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700 whitespace-nowrap space-x-3">

                                <div x-data="{ openEdit: false }" class="inline-block">
                                    <button @click="openEdit = true"
                                        class="bg-themeBlue/80 border-2 border-themeBlue/80 hover:bg-themeBlue text-white font-semibold py-2 px-4 rounded transition cursor-pointer">Editar</button>

                                    <x-modal :show="'openEdit'" @click.outside="openEdit = false">
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Editar comentario</x-heading>

                                        <form action="{{ route('admin.comments.update', $comment->id) }}" method="POST"
                                            class="space-y-4">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="block text-sm font-medium">Contenido del comentario</label>
                                                <input type="text" name="content" value="{{ $comment->content }}"
                                                    class="w-full border rounded px-3 py-2 dark:bg-themeDark dark:text-white dark:border-gray-600"
                                                    required>
                                            </div>

                                            <div class="mt-6 flex justify-end gap-4">
                                                <button type="button" @click="openEdit = false"
                                                    class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                                                    Cancelar
                                                </button>
                                                <button type="submit"
                                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 cursor-pointer">
                                                    Guardar cambios
                                                </button>
                                            </div>
                                        </form>
                                    </x-modal>
                                </div>

                                <div x-data="{ openDelete: false }" class="inline-block">
                                    <button @click="openDelete = true"
                                        class="bg-red-500 border-2 border-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded transition cursor-pointer">Eliminar</button>

                                    <x-modal :show="'openDelete'" @click.outside="openDelete = false">
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">¿Estás
                                            seguro?</x-heading>
                                        <p class="mb-4 text-gray-600 dark:text-gray-300 break-words">
                                            Esta acción eliminará el comentario de  <strong>{{ $comment->user->name }}: {{ $comment->content }}</strong>.
                                        </p>
                                        <div class="flex justify-end gap-4">
                                            <button @click="openDelete = false"
                                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">
                                                Cancelar
                                            </button>
                                            <form action="{{ route('admin.comment.destroy', $comment->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700 cursor-pointer">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </x-modal>

                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-5 text-center text-gray-500 dark:text-gray-300">
                                No hay ofertas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection