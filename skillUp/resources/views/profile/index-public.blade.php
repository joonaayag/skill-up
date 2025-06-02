@extends('layouts.app')

@section('title', __('messages.titles.profile'))

@section('content')
    <x-heading level="h1" class="mb-10">{{ __('messages.profile.title') }}</x-heading>
    <div class="relative z-10 max-w-xl mx-auto bg-white dark:bg-themeBgDark rounded-xl shadow-md overflow-hidden mt-10 border-2 border-themeLightGray">
        
        <div class="relative">
            <img src="{{ $user->banner ? Storage::disk('s3')->url($user->banner) : asset('images/defaultBanner.jpg') }}""
                alt="Fondo" class="w-full h-20 md:h-40 object-cover" id="bannerImage">

            <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                <img src="{{ $user->profile ? Storage::disk('s3')->url($user->profile) : asset('images/defaultProfile.png') }}"
                    alt="Perfil" id="profileImage"
                    class="h-16 w-16 md:h-24 md:w-24 rounded-full border-4 border-themeBlue dark:border-white object-cover shadow-lg">
            </div>
        </div>

        <div class="pt-8 pb-6 px-6 text-center ">

            <h2 class="text-2xl font-bold">{{ $user->name }} {{ $user->last_name }}</h2>
            <span
                class="inline-block mt-2 px-3 py-1 text-xs md:text-sm text-white bg-green-500 rounded-full">
                @switch($user->role)
                    @case('Alumno')
                        {{ __('messages.roles.student') }}
                        @break
                    @case('Usuario')
                        {{ __('messages.roles.user') }}
                        @break
                    @case('Profesor')
                        {{ __('messages.roles.teacher') }}
                        @break
                    @case('Empresa')
                        {{ __('messages.roles.company') }}
                    @break
                    @case('Admin')
                        <span>Admin</span>
                    @break
                @endswitch
            </span>
            @php
                $details = $user->detail;
            @endphp

            <div class="mt-6 text-left space-y-4 dark:[&>div>div>div]:text-themeLightGray">
                @if ($errors->any())
                    <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class="text-black dark:text-white">- {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="flex gap-2 text-xs md:tex-sm lg:text-base">
                    <div class="w-1/2 ">
                        <x-label for="name">{{ __('messages.profile.name') }}</x-label>
                        <div class=" text-dark ml-2">{{ $user->name }}</div>
                    </div>
                    <div class="w-1/2">
                        <x-label for="last_name">{{ __('messages.profile.last-name') }}</x-label>
                        <div class=" text-dark ml-2">{{ $user->last_name }}</div>
                    </div>
                </div>
                <div class="flex text-xs md:tex-sm lg:text-base">
                    <div class="w-1/2">
                        <x-label for="email">{{ __('messages.profile.email') }}</x-label>
                        <div class="text-dark ml-2">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="text-xs md:tex-sm lg:text-base">
                    <x-label for="description">{{ __('messages.profile.description') }}</x-label>
                    <div class="text-dark text-sm mt-1 ml-2">
                        {{ $user->description ?? __('messages.profile.no-specify') }}
                    </div>
                </div>
                @if ($user->role === 'Alumno')
                    <div class="flex gap-2 text-xs md:tex-sm lg:text-base">
                        <div class="w-1/2">
                            <x-label for="email">{{ __('messages.profile.birth-date') }}</x-label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->birth_date ?? __('messages.profile.no-specify') }}</div>
                        </div>
                        <div class="w-1/2 gap-2">
                            <x-label for="current_course">{{ __('messages.profile.current-course') }}</x-label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->current_course ?? __('messages.profile.no-specify') }}</div>
                        </div>
                    </div>
                    <div class="text-xs md:tex-sm lg:text-base">
                        <x-label for="educational_center">{{ __('messages.profile.educational-center') }}</x-label>
                        <div class="w-1/2 text-dark ml-2">{{ $details?->educational_center ?? __('messages.profile.no-specify') }}</div>
                    </div>
                @elseif ($user->role === 'Profesor')
                    <div class="flex gap-2 text-xs md:tex-sm lg:text-base">
                        <div class="w-1/2">
                            <x-label for="specialization">{{ __('messages.profile.specialization') }}</x-label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->specialization ?? __('messages.profile.no-specify') }}</div>
                        </div>
                        <div class="w-1/2">
                            <x-label for="department">{{ __('messages.profile.department') }}</x-label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->department ?? __('messages.profile.no-specify') }}</div>
                        </div>
                    </div>
                    <div class="text-xs md:tex-sm lg:text-base">
                        <x-label for="educational_center">{{ __('messages.profile.educational-center') }}</x-label>
                        <div class="w-1/2 text-dark ml-2">{{ $details?->educational_center ?? __('messages.profile.no-specify') }}</div>
                    </div>
                @elseif ($user->role === 'Empresa')
                    <div class="flex gap-2 text-xs md:tex-sm lg:text-base">
                        <div class="w-1/2">
                            <x-label for="cif">CIF</x-label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->cif ?? __('messages.profile.no-specify') }}</div>
                        </div>
                        <div class="w-1/2">
                            <x-label for="address">{{ __('messages.profile.address') }}</x-label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->address ?? __('messages.profile.no-specify') }}</div>
                        </div>
                    </div>
                    <div class="flex gap-2 text-xs md:tex-sm lg:text-base">
                        <div class="w-1/2">
                            <x-label for="sector">{{ __('messages.profile.sector') }}</x-label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->sector ?? __('messages.profile.no-specify') }}</div>
                        </div>
                        <div class="w-1/2">
                            <x-label for="website">{{ __('messages.profile.website') }}</x-label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->website ?? __('messages.profile.no-specify') }}</div>
                        </div>
                    </div>
                @endif

                <x-label for="cv">Curriculum</x-label>
                @if($user->cv)
                    <div class="text-dark text-xs md:tex-sm lg:text-base mt-1">
                        <p>📄 <a href="{{ asset('storage/' . $user->cv) }}" target="_blank"
                                class="underline text-blue-600">
                                {{ __('messages.profile.see-cv') }}
                            </a></p>
                    </div>
                @else
                    <p class="text-gray-500 ml-2">{{ __('messages.profile.no-cv') }}</p>
                @endif
            </div>

        </div>
    </div>

@endsection