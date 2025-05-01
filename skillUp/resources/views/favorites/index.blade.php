@extends('layouts.app')

@section('title', 'Favoritos')

@section('content')

    <h1>Mis favoritos</h1>

    <form method="GET" action="{{ route('favorites.index') }}" class="mb-6 space-y-2">
        <select name="type">
            <option value="">-- Tipo --</option>
            <option value="proyecto" @selected(request('type') == 'proyecto')>Proyectos</option>
            <option value="oferta" @selected(request('type') == 'oferta')>Ofertas</option>
        </select>

        <input type="text" name="name" placeholder="Título" value="{{ request('name') }}">
        <input type="text" name="description" placeholder="Descripción" value="{{ request('description') }}">
        <input type="text" name="author" placeholder="Autor o empresa" value="{{ request('author') }}">

        <select name="order">
            <option value="">-- Ordenar por --</option>
            <option value="name" @selected(request('order') == 'name')>Título</option>
            <option value="created_at" @selected(request('order') == 'created_at')>Fecha de creación</option>
        </select>

        <button type="submit">Filtrar</button>
    </form>

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