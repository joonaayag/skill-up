@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-10">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Proyectos Escolares</h1>

    <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full bg-white dark:bg-themeDarkGray text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                <tr>
                    <th class="px-4 py-3 border dark:border-gray-700">ID</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Nombre</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Autor</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Categoría</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Tags</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Fecha</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Enlace</th>
                    <th class="px-4 py-3 border dark:border-gray-700">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schoolProjects as $project)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-4 py-3 border dark:border-gray-700">{{ $project->id }}</td>
                        <td class="px-4 py-3 border dark:border-gray-700">{{ $project->title }}</td>
                        <td class="px-4 py-3 border dark:border-gray-700">{{ $project->author }}</td>
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
                            <a href="{{ route('admin.school_project.details', $project->id) }}"
                               class="text-themeBlue hover:underline font-medium">Ver detalles</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-5 text-center text-gray-500 dark:text-gray-300">
                            No hay proyectos escolares registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
