@extends('layouts.app')

@section('title', __('messages.titles.job-offers-details'))

@section('content')
    <x-heading level="h1" class="mb-10">{{__('messages.job-offers.title-details')}} </x-heading>
    @if ($errors->any())
        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-black dark:text-white">- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-card class="mb-12 flex flex-col md:flex-row gap-8 ">
        <div class="md:w-2/3">
            <div class="flex gap-4 items-center ">

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
                    <x-tags>{{ __('messages.projects.' . strtolower($categoryKey)) }}</x-tags>
                @endif

                @php
                    $stateKey = strtolower($offer->state) === 'cerrada' ? 'close' : 'open';
                @endphp

                <span
                    class="text-xs md:text-base px-2 py-1 md:px-3 md:py-1 rounded-full {{ $stateKey === 'close' ? 'bg-themeRed' : 'bg-themeBlue' }} text-white">
                    {{ __('messages.company-offers.' . $stateKey) }}
                </span>

            </div>
            <x-heading level="h2" class="mt-6 mb-3">{{ $offer->name }}</x-heading>
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

                $sectorkey = $sectorMap[$offer->sector_category] ?? null;
            @endphp

            @if ($sectorkey)
                <x-heading level="h3" class="mb-4">{{ __('messages.job-offers.' . $sectorkey) }}</x-heading>
            @endif

            @if ($offer->subtitle)
                <p class="text-xs md:tex-sm lg:text-base"><strong>{{ $offer->subtitle }}</strong></p>
            @endif

            <p class="mt-6 mb-3 text-xs md:tex-sm lg:text-base font-semibold">{{ __('messages.email.text-4') }}</p>

            <p class="mb-9 text-xs md:tex-sm lg:text-base">{{ $offer->description }}</p>

            <div class="flex items-center justify-start gap-4 mb-6 text-xs md:tex-sm lg:text-base mt-auto">
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
                        <button type="submit"
                            class="text-themeRed hover:scale-110 transition-transform duration-200 cursor-pointer"><x-icon
                                name="filled-heart" class="w-5 h-auto translate-y-1" /></button>
                    </form>
                @else
                    <form action="{{ route('favorites.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="oferta">
                        <input type="hidden" name="reference_id" value="{{ $offer->id }}">
                        <button type="submit"
                            class="text-themeRed hover:scale-110 transition-transform duration-200 cursor-pointer">
                            <x-icon name="heart" class="w-5 h-auto translate-y-1" />
                        </button>
                    </form>
                @endif
                <p class="flex items-center justify-center gap-1"><x-icon name="graphic"
                        class="w-4 h-auto" />{{ $offer->views }}</p>
                @if (auth()->id() === $offer->company_id)
                        <div x-data="{ open: false }" class="inline-block" x-cloak>
                            <button @click="open = true"
                                class="dark:bg-themeBgDark bg-white border-2 border-themeRed hover:bg-themeRed/20 text-themeRed font-semibold px-2 py-1 2md:px-4 2md:py-2 text-xs lg:text-sm rounded transition cursor-pointer">{{
                    __('messages.button.delete') }}</button>

                            <div x-show="open" x-cloak
                                class="fixed inset-0 bg-black/50 flex items-center justify-center px-10 z-50">
                                <div class="bg-white dark:bg-themeBgDark p-6 rounded shadow-lg w-full max-w-md"
                                    @click.outside="open = false">
                                    <x-heading level="h2"
                                        class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{__('messages.admin.users.heading-confirm')}}</x-heading>
                                    <p class="mb-4 text-gray-600 dark:text-gray-300 break-words">
                                        {{__('messages.admin.offers.delete-text-1')}} <strong>{{ $offer->name }}</strong>
                                        {{ __('messages.admin.offers.delete-text-2') }}
                                    </p>
                                    <div class="flex justify-end gap-4">
                                        <button @click="open = false"
                                            class="px-2 py-1 2md:px-4 2md:py-2 text-xs lg:text-sm bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">
                                            {{ __('messages.button.cancel') }}
                                        </button>

                                        <form action="{{ route('job.offers.destroy', $offer->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2 py-1 2md:px-4 2md:py-2 text-xs lg:text-sm bg-red-600 text-white rounded hover:bg-red-700 cursor-pointer">
                                                {{ __('messages.button.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endif


            </div>

            @if (
                in_array(auth()->user()->role, ['Usuario', 'Alumno']) &&
                $offer->state === 'Abierta' &&
                !$offer->hasApplied(auth()->id())
            )
                <div x-data="{ showModal: false }" x-cloak
                    x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))"
                    class="relative z-10">
                    <button @click="showModal = true"
                        class=" p-2 bg-themeBlue text-white rounded shadow-lg hover:bg-themeHoverBlue transition cursor-pointer">
                        {{__('messages.button.apply')}}
                    </button>

                    <x-modal>
                        <x-heading level="h2"
                            class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{__('messages.apply-form.title')}}</x-heading>

                        <form action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data"
                            class="space-y-4 [&>div>input]:outline-0 [&>div>textarea]:outline-0">
                            @csrf

                            <input type="hidden" name="offer_id" value="{{ $offer->id }}">
                            <div class="flex flex-col gap-2.5">
                                <x-label for="candidate_name">{{ __('messages.apply-form.candidate-name') }}</x-label>
                                <x-inputtext type="text" name="candidate_name" id="candidate_name"
                                    value="{{ auth()->user()->name }}" readonly />

                                <x-label for="position_applied">{{ __('messages.apply-form.position-applied') }}</x-label>
                                <x-inputtext type="text" name="position_applied" id="position_applied"
                                    value="{{ $offer->name }}" readonly />

                                <x-label for="position_applied">{{ __('messages.apply-form.application-reason') }}</x-label>
                                <textarea name="application_reason"
                                    class="w-full h-30 border rounded border-themeLightGray resize-none"
                                    required></textarea><br>


                                <div class="mt-4 mb-8" x-data="{ cvName: '' }" x-cloak>
                                    <x-label for="curriculum">{{ __('messages.apply-form.cv') }}</x-label>

                                    <label for="cv-upload"
                                        class="flex items-center justify-center w-full px-4 py-2 bg-themeBlue text-white font-medium rounded cursor-pointer hover:bg-themeHoverBlue transition">
                                        📄 {{__('messages.apply-form.upload-cv')}}
                                        <input id="cv-upload" type="file" name="cv" accept=".pdf" class="hidden"
                                            @change="cvName = $event.target.files.length ? $event.target.files[0].name : ''">
                                    </label>

                                    <template x-if="cvName">
                                        <p class="mt-2 text-sm text-black dark:text-themeLightGray" x-text="cvName"></p>
                                    </template>
                                </div>

                                <div class="flex justify-end gap-4">
                                    <button type="button" @click="showModal = false"
                                        class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                                        {{__('messages.button.cancel')}}
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-themeBlue/80 text-white rounded hover:bg-themeBlue transition cursor-pointer">
                                        {{__('messages.button.apply')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </x-modal>

                    </form>
                </div>
            @endif
        </div>
        <div class="md:w-1/3 md:flex md:justify-end">
            <ul class=" text-xs lg:text-sm">
                <li class="hidden md:flex mb-5 justify-end">
                    <img src="{{ $offer->company->profile ? asset('storage/' . auth()->user()->profile) : 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Windows_10_Default_Profile_Picture.svg/2048px-Windows_10_Default_Profile_Picture.svg.png' }}"
                        alt="Perfil" id="profileImage" class="size-32 lg:size-40 object-cover">
                </li>
                <li><strong>{{__('messages.project-details.published')}} </strong>{{ $offer->created_at }}</li>
                <li><strong>{{__('messages.roles.company')}}: </strong>
                <a href="{{ route('profile.index', ['id' => $offer->company->id]) }}"
                        class="text-themeBlue hover:underline font-semibold">
                        {{ $offer->company->name }}
                    </a></li>
            </ul>

        </div>

    </x-card>



    <a href="{{ route('job.offers.index') }}"
        class="mt-3 px-2 py-1 2md:px-4 2md:py-2 text-xs lg:text-sm bg-themeBlue text-white hover:bg-themeHoverBlue flex items-center gap-2 w-max rounded transition duration-200 ease-in-out transform hover:scale-101">
        <x-icon name="arrow-left" class="w-5 h-auto" /> {{ __('messages.project-details.back') }}</a>



@endsection