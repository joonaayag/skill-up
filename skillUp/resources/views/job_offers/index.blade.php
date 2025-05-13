@extends('layouts.app')

@section('title', 'Ofertas de Empleo')

@section('content')
    <h1>Ofertas de empleo</h1>

    <div class="grid grid-cols-[250px_1fr] gap-20 p-4 dark:text-themeLightGray">
        <aside class="bg-white dark:bg-themeBgDark rounded-lg border-2 border-themeLightGray shadow px-4 py-5 space-y-4">
            <form method="GET" action="{{ route('job.offers.index') }}" class="mb-6 space-y-2">
                <p>Categorias generales</p>
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

                <p>Sector</p>
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
            </form>
        </aside>

        <main class="space-y-4">
            <form method="GET" action="{{ route('job.offers.index') }}" class="space-y-4">
                <div
                    class="flex flex-wrap gap-2 h-12 [&>input]:h-full [&>select]:h-full [&>input]:bg-white dark:[&>input]:bg-themeBgDark [&>select]:bg-white dark:[&>select]:bg-themeBgDark
                                                                                         [&>input]:rounded-lg [&>select]:rounded-lg [&>input]:border-2 [&>input]:border-themeLightGray [&>select]:border-2 [&>select]:border-themeLightGray [&>select]:px-4 [&>input]:px-4 [&>input]:outline-0">
                    <input type="text" placeholder="Buscar por título..." class="input" />
                    <input type="text" placeholder="Buscar por descripción..." class="input" />
                    <input type="text" placeholder="Buscar por autor..." class="input" />
                    <select class="input">
                        <option>Ordenar por</option>
                        <option value="puesto" @selected(request('order') == 'puesto')>Puesto</option>
                        <option value="general_category" @selected(request('order') == 'name')>Categoria</option>
                        <option value="created_at" @selected(request('order') == 'created_at')>Fecha de creación</option>
                    </select>
                </div>
                <button type="submit" class="mt-2">Buscar</button>
            </form>

            <ul class="grid grid-cols-3 gap-10">
                @forelse ($offers as $offer)
                    <a href="{{ route('job.offers.show', $offer->id) }}">
                        <x-card class="h-full hover:border-themeBlue hover:scale-101 transition">
                            <li class="flex flex-col h-full ">
                                <x-tags class="mb-1">{{ $offer->sector_category }}</x-tags>
                                <x-heading level="h3" class="mb-1">{{ $offer->name }}</x-heading>
                                <x-heading level="h4" class="mb-1">{{ $offer->general_category }}</x-heading>
                                @if($offer->subtitle)
                                    <p><strong>{{ Str::limit($offer->subtitle, 20) }}</strong></p>
                                @endif

                                @php
                                    $favorite = auth()->user()->favorites()
                                        ->where('type', 'oferta')
                                        ->where('reference_id', $offer->id)
                                        ->first();
                                @endphp

                                <div class="flex flex-row justify-between items-center mt-auto">
                                    <div class="flex flex-row gap-3 ">
                                        <p>{{ $offer->state }}</p>
                                        @if ($favorite)
                                            <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit">❌</button>
                                            </form>
                                        @else
                                            <form action="{{ route('favorites.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="type" value="oferta">
                                                <input type="hidden" name="reference_id" value="{{ $offer->id }}">
                                                <button type="submit">❤️</button>
                                            </form>
                                        @endif
                                        <p>👁️{{ $offer->views }}</p>
                                    </div>
                                    <span
                                        class="text-sm">{{ $offer->company->name . ' ' . $offer->company->last_name  }}</span>
                                </div>

                            </li>
                        </x-card>
                    </a>
                @empty
                    <p>No hay proyectos disponibles.</p>
                @endforelse
            </ul>

        </main>
    </div>

    <div x-data="{ showModal: false }"
        x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))" class="relative z-10">

        <button @click="showModal = true"
            class="fixed bottom-6 right-6 p-2 bg-themeBlue text-white rounded-full shadow-lg hover:bg-themeHoverBlue transition cursor-pointer">
            <x-icon size="6" name="plus" />
        </button>

        <x-modal>
            <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Nuevo proyecto</x-heading>
            <form action="{{ route('job.offers.store') }}" method="POST" class="space-y-4 [&>select]:border-2  [&>select]:border-themeLightGray [&>input]:outline-0 [&>textarea]:outline-0">
                @csrf

                <x-label for="title">Título:</x-label>
                <x-inputtext type="text" name="name" id="name" required />

                <x-label for="subtitle">Subtitulo:</x-label>
                <x-inputtext type="text" name="subtitle" id="subtitle" required />

                <x-label for="description">Descripción:</x-label>
                <x-textarea name="description" id="description" required></x-textarea>

                <x-label for="sector_category">Categoría del sector:</x-label>
                <x-inputtext type="text" name="sector_category" id="sector_category" required />

                <x-label for="general_category">Categoría general:</x-label>
                <x-inputtext type="text" name="general_category" id="general_category" required />

                <x-label for="state">Estado:</x-label>
                <select name="state" required>
                    <option value="abierta">Abierta</option>
                    <option value="cerrada">Cerrada</option>
                </select><br>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="submit"
                        class="px-4 py-2 bg-themeGrape/80 text-white rounded hover:bg-themeGrape transition cursor-pointer">
                        Guardar
                    </button>
                    <button type="button" @click="showModal = false"
                        class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                        Cancelar
                    </button>
                </div>
            </form>
        </x-modal>


    </div>

@endsection