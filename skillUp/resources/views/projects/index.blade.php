@extends('layouts.app')

@section('title', 'Proyectos')

@section('content')
    <h1>Proyectos</h1>

    <ul>
        @forelse ($projects as $project)
            <li>
                <strong>{{ $project->name }}</strong><br>
                <span><em>Categoría:</em> {{ $project->category }} | <em>Fecha:</em> {{ $project->creation_date }}</span><br>
                <p>{{ $project->description }}</p>
                <hr>
            </li>
        @empty
            <p>No hay proyectos disponibles.</p>
        @endforelse
    </ul>
@endsection
