@extends('layouts.app')

@section('title', 'Ofertas de Empleo')

@section('content')
    <h1>Ofertas de empleo</h1>

    @forelse ($offers as $offer)
        <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem;">
            <a href="{{ route('job.offers.show', $offer->id) }}">
                <h3>{{ $offer->name }}</h3>
                @if($offer->subtitle)
                    <p><strong>{{ $offer->subtitle }}</strong></p>
                @endif
                <p>{{ $offer->description }}</p>
                <p><em>Estado:</em> {{ $offer->state }}</p>
                <p><em>Categoría:</em> {{ $offer->sector_category }} | {{ $offer->general_category }}</p>

            </a>

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

        </div>

    @empty
        <p>No hay ofertas disponibles en este momento.</p>
    @endforelse
@endsection