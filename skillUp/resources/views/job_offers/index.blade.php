@extends('layouts.app')

@section('title', 'Ofertas de Empleo')

@section('content')
    <h1>Ofertas de empleo</h1>

    <form method="GET" action="{{ route('job.offers.index') }}" class="mb-6 space-y-2">
        <input type="text" name="name" placeholder="Título" value="{{ request('name') }}">
        <input type="text" name="description" placeholder="Descripción" value="{{ request('description') }}">
        <input type="text" name="author" placeholder="Autor (empresa)" value="{{ request('author') }}">
        <div>
            <label><strong>Categorías generales:</strong></label><br>
            @php
                $generalCategories = [
                    'Tecnología y desarrollo',
                    'Diseño y comunicación',
                    'Administración y negocio',
                    'Comunicación',
                    'Educación',
                    'Ciencia y salud',
                    'Industria',
                    'Otro'
                ];
            @endphp
            @foreach ($generalCategories as $cat)
                <label class="block">
                    <input type="checkbox" name="general_category[]" value="{{ $cat }}"
                        @checked(is_array(request('general_category')) && in_array($cat, request('general_category')))>
                    {{ $cat }}
                </label>
            @endforeach
        </div>
        <div>
            <label><strong>Categorías específicas:</strong></label><br>
            @php
                $sectorCategories = [
                    'Desarrollo software',
                    'Ciberseguridad',
                    'Datos y analíticas',
                    'IA',
                    'Redes y sistemas',
                    'Publicidad',
                    'Diseño gráfico',
                    'Fotografía/Video',
                    'Finanzas y contabilidad',
                    'RRHH',
                    'Ventas',
                    'Logística',
                    'Legal/Jurídico',
                    'Periodismo',
                    'Traducción',
                    'SEO/SEM',
                    'Community Manager',
                    'Profesorado',
                    'Coordinación educativa',
                    'Orientación',
                    'Medicina/Enfermería',
                    'Psicología',
                    'Farmacia',
                    'Investigación/Laboratorio',
                    'Terapias/Rehabilitación',
                    'Nutrición',
                    'Construcción',
                    'Electricidad/Fontanería',
                    'Mecánica',
                    'Operario industrial',
                    'Energía/Renovables',
                    'Automoción',
                    'Agricultura/Medioambiente',
                    'Hostelería/Turismo',
                    'Arte/Cultura',
                    'Transporte/Reparto',
                    'Seguridad'
                ];
            @endphp
            @foreach ($sectorCategories as $sector)
                <label class="block">
                    <input type="checkbox" name="sector_category[]" value="{{ $sector }}"
                        @checked(is_array(request('sector_category')) && in_array($sector, request('sector_category')))>
                    {{ $sector }}
                </label>
            @endforeach
        </div>
        <button type="submit" class="mt-2">Buscar</button>
    </form>

    @forelse ($offers as $offer)
        <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem;">
            <a href="{{ route('job.offers.show', $offer->id) }}">
                <h3>{{ $offer->name }}</h3>
                @if($offer->subtitle)
                    <p><strong>{{ $offer->subtitle }}</strong></p>
                @endif
                <p>{{ $offer->description }}</p>
                <p><em>Estado:</em> {{ $offer->state }}</p>
                <p><em>Categoría:</em> {{ $offer->sector_category }} | {{ $offer->general_category }}</p>

            </a>

            @php
                $favorite = auth()->user()->favorites()
                    ->where('type', 'oferta')
                    ->where('reference_id', $offer->id)
                    ->first();
            @endphp

            @if ($favorite)
                <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit">❌ Quitar de favoritos</button>
                </form>
            @else
                <form action="{{ route('favorites.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="oferta">
                    <input type="hidden" name="reference_id" value="{{ $offer->id }}">
                    <button type="submit">❤️ Añadir a favoritos</button>
                </form>
            @endif

            <p class="text-sm text-gray-500">👁️ {{ $offer->views }} visitas</p>


        </div>

    @empty
        <p>No hay ofertas disponibles en este momento.</p>
    @endforelse
@endsection