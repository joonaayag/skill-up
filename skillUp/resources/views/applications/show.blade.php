@extends('layouts.app')

@section('title', 'Detalles de la candidatura')

@section('content')
    <h1>Candidatura para: {{ $application->offer->name }}</h1>

    <p><strong>Candidato:</strong> {{ $application->candidate_name }}</p>
    <p><strong>Puesto solicitado:</strong> {{ $application->position_applied }}</p>
    <p><strong>Motivo:</strong> {{ $application->application_reason }}</p>
    <p><strong>Estado:</strong> {{ ucfirst($application->state) }}</p>
    <p><strong>Fecha:</strong> {{ $application->application_date }}</p>

    @if ($application->cv)
        <p><a href="{{ asset('storage/' . $application->cv) }}" target="_blank">Ver CV</a></p>
    @endif

    <a href="{{ route('applications.index') }}">← Volver</a>
@endsection
