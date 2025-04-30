@extends('layouts.app')

@section('title', 'Favoritos')

@section('content')

    <h1>Mis favoritos</h1>

    @forelse ($favorites as $fav)
        @php $item = $fav->item(); @endphp
        @if ($fav->type === 'proyecto' && $item)
            <div>
                <h3>Proyecto: {{ $item->name }}</h3>
                <p>{{ Str::limit($item->description, 100) }}</p>
            </div>
        @elseif ($fav->type === 'oferta' && $item)
            <div>
                <h3>Oferta: {{ $item->name }}</h3>
                <p>{{ Str::limit($item->description, 100) }}</p>
            </div>
        @endif
    @empty
        <p>No tienes favoritos aún.</p>
    @endforelse


@endsection