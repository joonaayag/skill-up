@extends('layouts.app')

@section('title', __('messages.titles.applications'))

@section('content')
    <x-heading level="h1" class="mb-10">{{ __('messages.applications.title') }}</x-heading>
    @if ($errors->any())
        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-black dark:text-white">- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-24 ">
        <x-card class="h-full col-span-3 xl:col-span-1">
            <form id="candidate-filter-form" method="GET" action="{{ route('applications.index') }}" class="flex flex-col text-xs md:text-sm lg:text-base
            [&>select]:bg-white dark:[&>select]:bg-themeBgDark
            [&>select]:text-gray-800 dark:[&>select]:text-themeLightGray
            [&>select]:rounded-lg
            [&>select]:border-2 [&>select]:border-themeLightGray
            [&>select]:px-4 [&>select]:outline-0
            [&>select]:transition-all
            [&>select]:duration-300 [&>select]:ease-in-out
            [&>select]:hover:shadow-md
            [&>select]:focus:ring-2 [&>select]:focus:ring-themeBlue
            [&>select]:focus:border-themeBlue">

                <!-- Puesto -->
                <x-heading level="h3" class="mb-2.5">{{ __('messages.applications.position') }}</x-heading>

                <x-inputtext
                    class="mb-3 w-full h-10 bg-white dark:bg-themeBgDark text-gray-800 dark:text-themeLightGray border-2 border-themeLightGray rounded-lg px-4 outline-0 transition-all duration-300 ease-in-out hover:shadow-md focus:ring-2 focus:ring-themeBlue focus:border-themeBlue"
                    type="text" name="position_applied" id="position_applied" value="{{ request('position_applied') }}"
                    placeholder="{{ __('messages.applications.placeholder-position') }}" required />

                <!-- Estado -->
                <x-heading level="h3" class="mb-2.5">{{ __('messages.applications.status') }}</x-heading>

                <select class="mb-3 cursor-pointer h-10 ">
                    <option value="">{{ __('messages.applications.status') }}</option>
                    <option value="nueva" @selected(request('state') == 'nueva')>{{ __('messages.applications.new') }}
                    </option>
                    <option value="en revisión" @selected(request('state') == 'en revisión')>
                        {{ __('messages.applications.in-process') }}</option>
                    <option value="aceptado" @selected(request('state') == 'aceptado')>
                        {{ __('messages.applications.accepted') }}</option>
                    <option value="rechazado" @selected(request('state') == 'rechazado')>
                        {{ __('messages.applications.rejected') }}</option>
                </select>

                <!-- Nombre -->
                <x-heading level="h3" class="mb-2.5">{{ __('messages.applications.name') }}</x-heading>

                <x-inputtext
                    class="w-full h-10 bg-white dark:bg-themeBgDark text-gray-800 dark:text-themeLightGray border-2 border-themeLightGray rounded-lg px-4 outline-0 transition-all duration-300 ease-in-out hover:shadow-md focus:ring-2 focus:ring-themeBlue focus:border-themeBlue"
                    type="text" name="candidate_name" id="candidate_name" value="{{ request('candidate_name') }}"
                    placeholder="{{ __('messages.applications.placeholder-name') }}" required />
            </form>

        </x-card>
        <x-card class="col-span-3 bg-white dark:bg-themeBgDark">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b text-xs md:tex-sm lg:text-base">
                        <th class="py-2">{{ __('messages.applications.table-name') }}</th>
                        <th class="py-2">{{ __('messages.applications.table-status') }}</th>
                        <th class="py-2">{{ __('messages.applications.table-date') }}</th>
                        <th class="py-2">{{ __('messages.applications.table-actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $app)
                        <tr class="border-b [&>td]:text-xs [&>td]:md:tex-sm [&>td]lg:text-base">
                            <td class="py-2 ">
                                <div class="font-semibold">{{ $app->candidate_name }}</div>
                                <div class="text-sm text-gray-500">{{ ucfirst($app->position_applied) }}</div>
                            </td>
                            <td class="py-2">{{ ucfirst($app->state) }}</td>
                            <td class="py-2">{{ $app->application_date }}</td>

                            <td class="py-2 space-x-2 relative z-10" x-data="{ showDelete: false, showDetails: false }" x-cloak
                                x-init="$watch('showDelete', val => document.body.classList.toggle('overflow-hidden', val));
                                                                                                                            $watch('showDetails', val => document.body.classList.toggle('overflow-hidden', val));">
                                <button @click="showDetails = true"
                                    class="px-2 py-1 2md:px-4 2md:py-2 bg-themeBlue text-white shadow-lg rounded-lg cursor-pointer hover:bg-themeHoverBlue/80 transition">
                                    {{ __('messages.button.see') }}
                                </button>

                                <x-modal class="flex items-center justify-center" :show="'showDetails'">

                                    <x-heading level="h2"
                                        class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.applications.candidate-details') }}
                                        {{ $app->candidate_name }}
                                    </x-heading>
                                    <div class="flex flex-col justify-center mx-auto text-left gap-2.5 mb-4 ">
                                        <p class="text-xs md:tex-sm lg:text-base">
                                            <strong>{{ __('messages.applications.modal-name') }}</strong>
                                            {{ $app->candidate_name }}</p>
                                        <p class="text-xs md:tex-sm lg:text-base">
                                            <strong>{{ __('messages.applications.modal-position') }}</strong>
                                            {{ $app->position_applied }}</p>
                                        <p class="text-xs md:tex-sm lg:text-base">
                                            <strong>{{ __('messages.applications.modal-reason') }}:</strong>
                                            {{ $app->application_reason }}</p>
                                        <p class="text-xs md:tex-sm lg:text-base">
                                            <strong>{{ __('messages.applications.modal-date') }}</strong>
                                            {{ $app->application_date }}</p>

                                        @if ($app->cv)
                                            <p>
                                                <a href="{{ asset('storage/' . $app->cv) }}"
                                                    class="text-blue-600 underline text-xs md:tex-sm lg:text-base"
                                                    target="_blank">📄
                                                    {{ __('messages.applications.see-cv') }}</a>
                                            </p>
                                        @else
                                            <p class="text-gray-500 text-xs md:tex-sm lg:text-base">
                                                {{ __('messages.applications.no-cv') }}</p>
                                        @endif

                                        <form method="POST" action="{{ route('applications.update', $app->id) }}">
                                            @csrf
                                            @method('PUT')

                                            <x-label for="state">{{ __('messages.applications.change-status') }}</x-label>
                                            <select name="state" required
                                                class="dark:bg-themeBgDark text-xs md:tex-sm lg:text-base border border-themeLightGray rounded-md px-4 py-2 cursor-pointer">
                                                <option value="nueva" {{ $app->state === 'nueva' ? 'selected' : '' }}>
                                                    {{ __('messages.applications.new') }}
                                                </option>
                                                <option value="en revisión" {{ $app->state === 'en revisión' ? 'selected' : '' }}>
                                                    {{ __('messages.applications.in-process') }}
                                                </option>
                                                <option value="aceptado" {{ $app->state === 'aceptado' ? 'selected' : '' }}>
                                                    {{ __('messages.applications.accepted') }}
                                                </option>
                                                <option value="rechazado" {{ $app->state === 'rechazado' ? 'selected' : '' }}>
                                                    {{ __('messages.applications.rejected') }}
                                                </option>
                                            </select>
                                            <br>
                                            <div class="mt-6">
                                                <button type="submit"
                                                    class="h-8 sm:h-10 px-2 py-1 2md:px-4 2md:py-2 bg-themeBlue text-white cursor-pointer hover:bg-themeHoverBlue/80 transition rounded-lg">{{ __('messages.button.save-changes') }}</button>
                                                <button @click="showDetails = false"
                                                    class="h-8 sm:h-10 px-2 py-1 2md:px-4 2md:py-2 bg-themeLightGray/50 text-white cursor-pointer hover:bg-themeLightGray transition rounded-lg">{{ __('messages.button.cancel') }}</button>
                                            </div>
                                        </form>

                                    </div>
                                </x-modal>

                                <button @click="showDelete = true"
                                    class="px-2 py-1 2md:px-4 2md:py-2 bg-white dark:bg-themeBgDark text-themeRed border-2 border-themeRed shadow-lg rounded-lg hover:bg-themeHoverRed/30 transition cursor-pointer">
                                    {{ __('messages.button.delete') }}
                                </button>

                                <x-modal :show="'showDelete'">
                                    <x-heading level="h3"
                                        class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.applications.delete-confirm-1') }}
                                        {{ $app->candidate_name }} {{ __('messages.applications.delete-confirm-2') }}
                                        {{ $app->position_applied }}?</x-heading>
                                    <form action="{{ route('applications.destroy', $app->id) }}" method="POST"
                                        class="flex justify-center gap-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2 py-1 2md:px-4 2md:py-2 bg-white dark:bg-themeBgDark text-themeRed border-2 border-themeRed shadow-lg rounded-lg hover:bg-themeHoverRed/30 transition cursor-pointer">{{ __('messages.applications.delete') }}</button>
                                        <button type="button"
                                            class="px-2 py-1 2md:px-4 2md:py-2 bg-themeLightGray text-gray-800 cursor-pointer hover:bg-gray-300 transition rounded-lg"
                                            @click="showDelete = false">{{ __('messages.button.cancel') }}</button>
                                    </form>

                                </x-modal>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <p class="text-xs md:tex-sm lg:text-base">{{ __('messages.applications.no-applications') }}</p>
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