@extends('layouts.app')

@section('title', 'Favoritos')

@section('content')

    <x-heading level="h1" class="mb-10">{{ __('messages.favorites.title') }}</x-heading>
    @if ($errors->any())
        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-black dark:text-white">- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" id="favorite-filter-form" action="{{ route('favorites.index') }}" class="space-y-2 lg:mb-9 space-x-5 h-8 sm:h-10 lg:h-12 w-full
                [&>input]:h-full [&>select]:h-full
                [&>input]:bg-white dark:[&>input]:bg-themeBgDark
                [&>select]:bg-white dark:[&>select]:bg-themeBgDark
                [&>input]:text-gray-800 dark:[&>input]:text-themeLightGray
                [&>select]:text-gray-800 dark:[&>select]:text-themeLightGray
                [&>input]:placeholder:text-black dark:[&>input]:placeholder:text-themeLightGray
                [&>select]:placeholder:text-themeLightGray
                [&>input]:rounded-lg [&>select]:rounded-lg
                [&>input]:border-2 [&>input]:border-themeLightGray
                [&>select]:border-2 [&>select]:border-themeLightGray
                [&>input]:px-4 [&>select]:px-4
                [&>input]:outline-0 [&>select]:outline-0

                [&>input]:transition-all [&>select]:transition-all
                [&>input]:duration-300 [&>select]:duration-300
                [&>input]:ease-in-out [&>select]:ease-in-out
                [&>input]:hover:shadow-md [&>select]:hover:shadow-md
                [&>input]:focus:ring-2 [&>select]:focus:ring-2
                [&>input]:focus:ring-themeBlue [&>select]:focus:ring-themeBlue
                [&>input]:focus:border-themeBlue [&>select]:focus:border-themeBlue">

        <select name="type" class="cursor-pointer">
            <option value="">{{ __('messages.favorites.type') }}</option>
            <option value="proyecto" @selected(request('type') == 'proyecto')>{{ __('messages.favorites.projects') }}</option>
            <option value="oferta" @selected(request('type') == 'oferta')>{{ __('messages.favorites.offers') }}</option>
        </select>

        <input type="text" name="name" placeholder="{{ __('messages.favorites.placeholder-title') }}"
            value="{{ request('name') }}">

        <input type="text" name="description" placeholder="{{ __('messages.favorites.placeholder-description') }}"
            value="{{ request('description') }}">

        <input type="text" name="author" placeholder="{{ __('messages.favorites.placeholder-author') }}"
            value="{{ request('author') }}">

        <select name="order" class="cursor-pointer">
            <option value="">{{ __('messages.favorites.order-by') }}</option>
            <option value="name" @selected(request('order') == 'name')>{{ __('messages.favorites.order-title') }}</option>
            <option value="created_at" @selected(request('order') == 'created_at')>{{ __('messages.favorites.order-date') }}
            </option>
        </select>
    </form>


    @php
        $proyectos = $favorites->where('type', 'proyecto')->filter(fn($f) => $f->item());
        $ofertas = $favorites->where('type', 'oferta')->filter(fn($f) => $f->item());
        $proyectoEscolar = $favorites->where('type', 'proyectoEscolar')->filter(fn($f) => $f->item());
    @endphp

    <div class="mt-44 sm:mt-32 2md:mt-20 xl:mt-10">
        @if ($proyectos->isNotEmpty())
            <x-heading level="h3" class="mb-4">{{ __('messages.favorites.projects') }}</x-heading>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 w-full">
                @foreach ($proyectos as $fav)
                    @php $item = $fav->item(); @endphp
                    <a href="{{ route('projects.show', $item->id) }}">
                        <x-card
                            class="h-full border border-transparent hover:border-themeBlue shadow-md hover:shadow-lg hover:scale-[1.01] transition-all duration-300 ease-in-out bg-white dark:bg-themeDark text-gray-800 dark:text-white">
                            <li class="flex flex-col h-full ">
                                <x-tags class="mb-2">{{ $item->tags }}</x-tags>
                                <x-heading level="h3" class="mb-1">{{ $item->title }}</x-heading>
                                <span class="mb-4">{{ $item->general_category }}</span>

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
                                                <button type="submit"
                                                    class="text-themeRed hover:scale-110 transition-transform duration-200 cursor-pointer">
                                                    <x-icon name="filled-heart" class="w-5 h-auto" />
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('favorites.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="type" value="proyecto">
                                                <input type="hidden" name="reference_id" value="{{ $item->id }}">
                                                <button type="submit"
                                                    class="text-themeRed hover:scale-110 transition-transform duration-200 cursor-pointer">
                                                    <x-icon name="heart" class="w-5 h-auto" />
                                                </button>
                                            </form>
                                        @endif
                                        <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                                                class="w-4 h-auto" />{{ $item->views }}</p>
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
    </div>

    <div class="mt-44 sm:mt-32 2md:mt-20 xl:mt-10">
        @if ($ofertas->isNotEmpty())
            <x-heading level="h3" class="mb-4">{{ __('messages.favorites.offers') }}</x-heading>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 w-full">
                @foreach ($ofertas as $fav)
                    @php $item = $fav->item(); @endphp
                    <x-card
                        class="cursor-pointer h-full border border-transparent hover:border-themeBlue shadow-md hover:shadow-lg hover:scale-[1.01] transition-all duration-300 ease-in-out bg-white dark:bg-themeBgDark text-gray-800 dark:text-white">

                        <img src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : '' }}" alt="Perfil"
                            id="profileImage"
                            class="h-20 w-20 hidden 2xl:flex rounded-full object-cover shadow-lg absolute top-0 right-0 m-2">
                        <li class="flex flex-col h-full ">
                            <x-tags class="mb-2">{{ $item->general_category }}</x-tags>
                            <x-heading level="h3" class="mb-1">{{ $item->name }}</x-heading>
                            <span>{{ $item->sector_category }}</span>

                            @php
                                $favorite = auth()->user()->favorites()
                                    ->where('type', 'oferta')
                                    ->where('reference_id', $item->id)
                                    ->first();
                            @endphp

                            <div class="flex flex-row justify-between items-center mt-auto">
                                <div class="flex flex-row gap-3 items-center justify-center mt-2">
                                    <p class="px-3 py-1 rounded-full text-white text-sm font-medium
                                                                {{ $item->state === 'Abierta' ? 'bg-themeBlue' : 'bg-red-500' }}">
                                        {{ $item->state }}
                                    </p>
                                    @if ($favorite)
                                        <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-themeRed hover:scale-110 transition-transform duration-200 translate-y-1 cursor-pointer">
                                                <x-icon name="filled-heart" class="w-5 h-auto" />
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('favorites.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="type" value="oferta">
                                            <input type="hidden" name="reference_id" value="{{ $item->id }}">
                                            <button type="submit"
                                                class="text-themeRed hover:scale-110 transition-transform duration-200 translate-y-1 cursor-pointer">
                                                <x-icon name="heart" class="w-5 h-auto" />
                                            </button>
                                        </form>
                                    @endif
                                    <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                                            class="w-4 h-auto" />{{ $item->views }}</p>
                                </div>
                                <span class="text-sm">{{ $item->company->name . ' ' . $item->company->last_name  }}</span>
                            </div>

                        </li>
                    </x-card>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-44 sm:mt-32 2md:mt-20 xl:mt-10">
        @if ($proyectoEscolar->isNotEmpty())
            <x-heading level="h3" class="mb-4">Proyectos escolares</x-heading>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 w-full">
                @foreach ($proyectoEscolar as $fav)
                    @php $item = $fav->item(); @endphp
                    <x-card
                        class="cursor-pointer h-full border border-transparent hover:border-themeBlue shadow-md hover:shadow-lg hover:scale-[1.01] transition-all duration-300 ease-in-out bg-white dark:bg-themeBgDark text-gray-800 dark:text-white">
                        <li class="flex flex-col h-full ">
                            <x-tags class="mb-2">{{ $item->general_category }}</x-tags>
                            <x-heading level="h3" class="mb-1">{{ $item->title }}</x-heading>
                            <span>{{ $item->general_category }}</span>

                            @php
                                $favorite = auth()->user()->favorites()
                                    ->where('type', 'oferta')
                                    ->where('reference_id', $item->id)
                                    ->first();
                            @endphp

                            <div class="flex flex-row justify-between items-center mt-auto">
                                <div class="flex flex-row gap-3 items-center justify-center mt-2">
                                    @if ($favorite)
                                        <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-themeRed hover:scale-110 transition-transform duration-200 translate-y-1 cursor-pointer">
                                                <x-icon name="filled-heart" class="w-5 h-auto" />
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('favorites.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="type" value="oferta">
                                            <input type="hidden" name="reference_id" value="{{ $item->id }}">
                                            <button type="submit"
                                                class="text-themeRed hover:scale-110 transition-transform duration-200 translate-y-1 cursor-pointer">
                                                <x-icon name="heart" class="w-5 h-auto" />
                                            </button>
                                        </form>
                                    @endif
                                    <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                                            class="w-4 h-auto" />{{ $item->views }}</p>
                                    <p>
                                        {{ $item->averageRating() ? number_format($item->averageRating(), 1) : 'Sin calificaciones' }}
                                    </p>
                                </div>
                                <span class="text-sm">{{ $item->teacher->name . ' ' . $item->teacher->last_name  }}</span>
                            </div>

                        </li>
                    </x-card>
                @endforeach
            </div>
        @endif
    </div>

    @if ($proyectos->isEmpty() && $ofertas->isEmpty() && $proyectoEscolar->isEmpty())
        <p class="col-span-1 md:col-span-2 lg:col-span-3 mt-44 sm:mt-32 2md:mt-20 xl:mt-10">
            {{ __('messages.favorites.no-favorites') }}.
        </p>
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