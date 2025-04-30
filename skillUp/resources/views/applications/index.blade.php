@extends('layouts.app')

@section('title', 'Gestión de candidaturas')

@section('content')
    <h1>Gestión de candidaturas</h1>

    @forelse ($applications as $app)
        <div x-data="{ showView: false, showDelete: false }"
            style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem;">
            {{-- VISTA RESUMIDA --}}
            <p><strong>Nombre:</strong> {{ $app->candidate_name }}</p>
            <p><strong>Categoría:</strong> {{ $app->position_applied }}</p>
            <p><strong>Estado:</strong> {{ ucfirst($app->state) }}</p>
            <p><strong>Fecha:</strong> {{ $app->application_date }}</p>

            <button @click="showView = true">Ver</button>
            <button @click="showDelete = true" style="color:red;">Eliminar</button>

            {{-- MODAL VER --}}
            <div x-show="showView" style="margin-top: 1rem; border: 1px solid #aaa; padding: 1rem;">
                <h3>Detalles de la candidatura</h3>
                <p><strong>Nombre:</strong> {{ $app->candidate_name }}</p>
                <p><strong>Puesto solicitado:</strong> {{ $app->position_applied }}</p>
                <p><strong>Motivo:</strong> {{ $app->application_reason }}</p>
                <p><strong>Fecha:</strong> {{ $app->application_date }}</p>

                @if ($app->cv)
                    <p><a href="{{ asset('storage/' . $app->cv) }}" target="_blank">Ver CV</a></p>
                @endif

                {{-- FORMULARIO PARA CAMBIAR EL ESTADO --}}
                <form method="POST" action="{{ route('applications.update', $app->id) }}">
                    @csrf
                    @method('PUT')

                    <label for="state">Cambiar estado:</label>
                    <select name="state" required>
                        <option value="nueva" {{ $app->state === 'nueva' ? 'selected' : '' }}>Nueva</option>
                        <option value="en revisión" {{ $app->state === 'en revisión' ? 'selected' : '' }}>En revisión</option>
                        <option value="aceptado" {{ $app->state === 'aceptado' ? 'selected' : '' }}>Aceptado</option>
                        <option value="rechazado" {{ $app->state === 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    </select>

                    <button type="submit">Guardar</button>
                </form>

                <button @click="showView = false">Cerrar</button>
            </div>

            {{-- MODAL ELIMINAR --}}
            <div x-show="showDelete" style="margin-top: 1rem; border: 1px solid red; padding: 1rem;">
                <p>¿Deseas eliminar esta candidatura?</p>
                <form action="{{ route('applications.destroy', $app->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:red;">Sí, eliminar</button>
                    <button type="button" @click="showDelete = false">Cancelar</button>
                </form>
            </div>
        </div>
    @empty
        <p>No hay candidaturas todavía.</p>
    @endforelse
@endsection