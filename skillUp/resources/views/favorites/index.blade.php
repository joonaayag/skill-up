@extends('layouts.app')

@section('title', 'Favoritos')

@section('content')

    <x-heading level="h1" class="mb-10">Mis favoritos</x-heading>

    <form method="GET" id="favorite-filter-form" action="{{ route('favorites.index') }}"
        class="mb-9 space-x-5 h-12 w-full [&>input]:h-full [&>select]:h-full
          [&>select]:bg-white [&>input]:bg-white dark:[&>select]:bg-themeBgDark dark:[&>input]:bg-themeBgDark [&>input]:rounded-lg [&>select]:rounded-lg [&>input]:border-2 [&>input]:border-themeLightGray
            [&>select]:border-2 [&>select]:border-themeLightGray [&>select]:px-4 [&>input]:px-4 [&>input]:outline-0 dark:[&>select]:text-themeLightGray [&>input]:placeholder:text-black
            dark:[&>input]:text-themeLightGray dark:[&>input]:placeholder:text-themeLightGray [&>select]:placeholder:text-themeLightGray">
        <select name="type">
            <option value=""> Tipo </option>
            <option value="proyecto" @selected(request('type') == 'proyecto')>Proyectos</option>
            <option value="oferta" @selected(request('type') == 'oferta')>Ofertas</option>
        </select>

        <input type="text" name="name" placeholder="Título" value="{{ request('name') }}">
        <input type="text" name="description" placeholder="Descripción" value="{{ request('description') }}">
        <input type="text" name="author" placeholder="Autor o empresa" value="{{ request('author') }}">

        <select name="order">
            <option value=""> Ordenar por </option>
            <option value="name" @selected(request('order') == 'name')>Título</option>
            <option value="created_at" @selected(request('order') == 'created_at')>Fecha de creación</option>
        </select>
    </form>

    @php
        $proyectos = $favorites->where('type', 'proyecto')->filter(fn($f) => $f->item());
        $ofertas = $favorites->where('type', 'oferta')->filter(fn($f) => $f->item());
    @endphp

    @if ($proyectos->isNotEmpty())
        <x-heading level="h3" class="mb-4">Proyectos</x-heading>
        <div class="grid grid-cols-3 gap-6 mb-9">
            @foreach ($proyectos as $fav)
                @php $item = $fav->item(); @endphp
                <a href="{{ route('projects.show', $item->id) }}">
                    <x-card class="h-full hover:border-themeBlue hover:scale-101 transition cursor-pointer">
                        <li class="flex flex-col h-full ">
                            <x-tags class="mb-2">{{ $item->tags }}</x-tags>
                            <x-heading level="h3" class="mb-1">{{ $item->title }}</x-heading>
                            <span>{{ $item->general_category }}</span>
                            <p class=" text-sm break-words mb-1.5">{{ Str::limit($item->description, 100) }}</p>

                            @php
                                $favorite = auth()->user()->favorites()
                                    ->where('type', 'proyecto')
                                    ->where('reference_id', $item->id)
                                    ->first();
                            @endphp

                            <div class="flex flex-row justify-between items-center mt-auto">
                                <div class="flex flex-row gap-3 ">
                                    @if ($favorite)
                                        <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">❌</button>
                                        </form>
                                    @else
                                        <form action="{{ route('favorites.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="type" value="proyecto">
                                            <input type="hidden" name="reference_id" value="{{ $item->id }}">
                                            <button type="submit">❤️</button>
                                        </form>
                                    @endif
                                    <p class="flex items-center justify-center gap-1"><x-icon name="graphic" class="w-4 h-auto" />{{ $item->views }}</p>
                                    <p>
                                        {{ $item->averageRating() ? number_format($item->averageRating(), 1) : 'Sin calificaciones' }}
                                    </p>
                                </div>
                                <span class="text-sm">{{ $item->author->name . ' ' . $item->author->last_name  }}</span>
                            </div>

                        </li>
                    </x-card>
                </a>
            @endforeach
        </div>
    @endif

    @if ($ofertas->isNotEmpty())
        <x-heading level="h3" class="mb-4">Ofertas</x-heading>
        <div class="grid grid-cols-3 gap-6 mb-9">
            @foreach ($ofertas as $fav)
                @php $item = $fav->item(); @endphp
                <x-card class="h-full relative">
                    <img src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Windows_10_Default_Profile_Picture.svg/2048px-Windows_10_Default_Profile_Picture.svg.png' }}"
                        alt="Perfil" id="profileImage"
                        class="h-20 w-20 rounded-full object-cover shadow-lg absolute top-0 right-0 m-2">
                    <li class="flex flex-col h-full ">
                        <x-tags class="mb-2">{{ $item->general_category }}</x-tags>
                        <x-heading level="h3" class="mb-1">{{ $item->name }}</x-heading>
                        <span>{{ $item->sector_category }}</span>
                        <p class=" text-sm mb-1.5">{{ Str::limit($item->description, 100) }}</p>

                        @php
                            $favorite = auth()->user()->favorites()
                                ->where('type', 'oferta')
                                ->where('reference_id', $item->id)
                                ->first();
                        @endphp

                        <div class="flex flex-row justify-between items-center mt-auto">
                            <div class="flex flex-row gap-3 items-cente mt-2">
                                <p class="px-3 py-1 rounded-full text-white text-sm font-medium
                                                {{ $item->state === 'abierta' ? 'bg-themeBlue' : 'bg-red-500' }}">
                                    {{ $item->state }}
                                </p>
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
                                        <input type="hidden" name="reference_id" value="{{ $item->id }}">
                                        <button type="submit">❤️</button>
                                    </form>
                                @endif
                                <p class="flex items-center justify-center gap-1"><x-icon name="graphic" class="w-4 h-auto" />{{ $item->views }}</p>
                            </div>
                            <span class="text-sm">{{ $item->company->name . ' ' . $item->company->last_name  }}</span>
                        </div>

                    </li>
                </x-card>
            @endforeach
        </div>
    @endif

    @if ($proyectos->isEmpty() && $ofertas->isEmpty())
        <p>No tienes favoritos aún.</p>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('favorite-filter-form');

            if (form) {
                const inputs = form.querySelectorAll('input, select');

                inputs.forEach(input => {
                    input.addEventListener('change', () => {
                        form.submit();
                    });

                    if (input.tagName === 'INPUT') {
                        input.addEventListener('keyup', () => {
                            clearTimeout(input._timeout);
                            input._timeout = setTimeout(() => form.submit(), 1000);
                        });
                    }
                });
            }
        });

    </script>

@endsection