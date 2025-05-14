@extends('layouts.app')

@section('title', 'Detalles de la oferta')

@section('content')
    <x-heading level="h1" class="mb-10">Oferta de empleo </x-heading>

    <x-card class="mb-12 flex gap-8 ">
        <div class="w-2/3">
            <div class="flex gap-4 items-center ">
                <x-tags>{{ $offer->general_category }}</x-tags>
                <span
                    class="px-3 py-1 rounded-full {{ $offer->state === 'Cerrada' ? 'bg-themeRed' : 'bg-themeBlue' }} text-white">{{ $offer->state }}</span>
            </div>
            <x-heading level="h2" class="mt-6 mb-3">{{ $offer->name }}</x-heading>

            @if ($offer->subtitle)
                <x-heading level="h3" class="mb-4">{{ $offer->subtitle }}</x-heading>
            @endif
            <p><strong>Categoría del sector:</strong> {{ $offer->sector_category }}</p>

            <x-heading level="h4" class="mt-6 mb-3">Descripción del puesto</x-heading>

            <p class="mb-9"><strong>Descripción:</strong><br>{{ $offer->description }}</p>

            <div class="flex items-center gap-4 mb-6 mt-auto">
                @php
                    $favorite = auth()->user()->favorites()
                        ->where('type', 'proyecto')
                        ->where('reference_id', $offer->id)
                        ->first();
                @endphp

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
                        <input type="hidden" name="reference_id" value="{{ $offer->id }}">
                        <button type="submit">❤️</button>
                    </form>
                @endif

                <p class="text-sm text-gray-500">👁️ {{ $offer->views }} visitas</p>
            </div>

            @if (in_array(auth()->user()->role, ['Usuario', 'Alumno']) && $offer->state === 'Abierta')
                <div x-data="{ showModal: false }"
                    x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))"
                    class="relative z-10">
                    <button @click="showModal = true"
                        class=" p-2 bg-themeBlue text-white rounded shadow-lg hover:bg-themeHoverBlue transition cursor-pointer">
                        Aplicar
                    </button>

                    <x-modal>
                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Formulario de
                            aplicación</x-heading>

                        <form action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data"
                            class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0">
                            @csrf

                            <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                            <div class="flex flex-col gap-2.5">
                                <x-label for="candidate_name">Nombre:</x-label>
                                <x-inputtext type="text" name="candidate_name" id="candidate_name"
                                    value="{{ auth()->user()->name }}" readonly />

                                <x-label for="position_applied">Puesto solicitado:</x-label>
                                <x-inputtext type="text" name="position_applied" id="position_applied"
                                    value="{{ $offer->name }}" readonly />

                                <x-label for="position_applied">Motivo de la aplicación:</x-label>
                                <textarea name="application_reason"
                                    class="w-full h-30 border rounded border-themeLightGray resize-none"
                                    required></textarea><br>


                                <div class="mt-4 mb-8" x-data="{ cvName: '' }">
                                    <x-label for="curriculum">Curriculum:</x-label>

                                    <label for="cv-upload"
                                        class="flex items-center justify-center w-full px-4 py-2 bg-themeBlue text-white font-medium rounded cursor-pointer hover:bg-themeHoverBlue transition">
                                        📄 Subir CV
                                        <input id="cv-upload" type="file" name="cv" accept=".pdf" class="hidden"
                                            @change="cvName = $event.target.files.length ? $event.target.files[0].name : ''">
                                    </label>

                                    <template x-if="cvName">
                                        <p class="mt-2 text-sm text-black dark:text-themeLightGray" x-text="cvName"></p>
                                    </template>
                                </div>

                                <div class="flex justify-end gap-4">
                                    <button type="submit"
                                        class="px-4 py-2 bg-themeGrape/80 text-white rounded hover:bg-themeGrape transition cursor-pointer">
                                        Guardar
                                    </button>
                                    <button type="button" @click="showModal = false"
                                        class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </x-modal>

                    </form>
                </div>
            @endif
        </div>
        <div class="w-1/3 flex justify-end">
            <ul>
                <li class="mb-5">
                    <img src="{{ $offer->company->profile ? asset('storage/' . auth()->user()->profile) : 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Windows_10_Default_Profile_Picture.svg/2048px-Windows_10_Default_Profile_Picture.svg.png' }}"
                        alt="Perfil" id="profileImage"
                        class="size-40 object-cover">
                </li>
                <li><strong>Publicado: </strong>{{ $offer->created_at }}</li>
                <li><strong>Empresa: </strong>{{ $offer->company->name }}</li>
                <li><strong>Ubicación: </strong>{{ $offer->company->detail->address }}</li>
                <li><strong>Fecha límite: </strong> Fecha limite</li>
            </ul>

        </div>

    </x-card>



    <a href="{{ route('job.offers.index') }}">← Volver</a>



@endsection