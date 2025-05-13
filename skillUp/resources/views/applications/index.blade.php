@extends('layouts.app')

@section('title', 'Gestión de candidaturas')

@section('content')
    <x-heading level="h1" class="mb-10">Gestión de candidaturas</x-heading>

    <div class="grid grid-cols-4 gap-24 ">
        <x-card class="h-full col-span-1">
            <form id="candidate-filter-form" method="GET" action="{{ route('applications.index') }}"
                class=" flex flex-col [&>select]:h-12 [&>select]:bg-white dark:[&>select]:bg-themeBgDark [&>select]:rounded-lg [&>select]:border-2 [&>select]:border-themeLightGray [&>select]:px-4 ">

                <x-heading level="h3" class="mb-2.5">Puesto</x-heading>

                <x-inputtext class="mb-3" type="text" name="position_applied" id="position_applied"
                    value="{{ request('position_applied') }}" placeholder="Buscar por puesto" required />

                <x-heading level="h3" class="mb-2.5">Estado</x-heading>
                <select class="mb-3" name="state">
                    <option value="">Estado</option>
                    <option value="nueva" @selected(request('state') == 'nueva')>Nueva</option>
                    <option value="en revisión" @selected(request('state') == 'en revisión')>En revisión</option>
                    <option value="aceptado" @selected(request('state') == 'aceptado')>Aceptado</option>
                    <option value="rechazado" @selected(request('state') == 'rechazado')>Rechazado</option>
                </select>

                <x-heading level="h3" class="mb-2.5">Nombre</x-heading>

                <x-inputtext type="text" name="candidate_name" id="candidate_name" value="{{ request('candidate_name') }}"
                    placeholder="Nombre del candidato" required />

            </form>
        </x-card>
        <x-card class="col-span-3 bg-white dark:bg-themeBgDark">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="py-2">Nombre</th>
                        <th class="py-2">Estado</th>
                        <th class="py-2">Fecha de realización</th>
                        <th class="py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $app)
                        <tr class="border-b">
                            <td class="py-2">
                                <div class="font-semibold">{{ $app->candidate_name }}</div>
                                <div class="text-sm text-gray-500">{{ ucfirst($app->position_applied) }}</div>
                            </td>
                            <td class="py-2">{{ ucfirst($app->state) }}</td>
                            <td class="py-2">{{ $app->application_date }}</td>

                            <td class="py-2 space-x-2 relative z-10" x-data="{ showDelete: false, showDetails: false }"
                                x-init="$watch('showDelete', val => document.body.classList.toggle('overflow-hidden', val));
                                                                                                            $watch('showDetails', val => document.body.classList.toggle('overflow-hidden', val));">
                                <button @click="showDetails = true"
                                    class="p-2 bg-themeBlue text-white shadow-lg rounded-lg cursor-pointer hover:bg-themeHoverBlue/80 transition">
                                    Ver
                                </button>

                                <x-modal class="flex items-center justify-center" :show="'showDetails'">

                                    <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Detalles
                                        de la candidatura de {{ $app->candidate_name }}
                                    </x-heading>
                                    <div class="flex flex-col items-center text-left gap-2.5 mb-4 ">
                                        <p><strong>Nombre:</strong> {{ $app->candidate_name }}</p>
                                        <p><strong>Puesto solicitado:</strong> {{ $app->position_applied }}</p>
                                        <p><strong>Motivo:</strong> {{ $app->application_reason }}</p>
                                        <p><strong>Fecha:</strong> {{ $app->application_date }}</p>

                                        @if ($app->cv)
                                            <p>
                                                <a href="{{ asset('storage/' . $app->cv) }}" class="text-blue-600 underline"
                                                    target="_blank">📄
                                                    Ver CV</a>
                                            </p>
                                        @else
                                            <p class="text-gray-500">No se subió CV</p>
                                        @endif

                                        <form method="POST" action="{{ route('applications.update', $app->id) }}">
                                            @csrf
                                            @method('PUT')

                                            <label for="state">Cambiar estado:</label>
                                            <select name="state" required class="dark:bg-themeBgDark">
                                                <option value="nueva" {{ $app->state === 'nueva' ? 'selected' : '' }}>Nueva
                                                </option>
                                                <option value="en revisión" {{ $app->state === 'en revisión' ? 'selected' : '' }}>
                                                    En
                                                    revisión
                                                </option>
                                                <option value="aceptado" {{ $app->state === 'aceptado' ? 'selected' : '' }}>
                                                    Aceptado
                                                </option>
                                                <option value="rechazado" {{ $app->state === 'rechazado' ? 'selected' : '' }}>
                                                    Rechazado
                                                </option>
                                            </select>
                                            <br>
                                        </form>
                                        <div>
                                            <button type="submit"
                                                class="px-4 py-2 bg-themeBlue text-gray-800 cursor-pointer hover:bg-themeHoverBlue/80 transition rounded-lg">Guardar</button>
                                            <button @click="showDetails = false"
                                                class="px-4 py-2 bg-themeLightGray/50 text-gray-800 cursor-pointer hover:bg-themeLightGray transition rounded-lg">Cerrar</button>
                                        </div>

                                    </div>
                                </x-modal>

                                <button @click="showDelete = true"
                                    class="p-2 bg-white dark:bg-themeBgDark text-themeRed border-2 border-themeRed shadow-lg rounded-lg hover:bg-themeHoverRed/30 transition cursor-pointer">
                                    Eliminar
                                </button>

                                <x-modal :show="'showDelete'">
                                    <x-heading level="h3" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">Seguro
                                        deses eliminar a {{ $app->candidate_name }} de la oferta {{ $app->position_applied }}?</x-heading>
                                    <form action="{{ route('applications.destroy', $app->id) }}" method="POST"
                                        class="flex justify-center gap-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 bg-white dark:bg-themeBgDark text-themeRed border-2 border-themeRed shadow-lg rounded-lg hover:bg-themeHoverRed/30 transition cursor-pointer">Eliminar</button>
                                        <button type="button"
                                            class="px-4 py-2 bg-themeLightGray text-gray-800 cursor-pointer hover:bg-gray-300 transition rounded-lg"
                                            @click="showDelete = false">Cancelar</button>
                                    </form>

                                </x-modal>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <p class="text-lg">No hay candidaturas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('candidate-filter-form');

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