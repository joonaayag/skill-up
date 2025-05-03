@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Bienvenido al Dashboard</h1>
    <p>Este es el panel principal tras iniciar sesión.</p>

    
    @if($notifications->count())
        <h2 class="mt-6 text-lg font-semibold">Tus notificaciones recientes</h2>
        <ul class="mt-2 space-y-2">
            @foreach($notifications as $notification)
                <li class="p-4 border rounded-lg {{ $notification->is_read ? 'bg-gray-100' : 'bg-white' }}">
                    <strong>{{ $notification->title }}</strong>
                    <p class="text-sm text-gray-600">{{ $notification->message }}</p>
                    <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                </li>
            @endforeach
        </ul>
    @else
        <p class="mt-6 text-gray-500">No tienes notificaciones por el momento.</p>
    @endif
@endsection
