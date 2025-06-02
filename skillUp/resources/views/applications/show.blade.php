@extends('layouts.app')

@section('title', __('messages.titles.applications-details'))

@section('content')
    <x-heading level="h1" class="mb-10">{{ $application->offer->name }}</x-heading>

    <p><strong>Candidato:</strong> {{ $application->candidate_name }}</p>
    <p><strong>Puesto solicitado:</strong> {{ $application->position_applied }}</p>
    <p><strong>Motivo:</strong> {{ $application->application_reason }}</p>
    <p><strong>Estado:</strong> {{ ucfirst($application->state) }}</p>
    <p><strong>Fecha:</strong> {{ $application->application_date }}</p>

    @if ($application->cv)
        <p><a href="{{ asset('storage/' . $application->cv) }}" target="_blank">Ver CV</a></p>
    @endif

    <a href="{{ route('applications.index') }}"
        class="mt-3 px-2 py-2 bg-themeBlue text-white hover:bg-themeHoverBlue flex items-center gap-2 w-max rounded transition duration-200 ease-in-out transform hover:scale-101">
        <x-icon name="arrow-left" class="w-5 h-auto" /> Volver</a>
@endsection