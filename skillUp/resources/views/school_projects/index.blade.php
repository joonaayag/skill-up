@extends('layouts.app')

@section('title', 'Proyectos Escolares')

@section('content')
    <h1>Gestión de Proyectos Escolares</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Botón para abrir el modal -->
    <div x-data="{ showCreate: false }" class="mb-4">
        <button @click="showCreate = true">+ Nuevo Proyecto</button>

        <!-- Modal para crear nuevo proyecto -->
        <div x-show="showCreate" style="background: rgba(0, 0, 0, 0.5);"
            class="fixed inset-0 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-md">
                <h2 class="text-xl font-bold mb-4">Crear Nuevo Proyecto</h2>
                <form action="{{ route('school.projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <label>Título:</label>
                    <input type="text" name="title" required><br>

                    <label>Autor:</label>
                    <input type="text" name="author" required><br>

                    <label>Fecha de creación:</label>
                    <input type="date" name="creation_date" required><br>

                    <label>Descripción:</label>
                    <textarea name="description" required></textarea><br>

                    <label>Tags:</label>
                    <input type="text" name="tags"><br>

                    <label>Categoría general:</label>
                    <input type="text" name="general_category"><br>

                    <label class="block text-sm">Imagen destacada</label>
                    <input type="file" name="image" accept="image/*" class="mb-2">

                    <label class="block text-sm">Archivos adicionales</label>
                    <input type="file" name="files[]" multiple class="mb-4">

                    <div class="mt-4">
                        <button type="submit">Guardar</button>
                        <button type="button" @click="showCreate = false">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <ul>
        @foreach($projects as $project)
            <li x-data="{ showDelete: false, showEdit: false }">
                <strong>{{ $project->title }}</strong> - {{ $project->author }} ({{ $project->creation_date }})

                <button @click="showDelete = true">Eliminar</button>
                <button @click="showEdit = true">Editar</button>

                <div x-show="showDelete">
                    <p>¿Seguro que deseas eliminar este proyecto?</p>
                    <form action="{{ route('school.projects.destroy', $project->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Sí, eliminar</button>
                        <button type="button" @click="showDelete = false">Cancelar</button>
                    </form>
                </div>

                <div x-show="showEdit">
                    <form action="{{ route('school.projects.update', $project->id) }}" method="POST" enctype="multipart/form-data"
                    >
                        @csrf
                        @method('PUT')

                        <label>Título:</label>
                        <input type="text" name="title" value="{{ $project->title }}" required><br>

                        <label>Autor:</label>
                        <input type="text" name="author" value="{{ $project->author }}" required><br>

                        <label>Fecha de creación:</label>
                        <input type="date" name="creation_date" value="{{ $project->creation_date }}" required><br>

                        <label>Descripción:</label>
                        <textarea name="description" required>{{ $project->description }}</textarea><br>

                        <label>Tags:</label>
                        <input type="text" name="tags" value="{{ $project->tags }}"><br>


                        <label>Categoría general:</label>
                        <input type="text" name="general_category" value="{{ $project->general_category }}"><br>

                        <label class="block text-sm">Enlace (opcional)</label>
                        <input type="url" name="link" class="w-full border rounded px-3 py-2 mb-2"
                            value="{{ old('link', $project->link) }}">

                        <label class="block text-sm">Imagen destacada</label>
                        <input type="file" name="image" accept="image/*" class="mb-2">

                        <label class="block text-sm">Archivos adicionales</label>
                        <input type="file" name="files[]" multiple class="mb-4">

                        <button type="submit">Guardar cambios</button>
                        <button type="button" @click="showEdit = false">Cancelar</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>

@endsection