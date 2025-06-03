@php
    $sectorMap = [
        'Agricultura/Medio ambiente' => 'sector-agri',
        'Arte/Cultura' => 'sector-art',
        'Automoción' => 'sector-aut',
        'Ciberseguridad' => 'sector-cyb',
        'Community Manager' => 'sector-comm',
        'Construcción' => 'sector-cons',
        'Coordinación Educativa' => 'sector-educ',
        'Diseño Gráfico' => 'sector-grap',
        'Electricidad y fontanería' => 'sector-elec',
        'Energía/Renovables' => 'sector-ener',
        'Farmacia' => 'sector-phar',
        'Finanzas y contabilidad' => 'sector-fina',
        'Fotografía/vídeo' => 'sector-photo',
        'Hostelería/turismo' => 'sector-hosp',
        'AI' => 'sector-ai',
        'Investigación/laboratorio' => 'sector-res',
        'Legal' => 'sector-leg',
        'Logística' => 'sector-log',
        'Mecánica' => 'sector-mec',
        'Medicina/Enfermería' => 'sector-med',
        'Nutrición' => 'sector-nut',
        'Operador Industrial' => 'sector-ind',
        'Orientación' => 'sector-ori',
        'Periodismo' => 'sector-jout',
        'Enseñanza' => 'sector-tea',
        'Psicología' => 'sector-psy',
        'Publicidad' => 'sector-adv',
        'Redes y Sistemas' => 'sector-net',
        'RRHH' => 'sector-hr',
        'Seguridad' => 'sector-sec',
        'SEO/SEM' => 'sector-seo',
        'Terapias/Rehabilitación' => 'sector-ther',
        'Traducción' => 'sector-trans',
        'Transporte/Entrega' => 'sector-transp',
        'Ventas' => 'sector-sal',
    ];
@endphp

@extends('layouts.app')

@section('title', __('messages.titles.job-offers'))

