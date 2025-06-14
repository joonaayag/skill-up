@extends('layouts.app')

@section('title', 'Editar Comentario')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Editar Comentario</div>
                    @if ($errors->any())
                        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="text-black dark:text-white">- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card-body">
                        <form method="POST" action="{{ route('comments.update', $comment->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <x-label for="content">Contenido</x-label>
                                <textarea id="content" class="form-control @error('content') is-invalid @enderror resize-none"
                                    name="content" rows="5" required>{{ old('content', $comment->content) }}</textarea>
                                @error('content')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary">
                                    Actualizar Comentario
                                </button>

                                <a href="{{ $comment->project_id ? route('projects.show', $comment->project_id) : route('school-projects.show', $comment->school_project_id) }}"
                                    class="btn btn-secondary">
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection