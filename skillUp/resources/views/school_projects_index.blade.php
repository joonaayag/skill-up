@extends('layouts.app')

@section('title', 'Proyectos Escolares')

@section('content')
    <h1>Gestión de Proyectos Escolares</h1>

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
                    <form action="{{ route('school.projects.update', $project->id) }}" method="POST">
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

                        <button type="submit">Guardar cambios</button>
                        <button type="button" @click="showEdit = false">Cancelar</button>
                    </form>
                </div>
            </li>

        @endforeach
    </ul>
@endsection