@section('content')
    <x-heading level="h1" class="mb-10">{{ __('messages.job-offers.title') }}</x-heading>
    @if ($errors->any())
        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-black dark:text-white">- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="relative">
        <form id="filters-input" method="GET" action="{{ route('job.offers.index') }}" class="mb-6 space-y-2">
            <div class="grid grid-cols-1 md:grid-cols-[250px_1fr] gap-20 p-4 dark:text-themeLightGray">
                <aside
                    class="bg-white dark:bg-themeBgDark rounded-lg border-2 border-themeLightGray shadow-md px-4 py-5 space-y-4 transition-all duration-300 ease-in-out">

                    <x-heading level="h4"><strong>{{ __('messages.job-offers.category') }}</strong></x-heading>

                    @php
                        $generalCategories = [
                            __('messages.job-offers.category-admin'),
                            __('messages.job-offers.category-science'),
                            __('messages.job-offers.category-comunication'),
                            __('messages.job-offers.category-design'),
                            __('messages.job-offers.category-education'),
                            __('messages.job-offers.category-industry'),
                            __('messages.job-offers.category-other'),
                            __('messages.job-offers.category-tec'),
                        ];
                    @endphp

                    <div class="space-y-2">
                        @foreach ($generalCategories as $cat)
                            <label
                                class="flex items-center gap-2 text-sm text-gray-700 dark:text-themeLightGray cursor-pointer hover:text-themeBlue transition-colors duration-200">
                                <input type="checkbox" name="general_category[]" value="{{ $cat }}"
                                    @checked(is_array(request('general_category')) && in_array($cat, request('general_category')))
                                    class="accent-themeBlue focus:ring-themeBlue transition duration-200">
                                {{ $cat }}
                            </label>
                        @endforeach
                    </div>

                    <x-heading level="h4" class="mt-4"><strong>Sector</strong></x-heading>

                    @php
                        $sectorCategories = [
                            __('messages.job-offers.sector-agri'),
                            __('messages.job-offers.sector-art'),
                            __('messages.job-offers.sector-aut'),
                            __('messages.job-offers.sector-cyb'),
                            __('messages.job-offers.sector-comm'),
                            __('messages.job-offers.sector-cons'),
                            __('messages.job-offers.sector-educ'),
                            __('messages.job-offers.sector-grap'),
                            __('messages.job-offers.sector-elec'),
                            __('messages.job-offers.sector-ener'),
                            __('messages.job-offers.sector-phar'),
                            __('messages.job-offers.sector-fina'),
                            __('messages.job-offers.sector-photo'),
                            __('messages.job-offers.sector-hosp'),
                            __('messages.job-offers.sector-ai'),
                            __('messages.job-offers.sector-res'),
                            __('messages.job-offers.sector-leg'),
                            __('messages.job-offers.sector-log'),
                            __('messages.job-offers.sector-mec'),
                            __('messages.job-offers.sector-med'),
                            __('messages.job-offers.sector-nut'),
                            __('messages.job-offers.sector-ind'),
                            __('messages.job-offers.sector-ori'),
                            __('messages.job-offers.sector-jout'),
                            __('messages.job-offers.sector-tea'),
                            __('messages.job-offers.sector-psy'),
                            __('messages.job-offers.sector-adv'),
                            __('messages.job-offers.sector-net'),
                            __('messages.job-offers.sector-hr'),
                            __('messages.job-offers.sector-sec'),
                            __('messages.job-offers.sector-seo'),
                            __('messages.job-offers.sector-ther'),
                            __('messages.job-offers.sector-trans'),
                            __('messages.job-offers.sector-transp'),
                            __('messages.job-offers.sector-sal'),
                        ];
                    @endphp

                    <div class="space-y-2">
                        @foreach ($sectorCategories as $sector)
                            <label
                                class="flex items-center gap-2 text-sm text-gray-700 dark:text-themeLightGray cursor-pointer hover:text-themeBlue transition-colors duration-200">
                                <input type="checkbox" name="sector_category[]" value="{{ $sector }}"
                                    @checked(is_array(request('sector_category')) && in_array($sector, request('sector_category')))
                                    class="accent-themeBlue focus:ring-themeBlue transition duration-200">
                                {{ $sector }}
                            </label>
                        @endforeach
                    </div>
                </aside>


                <main class="space-y-4 ">
                    <div class="flex flex-wrap gap-2 h-8 sm:h-10 lg:h-12 text-sm
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
                [&>select]:px-4 [&>input]:px-4
                [&>input]:outline-0 [&>select]:outline-0

                [&>input]:transition-all [&>select]:transition-all
                [&>input]:duration-300 [&>select]:duration-300
                [&>input]:ease-in-out [&>select]:ease-in-out
                [&>input]:hover:shadow-md [&>select]:hover:shadow-md
                [&>input]:focus:ring-2 [&>select]:focus:ring-2
                [&>input]:focus:ring-themeBlue [&>select]:focus:ring-themeBlue
                [&>input]:focus:border-themeBlue [&>select]:focus:border-themeBlue">

                        <input type="text" name="title" placeholder="{{ __('messages.job-offers.placeholder-title') }}"
                            value="{{ request('title') }}" />

                        <input type="text" name="author" placeholder="{{ __('messages.school-projects.placeholder-author') }}"
                            value="{{ request('author') }}" />

                        <select name="order" class="cursor-pointer">
                            <option value="">{{ __('messages.job-offers.order-by') }}</option>
                            <option value="name" @selected(request('order') == 'name')>
                                {{ __('messages.job-offers.title') }}
                            </option>
                            <option value="general_category" @selected(request('order') == 'general_category')>
                                {{ __('messages.job-offers.category') }}
                            </option>
                            <option value="created_at" @selected(request('order') == 'created_at')>
                                {{ __('messages.job-offers.order-date') }}
                            </option>
                        </select>

                        <select name="direction" class="cursor-pointer">
                            <option value="asc" @selected(request('direction') == 'asc')>Asc</option>
                            <option value="desc" @selected(request('direction') == 'desc')>Desc</option>
                        </select>
                    </div>

                </main>
            </div>
        </form>

        <ul
            class="w-fit grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-10 mt-32 sm:mt-20 md:mt-40 xl:mt-20 absolute top-4 left-[340px] ">
            @forelse ($offers as $offer)
                <a href="{{ route('job.offers.show', $offer->id) }}">
                    <x-card
                        class="h-full border border-transparent hover:border-themeBlue shadow-md hover:shadow-lg hover:scale-[1.01] transition-all duration-300 ease-in-out bg-white dark:bg-themeDark text-gray-800 dark:text-white">

                        <li class="flex flex-col">
                            @php
                                $categoryMap = [
                                    'Administración y negocio' => 'option-admin',
                                    'Ciencia y salud' => 'option-science',
                                    'Comunicación' => 'option-comunication',
                                    'Diseño y comunicación' => 'option-design',
                                    'Educación' => 'option-education',
                                    'Industria' => 'option-industry',
                                    'Otro' => 'option-other',
                                    'Tecnología y desarrollo' => 'option-tec',
                                ];

                                $categoryKey = $categoryMap[$offer->general_category] ?? null;
                            @endphp

                            @if ($categoryKey)
                                <x-tags class="mb-1 ">{{ __('messages.projects.' . $categoryKey) }}</x-tags>
                            @endif

                            <x-heading level="h3" class="mb-1">{{ $offer->name }}</x-heading>

                            @php
                                $sectorkey = $sectorMap[$offer->sector_category] ?? null;
                            @endphp

                            @if ($sectorkey)
                                <x-heading level="h4" class="mb-1">
                                    {{ __('messages.job-offers.' . $sectorkey) }}
                                </x-heading>
                            @endif

                            @php
                                $favorite = auth()->user()->favorites()
                                    ->where('type', 'oferta')
                                    ->where('reference_id', $offer->id)
                                    ->first();
                            @endphp

                            <div class="flex flex-row justify-between items-center mt-2">
                                <div class="flex flex-row gap-4">
                                    <p
                                        class="px-3 py-1 rounded-full {{ $offer->state === 'Abierta' ? 'bg-themeBlue text-white' : 'bg-themeRed text-white' }}">
                                        {{ __('messages.company-offers.' . ($offer->state === 'Abierta' ? 'open' : 'close')) }}
                                    </p>

                                    @if ($favorite)
                                        <form action="{{ route('favorites.destroy', $favorite->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class=" text-themeRed translate-y-1.5 hover:scale-110 transition-transform duration-200 cursor-pointer"><x-icon
                                                    name="filled-heart" class="w-5 h-auto" /></button>
                                        </form>
                                    @else
                                        <form action="{{ route('favorites.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="type" value="oferta">
                                            <input type="hidden" name="reference_id" value="{{ $offer->id }}">
                                            <button type="submit"
                                                class=" text-themeRed translate-y-1.5 hover:scale-110 transition-transform duration-200 cursor-pointer"><x-icon
                                                    name="heart" class="w-5 h-auto cursor-pointer" /></button>
                                        </form>
                                    @endif
                                    <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                                            class="w-4 h-auto" />{{ $offer->views }}</p>
                                </div>
                                <span class="text-sm">{{ $offer->company->name }}</span>
                            </div>

                        </li>
                    </x-card>
                </a>
            @empty
                <p class="w-fit mt-32 md:mt-44 2md:mt-24 xl:mt-12">{{ __('messages.job-offers.no-offers') }}.</p>
            @endforelse
        </ul>
    </div>

    @if (in_array(auth()->user()->role, ['Profesor', 'Empresa']))
        <div x-data="{ showModal: false }" x-cloak
            x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))" class="relative z-10">

            <button @click="showModal = true"
                class="fixed bottom-6 right-6 p-2 bg-themeBlue text-white rounded-full shadow-lg hover:bg-themeBlue/80 transition cursor-pointer">
                <x-icon size="6" name="plus" />
            </button>

            <x-modal>
                <x-heading level="h2"
                    class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.job-offers.new-offer') }}</x-heading>
                <form action="{{ route('job.offers.store') }}" method="POST"
                    class="space-y-4 [&>select]:border-1 [&>select]:border-themeLightGray [&>input]:outline-0 [&>textarea]:outline-0 text-xs md:tex-sm lg:text-base">
                    @csrf

                    <div id="form-errors"
                        class="bg-red-300/70 border border-red-500 text-black dark:text-white p-4 mb-4 rounded hidden">
                        <ul id="error-list" class="list-disc list-inside"></ul>
                    </div>


                    <x-label for="title">{{ __('messages.job-offers.label-title') }}<x-red-required/></x-label>
                    <x-inputtext type="text" name="name" id="name" value="{{ old('name') }}" required />

                    <x-label for="subtitle">{{ __('messages.job-offers.label-subtitle') }}</x-label>
                    <x-inputtext type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}" required />

                    <x-label for="description">{{ __('messages.job-offers.label-description') }}<x-red-required/></x-label>
                    <x-textarea name="description" id="description" required>{{ old('description') }}</x-textarea>

                    <x-label for="sector_category">{{ __('messages.job-offers.label-sector') }}<x-red-required/></x-label>
                    <select name="sector_category" id="sector_category" required
                        class="w-full rounded px-4 py-2 dark:bg-themeBgDark bg-white cursor-pointer">

                        
                        @foreach ($sectorMap as $spanishLabel => $translationKey)
                            <option value="{{ $spanishLabel }}" {{ old('sector_category') === $spanishLabel ? 'selected' : '' }}>
                                {{ __('messages.job-offers.' . $translationKey) }}
                            </option>
                        @endforeach

                    </select>

                    <x-label for="general_category">{{ __('messages.job-offers.label-category') }}<x-red-required/></x-label>
                    <select name="general_category" required
                        class="w-full px-3 py-2 dark:bg-themeBgDark rounded border  cursor-pointer">
                        <option value="Administración y negocio" {{ old('general_category') == 'Administración y negocio' ? 'selected' : '' }}>{{ __('messages.projects.option-admin') }}</option>
                        <option value="Ciencia y salud" {{ old('general_category') == 'Ciencia y salud' ? 'selected' : '' }}>
                            {{ __('messages.projects.option-science') }}
                        </option>
                        <option value="Comunicación" {{ old('general_category') == 'Comunicación' ? 'selected' : '' }}>
                            {{ __('messages.projects.option-comunication') }}
                        </option>
                        <option value="Diseño y comunicación" {{ old('general_category') == 'Diseño y comunicación' ? 'selected' : '' }}>{{ __('messages.projects.option-design') }}</option>
                        <option value="Educación" {{ old('general_category') == 'Educación' ? 'selected' : '' }}>
                            {{ __('messages.projects.option-education') }}
                        </option>
                        <option value="Industria" {{ old('general_category') == 'Industria' ? 'selected' : '' }}>
                            {{ __('messages.projects.option-industry') }}
                        </option>
                        <option value="Otro" {{ old('general_category') == 'Otro' ? 'selected' : '' }}>
                            {{ __('messages.projects.option-other') }}
                        </option>
                        <option value="Tecnología y desarrollo" {{ old('general_category') == 'Tecnología y desarrollo' ? 'selected' : '' }}>{{ __('messages.projects.option-tec') }}</option>
                    </select>

                    <x-label for="state">{{ __('messages.job-offers.label-state') }}<x-red-required/></x-label>
                    <select name="state" required
                        class="w-full border-themeLightGray rounded px-4 py-2 bg-white dark:bg-themeBgDark outline-0">
                        <option value="Abierta" @selected(old('state') == 'Abierta')>{{ __('messages.job-offers.state-open') }}
                        </option>
                        <option value="Cerrada" @selected(old('state') == 'Cerrada')>{{ __('messages.job-offers.state-close') }}
                        </option>
                    </select>

                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                            {{ __('messages.button.cancel') }}
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-themeBlue text-white rounded hover:bg-themeBlue/80 transition cursor-pointer">
                            {{ __('messages.button.save') }}
                        </button>
                    </div>
                </form>
            </x-modal>

        </div>

    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('filters-input');

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