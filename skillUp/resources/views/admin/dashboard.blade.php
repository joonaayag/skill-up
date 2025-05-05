@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Panel de Administración</h1>
        <p>Bienvenido, {{ auth()->user()->name }}.</p>

        <ul>
            <li><a href="{{ route('admin.projects') }}">Gestionar Proyectos</a></li>
            <li><a href="{{ route('admin.school_projects') }}">Gestionar Proyectos escolares</a></li>
            <li><a href="{{ route('admin.users') }}">Gestionar Usuarios</a></li>
            <li><a href="{{ route('admin.offers') }}">Gestionar Ofertas</a></li>
        </ul>
    </div>
@endsection
