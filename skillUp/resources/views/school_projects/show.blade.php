@extends('layouts.app')

@section('title', $schoolProject->name)

@section('content')
    <h1>{{ $schoolProject->name }}</h1>

    @if ($schoolProject->image)
        <img src="{{ asset('storage/' . $schoolProject->image) }}" alt="Imagen del proyecto" style="max-width: 400px;">
    @endif

    <p><strong>Descripción:</strong><br>{{ $schoolProject->description }}</p>

    <p><strong>Etiquetas:</strong> {{ $schoolProject->tags }}</p>
    <p><strong>Categoría:</strong> {{ $schoolProject->sector_category }}</p>
    <p><strong>Fecha de creación:</strong> {{ $schoolProject->creation_date }}</p>

    @if ($schoolProject->link)
        <p><strong>Enlace:</strong> <a href="{{ $schoolProject->link }}" target="_blank">{{ $schoolProject->link }}</a></p>
    @endif

    <p><a href="{{ url()->previous() }}">← Volver</a></p>
@endsection
