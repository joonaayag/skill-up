@extends('layouts.app')

@section('title', 'Ofertas de Empleo')

@section('content')
    <h1>Mis ofertas de empleo</h1>

    {{-- Formulario de filtros --}}
    <form method="GET" action="{{ route('job.offers.company.index') }}" class="mb-6 space-y-2">
        <input type="text" name="name" placeholder="Título" value="{{ request('name') }}">
        <input type="text" name="description" placeholder="Descripción" value="{{ request('description') }}">

        <div>
            <label><strong>Categorías generales:</strong></label><br>
            @php
                $generalCategories = [
                    'Tecnología y desarrollo', 'Diseño y comunicación', 'Administración y negocio', 'Comunicación',
                    'Educación', 'Ciencia y salud', 'Industria', 'Otro'
                ];
            @endphp
            @foreach ($generalCategories as $cat)
                <label class="block">
                    <input type="checkbox" name="general_category[]" value="{{ $cat }}"
                        @checked(is_array(request('general_category')) && in_array($cat, request('general_category'))) >
                    {{ $cat }}
                </label>
            @endforeach
        </div>

        <div>
            <label><strong>Categorías específicas:</strong></label><br>
            @php
                $sectorCategories = [
                    'Desarrollo software', 'Ciberseguridad', 'Datos y analíticas', 'IA', 'Redes y sistemas',
                    'Publicidad', 'Diseño gráfico', 'Fotografía/Video', 'Finanzas y contabilidad', 'RRHH', 'Ventas',
                    'Logística', 'Legal/Jurídico', 'Periodismo', 'Traducción', 'SEO/SEM', 'Community Manager',
                    'Profesorado', 'Coordinación educativa', 'Orientación', 'Medicina/Enfermería', 'Psicología',
                    'Farmacia', 'Investigación/Laboratorio', 'Terapias/Rehabilitación', 'Nutrición', 'Construcción',
                    'Electricidad/Fontanería', 'Mecánica', 'Operario industrial', 'Energía/Renovables', 'Automoción',
                    'Agricultura/Medioambiente', 'Hostelería/Turismo', 'Arte/Cultura', 'Transporte/Reparto', 'Seguridad'
                ];
            @endphp
            @foreach ($sectorCategories as $sector)
                <label class="block">
                    <input type="checkbox" name="sector_category[]" value="{{ $sector }}"
                        @checked(is_array(request('sector_category')) && in_array($sector, request('sector_category'))) >
                    {{ $sector }}
                </label>
            @endforeach
        </div>

        <div>
            <label><strong>Ordenar por:</strong></label><br>
            <select name="order">
                <option value="">-- Selecciona un campo --</option>
                <option value="name" @selected(request('order') == 'name')>Título</option>
                <option value="general_category" @selected(request('order') == 'general_category')>Categoría general</option>
                <option value="sector_category" @selected(request('order') == 'sector_category')>Categoría específica</option>
                <option value="created_at" @selected(request('order') == 'created_at')>Fecha de creación</option>
            </select>
        </div>

        <button type="submit" class="mt-2">Buscar</button>
    </form>

    {{-- Listado de ofertas --}}
    <div x-data="{ showModal: false }">
        @forelse ($offers as $offer)
            <div x-data="{ showEdit: false, showDeleteConfirm: false }" style="margin-bottom: 2rem; border: 1px solid #ccc; padding: 1rem;">
                <a href="{{ route('job.offers.show', $offer->id) }}">
                    <strong>{{ $offer->name }}</strong>
                    <p>{{ $offer->subtitle }}</p>
                    <p>{{ $offer->description }}</p>
                    <p><em>Estado:</em> {{ $offer->state }}</p>
                </a>

                <button @click="showEdit = true" style="margin-top: 0.5rem;">Editar</button>

                {{-- Formulario de edición --}}
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
                            <option value="Abierta" {{ $offer->state === 'Abierta' ? 'selected' : '' }}>Abierta</option>
                            <option value="Cerrada" {{ $offer->state === 'Cerrada' ? 'selected' : '' }}>Cerrada</option>
                        </select><br>

                        <label>Logo:</label>
                        <input type="text" name="logo" value="{{ $offer->logo }}"><br>

                        <button type="submit">Guardar cambios</button>
                    </form>

                    <button @click="showEdit = false" style="margin-top: 0.5rem;">Cerrar</button>
                </div>

                {{-- Botón y confirmación de eliminación --}}
                <button @click="showDeleteConfirm = true" style="color:red; margin-top: 1rem;">Eliminar oferta</button>

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
        @empty
            <p>No tienes ofertas creadas aún.</p>
        @endforelse

        {{-- Botón de crear nueva oferta --}}
        <button @click="showModal = true" class="mt-4">Crear nueva oferta</button>

        {{-- Modal de creación --}}
        <div x-show="showModal" style="margin-top: 1rem; border: 1px solid #ccc; padding: 1rem;">
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
