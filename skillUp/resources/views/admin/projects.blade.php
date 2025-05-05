@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-6">Listado de Proyectos</h1>

    <table class="w-full table-auto border border-gray-300 text-left text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Nombre</th>
                <th class="p-2 border">Autor</th>
                <th class="p-2 border">Categoría</th>
                <th class="p-2 border">Tags</th>
                <th class="p-2 border">Fecha creación</th>
                <th class="p-2 border">Enlace</th>
                <th class="p-2 border">Valoración media</th>
                <th class="p-2 border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($projects as $project)
                <tr class="hover:bg-gray-50">
                    <td class="p-2 border">{{ $project->id }}</td>
                    <td class="p-2 border">{{ $project->name }}</td>
                    <td class="p-2 border">{{ $project->author->name ?? 'Sin autor' }}</td>
                    <td class="p-2 border">{{ $project->general_category ?? '-' }}</td>
                    <td class="p-2 border">{{ $project->tags ?? '-' }}</td>
                    <td class="p-2 border">{{ $project->creation_date ? \Carbon\Carbon::parse($project->creation_date)->format('d/m/Y') : '-' }}</td>
                    <td class="p-2 border">
                        @if ($project->link)
                            <a href="{{ $project->link }}" target="_blank" class="text-blue-500 hover:underline">Ver</a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="p-2 border">
                        {{ number_format($project->averageRating() ?? 0, 1) }} / 5
                    </td>
                    <td class="p-2 border">
                        <a href="{{ route('admin.project.details', $project->id) }}" class="text-blue-600 hover:underline">Ver detalles</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="p-4 text-center">No hay proyectos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
