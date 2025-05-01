@extends('layouts.app')

@section('title', 'Tus proyectos')

@section('content')
    <h2>Tus proyectos</h2>
    <ul>
        @forelse ($userProjects as $project)
            <a href="{{ route('projects.show', $project->id) }}">
                <li>
                    <strong>{{ $project->name }}</strong><br>
                    <span><em>Categoría:</em> {{ $project->category }} | <em>Fecha:</em>
                        {{ $project->creation_date }}</span><br>
                    <p>{{ $project->description }}</p>
                    <hr>
                </li>
            </a>
        @empty
            <p>No has creado ningún proyecto todavía.</p>
        @endforelse
    </ul>

@endsection