@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <x-heading level="h1" class="mb-10 ">{{ __('messages.dashboard.hi') }}, {{ auth()->user()->name }}</x-heading>
    @if ($errors->any())
        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-black dark:text-white">- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 2md:grid-cols-2 xl:grid-cols-3 gap-14 h-full mb-24 transition-all duration-300 ease-in-out">

        <x-card class="order-2 2md:order-1">
            <x-heading level="h3" class="mb-8">{{ __('messages.dashboard.highlight-projects') }}</x-heading>
            <div class="flex flex-col gap-3">
                @if ($combined->isNotEmpty())
                    @foreach($combined as $project)
                        <a href="{{ route('projects.show', $project->id) }}">
                            <div
                                class="flex items-center space-x-4 hover:bg-themeLightGray/20 cursor-pointer p-1 rounded-lg transition">
                                <div class="bg-blue-100 p-2 rounded-md">
                                    <x-icon name="project" class="w-8 h-auto" />
                                </div>
                                <div>
                                    <x-heading level="h4">{{ $project->title }}</x-heading>
                                    <p class=" text-xs text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
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

                                            $categoryKey = $categoryMap[$project->general_category] ?? null;
                                        @endphp

                                        @if ($categoryKey)
                                            {{ __('messages.projects.' . $categoryKey) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <p class="text-xs mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                        {{ __('messages.dashboard.no-projects') }}.
                    </p>
                @endif
            </div>
        </x-card>

        <div class="grid grid-rows-5 gap-8 order-3 2md:order-2 transition-all duration-300 ease-in-out">

            <x-card class="row-span-2 h-full">
                <x-heading level="h3" class="mb-8">{{ __('messages.dashboard.your-projects') }}</x-heading>
                <div class="flex flex-col gap-3">
                    @if ($ownProjects->isNotEmpty())
                        @foreach($ownProjects as $project)
                            <a href="{{ route('projects.show', $project->id) }}">
                                <div
                                    class="flex items-center space-x-4 hover:bg-themeLightGray/20 cursor-pointer p-1 rounded-lg transition">
                                    <div class="bg-blue-100 p-2 rounded-md">
                                        <x-icon name="project" class="w-8 h-auto" />
                                    </div>
                                    <div>
                                        <strong>{{ $project->title }}</strong>
                                        <p class="text-xs text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
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

                                                $categoryKey = $categoryMap[$project->general_category] ?? null;
                                            @endphp

                                            @if ($categoryKey)
                                                {{ __('messages.projects.' . $categoryKey) }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                            {{ __('messages.dashboard.no-own-projects') }}.
                        </p>
                    @endif
                </div>
            </x-card>
            <x-card class="row-span-3 h-full">
                <x-heading level="h3" class="mb-8">{{ __('messages.dashboard.last-offers') }}</x-heading>
                <div class="flex flex-col gap-3">
                    @if ($jobOffers->isNotEmpty())
                        @foreach($jobOffers as $offer)
                            <a href="{{ route('job.offers.show', $offer->id) }}">
                                <div
                                    class="flex items-center space-x-4 hover:bg-themeLightGray/20 cursor-pointer p-1 rounded-lg transition">
                                    <div class="bg-blue-100 p-2 rounded-md">
                                        <x-icon name="briefcase" class="w-8 h-auto dark:text-black" />
                                    </div>
                                    <div class="[&>p]:mt-1">
                                        <strong>{{ $offer->name }}</strong>
                                        <p class="text-xs text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                                            {{ $offer->created_at->diffForHumans() }} - {{ $offer->company->name }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                            {{ __('messages.dashboard.no-offers') }}.
                        </p>
                    @endif
                </div>
            </x-card>
        </div>

        <div class="grid grid-rows-7 gap-8 xl:-mt-20 order-1 2md:order-3 transition-all duration-300 ease-in-out">

            <div
                class="row-span-3 h-full order-2 xl:order-1 [&>div]:h-full [&>div]:bg-white [&>div]:border-2 [&>div]:border-themeLightGray [&>div]:rounded-lg dark:[&>div]:bg-themeBgDark">
                <div class="relative">
                    <img src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : asset('images/defaultBanner.jpg')  }}"
                        alt="Fondo" class="w-full h-30 rounded-t-md object-cover" id="bannerImage">
                    <div class="absolute top-20 left-1/6 transform -translate-x-1/2">
                        <img src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : asset('images/defaultProfile.png') }}"
                            alt="Perfil" id="profileImage"
                            class="h-18 w-18 rounded-full border-4 border-white object-cover shadow-lg">

                    </div>
                    <div class="px-3 mt-8">
                        <x-heading level="h3">{{ auth()->user()->name . ' ' . auth()->user()->last_name }}</x-heading>
                        @if (auth()->user()->role === 'Alumno')
                            <p>{{ __('messages.dashboard.student-of') }} {{ auth()->user()->detail->educational_center }}</p>
                        @else
                            <p>{{ auth()->user()->role }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <x-card class="row-span-4 h-full order-1 xl:order-2">
                <div id="dashboard-notification-list">
                    <x-heading level="h3" class="mb-8">{{ __('messages.dashboard.recent-notifications') }}</x-heading>
                    @if($notifications->count())
                        @foreach($notifications as $notification)
                            <div
                                class="flex items-center space-x-4 leading-card mb-2.5 hover:bg-themeLightGray/20 cursor-pointer p-1 rounded-lg transition">
                                <div class="bg-themeGrape text-white p-2 rounded-full">
                                    <x-icon name="bell" class="w-8 h-auto" />
                                </div>
                                <div class="[&>p]:mt-1">
                                    <p class="font-semibold">{{ $notification->message }}</p>
                                    <p class="text-xs text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="mt-6 text-themeSmallTextLightGray dark:text-darkThemeSmallTextLightGray">
                            {{ __('messages.dashboard.no-notifications') }}
                        </p>
                    @endif
                </div>
            </x-card>


        </div>

    </div>

@endsection