@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-10">
        <x-heading level="h1" class="mb-10">Listado de Proyectos</x-heading>

        <table
            class="min-w-full bg-white dark:bg-themeBgDark text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-4 py-3 border dark:border-gray-700">ID</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Nombre</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Autor</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Categoría</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Tags</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Fecha creación</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Enlace</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Valoración media</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-4 py-3 border dark:border-gray-700">{{ $project->id }}</td>
                        <td class="px-4 py-3 border dark:border-gray-700">{{ $project->title }}</td>
                        <td class="px-4 py-3 border dark:border-gray-700">{{ $project->author->name ?? 'Sin autor' }}</td>
                        <td class="px-4 py-3 border dark:border-gray-700">{{ $project->general_category ?? '-' }}</td>
                        <td class="px-4 py-3 border dark:border-gray-700">{{ $project->tags ?? '-' }}</td>
                        <td class="px-4 py-3 border dark:border-gray-700">
                            {{ $project->creation_date ? \Carbon\Carbon::parse($project->creation_date)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 border dark:border-gray-700">
                            @if ($project->link)
                                <a href="{{ $project->link }}" target="_blank" class="text-themeBlue hover:underline">Ver</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 border dark:border-gray-700">
                            {{ number_format($project->averageRating() ?? 0, 1) }} / 5
                        </td>
                        <td class="px-4 py-3 border dark:border-gray-700">
                            <a href="{{ route('admin.project.details', $project->id) }}"
                                class="text-themeBlue hover:underline font-medium">Ver detalles</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-5 text-center text-gray-500 dark:text-gray-300">
                            No hay proyectos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection