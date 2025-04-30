@extends('layouts.app')

@section('title', 'Ofertas de Empleo')

@section('content')
    <h1>Ofertas de empleo</h1>

    @forelse ($offers as $offer)
        <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem;">
            <h3>{{ $offer->name }}</h3>
            @if($offer->subtitle)
                <p><strong>{{ $offer->subtitle }}</strong></p>
            @endif
            <p>{{ $offer->description }}</p>
            <p><em>Estado:</em> {{ $offer->state }}</p>
            <p><em>Categoría:</em> {{ $offer->sector_category }} | {{ $offer->general_category }}</p>
        </div>
    @empty
        <p>No hay ofertas disponibles en este momento.</p>
    @endforelse
@endsection
