@extends('layouts.app')

@section('title', 'Detalles de la oferta')

@section('content')
    <h1>{{ $offer->name }}</h1>

    @if ($offer->subtitle)
        <h3>{{ $offer->subtitle }}</h3>
    @endif

    <p><strong>Descripción:</strong><br>{{ $offer->description }}</p>

    <p><strong>Categoría del sector:</strong> {{ $offer->sector_category }}</p>
    <p><strong>Categoría general:</strong> {{ $offer->general_category }}</p>
    <p><strong>Estado:</strong> {{ $offer->state }}</p>

    @if ($offer->logo)
        <p><strong>Logo:</strong><br><img src="{{ asset('storage/' . $offer->logo) }}" alt="Logo" style="max-width: 200px;"></p>
    @endif

    <a href="{{ route('job.offers.index') }}">← Volver</a>

    @php
        $favorite = auth()->user()->favorites()
            ->where('type', 'proyecto')
            ->where('reference_id', $offer->id)
            ->first();
    @endphp

    @if ($favorite)
        <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">❌ Quitar de favoritos</button>
        </form>
    @else
        <form action="{{ route('favorites.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="proyecto">
            <input type="hidden" name="reference_id" value="{{ $offer->id }}">
            <button type="submit">❤️ Añadir a favoritos</button>
        </form>
    @endif


    @auth
        @php
            $role = auth()->user()->role;
        @endphp

        @if(in_array($role, ['usuario', 'alumno', 'profesor']))
            <div x-data="{ showApply: false }" style="margin-top: 1rem;">
                <button @click="showApply = true">Aplicar</button>

                <div x-show="showApply" style="margin-top: 1rem; border: 1px solid #ccc; padding: 1rem;">
                    <h3>Aplicar a esta oferta</h3>
                    <form action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="offer_id" value="{{ $offer->id }}">

                        <label>Nombre:</label>
                        <input type="text" name="candidate_name" value="{{ auth()->user()->name }}" required><br>

                        <label>Puesto solicitado:</label>
                        <input type="text" name="position_applied" value="{{ $offer->name }}" required><br>

                        <label>Motivo de la aplicación:</label>
                        <textarea name="application_reason" required></textarea><br>

                        <label>Currículum (PDF):</label>
                        <input type="file" name="cv" accept=".pdf"><br>

                        <button type="submit">Enviar candidatura</button>
                        <button type="button" @click="showApply = false">Cancelar</button>
                    </form>
                </div>
            </div>
        @endif
    @endauth

@endsection