@extends('layouts.app')

@section('title', 'Ofertas de Empleo')

@section('content')
    <h1>Mis ofertas de empleo</h1>

    <div x-data="{ showModal: false }">

        @forelse ($offers as $offer)
            <div style="margin-bottom: 2rem; border: 1px solid #ccc; padding: 1rem;">
                <a href="{{ route('job.offers.show', $offer->id) }}">
                    <div x-data="{ showEdit: false, showDeleteConfirm: false }">
                        <strong>{{ $offer->name }}</strong>
                        <p>{{ $offer->subtitle }}</p>
                        <p>{{ $offer->description }}</p>
                        <p><em>Estado:</em> {{ $offer->state }}</p>


                        {{-- Modal de edición --}}
                        <div x-show="showEdit" style="border: 1px solid #aaa; padding: 1rem; margin-top: 1rem;">
                            <h4>Editar oferta</h4>
                            <form action="{{ route('job.offers.update', $offer->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <label>Título:</label>
                                <input type="text" name="name" value="{{ $offer->name }}" required><br>

                                <label>Subtítulo:</label>
                                <input type="text" name="subtitle" value="{{ $offer->subtitle }}"><br>

                                <label>Descripción:</label>
                                <textarea name="description" required>{{ $offer->description }}</textarea><br>

                                <label>Categoría del sector:</label>
                                <input type="text" name="sector_category" value="{{ $offer->sector_category }}" required><br>

                                <label>Categoría general:</label>
                                <input type="text" name="general_category" value="{{ $offer->general_category }}" required><br>

                                <label>Estado:</label>
                                <select name="state" required>
                                    <option value="abierta" {{ $offer->state === 'abierta' ? 'selected' : '' }}>Abierta</option>
                                    <option value="cerrada" {{ $offer->state === 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                                </select><br>

                                <label>Logo:</label>
                                <input type="text" name="logo" value="{{ $offer->logo }}"><br>

                                <button type="submit">Guardar cambios</button>
                            </form>

                            <button @click="showDeleteConfirm = true" style="color:red; margin-top: 1rem;">Eliminar
                                oferta</button>

                            <button @click="showEdit = false" style="margin-top: 0.5rem;">Cerrar</button>

                            {{-- Modal de confirmación de eliminación --}}
                            <div x-show="showDeleteConfirm" style="border: 1px solid red; padding: 1rem; margin-top: 1rem;">
                                <p>¿Estás seguro de que quieres eliminar esta oferta?</p>
                                <form action="{{ route('job.offers.destroy', $offer->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color:red;">Sí, eliminar</button>
                                    <button type="button" @click="showDeleteConfirm = false">Cancelar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </a>
                <button @click="showEdit = true">Editar</button>
            </div>
        @empty
            <p>No tienes ofertas creadas aún.</p>
        @endforelse




        <button @click="showModal = true">Crear nueva oferta</button>

        {{-- Modal --}}
        <div x-show="showModal">
            <h2>Crear oferta</h2>
            <form action="{{ route('job.offers.store') }}" method="POST">
                @csrf

                <label>Título:</label>
                <input type="text" name="name" required><br>

                <label>Subtítulo:</label>
                <input type="text" name="subtitle"><br>

                <label>Descripción:</label>
                <textarea name="description" required></textarea><br>

                <label>Categoría del sector:</label>
                <input type="text" name="sector_category" required><br>

                <label>Categoría general:</label>
                <input type="text" name="general_category" required><br>

                <label>Estado:</label>
                <select name="state" required>
                    <option value="abierta">Abierta</option>
                    <option value="cerrada">Cerrada</option>
                </select><br>

                <label>Logo (opcional):</label>
                <input type="text" name="logo"><br>

                <button type="submit">Guardar oferta</button>
                <button type="button" @click="showModal = false">Cancelar</button>
            </form>
        </div>
    </div>
@endsection