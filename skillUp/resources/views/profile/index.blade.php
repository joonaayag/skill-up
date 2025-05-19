@extends('layouts.app')

@section('title', 'Perfil')

@section('content')
    <x-heading level="h1" class="mb-10">{{ __('messages.profile.title') }}</x-heading>
    <div x-data="{ showModal: false }"
        x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))"
        class="relative z-10 max-w-xl mx-auto bg-white dark:bg-themeBgDark rounded-xl shadow-md overflow-hidden mt-10 border-2 border-themeLightGray">
        
        <div class="relative">
            <img src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : asset('images/defaultBanner.jpg') }}"
                alt="Fondo" class="w-full h-40 object-cover" id="bannerImage">

            <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                <img src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : asset('images/defaultProfile.png') }}"
                    alt="Perfil" id="profileImage"
                    class="h-24 w-24 rounded-full border-4 border-themeBlue dark:border-white object-cover shadow-lg">
            </div>
        </div>

        <div class="pt-8 pb-6 px-6 text-center ">

            <h2 class="text-2xl font-bold">{{ auth()->user()->name }} {{ auth()->user()->last_name }}</h2>
            <span
                class="inline-block mt-2 px-3 py-1 text-sm text-white bg-green-500 rounded-full">
                @switch(auth()->user()->role)
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
                $details = auth()->user()->detail;
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
                <div class="flex gap-2">
                    <div class="w-1/2 ">
                        <label for="name" class="text-md font-bold">{{ __('messages.profile.name') }}</label>
                        <div class=" text-dark ml-2">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="w-1/2">
                        <label for="name" class="text-md font-bold">{{ __('messages.profile.last-name') }}</label>
                        <div class=" text-dark ml-2">{{ auth()->user()->last_name }}</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/2">
                        <label for="email" class="text-md font-bold">{{ __('messages.profile.email') }}</label>
                        <div class="text-dark ml-2">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <div>
                    <label for="email" class="text-md font-bold">{{ __('messages.profile.description') }}</label>
                    <div class="text-dark text-sm mt-1 ml-2">
                        {{ auth()->user()->description ?? __('messages.profile.no-specify') }}
                    </div>
                </div>
                @if (auth()->user()->role === 'Alumno')
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">{{ __('messages.profile.birth-date') }}</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->birth_date ?? __('messages.profile.no-specify') }}</div>
                        </div>
                        <div class="w-1/2 gap-2">
                            <label for="email" class="text-md font-bold">{{ __('messages.profile.current-course') }}</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->current_course ?? __('messages.profile.no-specify') }}</div>
                        </div>
                    </div>
                    <div class="">
                        <label for="email" class="text-md font-bold">{{ __('messages.profile.educational-center') }}</label>
                        <div class="w-1/2 text-dark ml-2">{{ $details?->educational_center ?? __('messages.profile.no-specify') }}</div>
                    </div>
                @elseif (auth()->user()->role === 'Profesor')
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">{{ __('messages.profile.specialization') }}</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->specialization ?? __('messages.profile.no-specify') }}</div>
                        </div>
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">{{ __('messages.profile.department') }}</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->department ?? __('messages.profile.no-specify') }}</div>
                        </div>
                    </div>
                    <div class="flex">

                    </div>
                @elseif (auth()->user()->role === 'Empresa')
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">CIF</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->cif ?? __('messages.profile.no-specify') }}</div>
                        </div>
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">{{ __('messages.profile.address') }}</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->address ?? __('messages.profile.no-specify') }}</div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">{{ __('messages.profile.sector') }}</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->sector ?? __('messages.profile.no-specify') }}</div>
                        </div>
                        <div class="w-1/2">
                            <label for="email" class="text-md font-bold">{{ __('messages.profile.website') }}</label>
                            <div class="w-1/2 text-dark ml-2">{{ $details?->website ?? __('messages.profile.no-specify') }}</div>
                        </div>
                    </div>
                @endif

                <label for="email" class="text-md font-bold">Currículum</label>
                @if(auth()->user()->cv)
                    <div class="text-dark text-sm mt-1">
                        <p>📄 <a href="{{ asset('storage/' . auth()->user()->cv) }}" target="_blank"
                                class="underline text-blue-600">
                                {{ __('messages.profile.see-cv') }}
                            </a></p>
                    </div>
                @else
                    <p class="text-gray-500 ml-2">{{ __('messages.profile.no-cv') }}</p>
                @endif
            </div>

            <div>
                <button @click="showModal = true"
                    class="mt-6 bg-themeBlue border-2 border-themeBlue hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition cursor-pointer">
                    {{ __('messages.profile.edit-profile') }}
                </button>
                <div x-cloak x-data="{ open: false }" class="inline-block" x-cloak>
                    <button @click="open = true" class="mt-6 dark:bg-themeBgDark bg-white border-2 border-themeRed hover:bg-themeRed/20 text-themeRed font-semibold py-2 px-4 rounded transition cursor-pointer">{{ __('messages.profile.logout') }}</button>

                    <div x-show="open" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                        <div class="bg-white dark:bg-themeBgDark p-6 rounded shadow-lg w-full max-w-md"
                            @click.outside="open = false">
                            <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.profile.logout') }}</x-heading>
                            <p class="mb-4 text-gray-600 dark:text-gray-300 break-words">
                                {{ __('messages.profile.confirm-logout') }} <strong>{{ auth()->user()->name }} {{ auth()->user()->last_name}}</strong>?
                            </p>
                            <div class="flex justify-end gap-4">
                                <button @click="open = false"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition cursor-pointer">
                                    {{ __('messages.button.cancel') }}
                                </button>

                                <form action="{{ route('user.logout', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition cursor-pointer">
                                        {{ __('messages.profile.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div x-cloak x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))">
            <x-modal>
                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.profile.edit-profile') }}</x-heading>
                <form action="{{ route('user.update', auth()->id()) }}" method="POST" enctype="multipart/form-data"
                    class="max-w-2xl mx-auto p-6 rounded shadow [&>div>div>input]:border-themeLightGray [&>div>input]:border-themeLightGray [&>div>textarea]:border-themeLightGray">
                    @csrf
                    @method('PUT')

                    <div class="relative mb-8">
                        <img id="bannerPreview"
                            src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : asset('images/defaultBanner.jpg') }}"
                            class="w-full h-40 object-cover cursor-pointer" alt="Banner">
                        <input type="file" name="banner" id="bannerInput" accept="image/*" class="hidden">

                        <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                            <img id="fotoPerfilPreview"
                                src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : asset('images/defaultProfile.png') }}"
                                class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg cursor-pointer"
                                alt="Foto de perfil">
                            <input type="file" name="profile" id="fotoPerfilInput" accept="image/*" class="hidden">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-name') }}</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-last-name') }}</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium">{{ __('messages.profile.label-email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium">{{ __('messages.profile.label-description') }}</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2"
                            rows="4">{{ old('description', $user->description) }}</textarea>
                    </div>

                    @if ($user->role === 'Alumno')
                        <div class="mt-4">
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-birth-date') }}</label>
                            <input type="date" name="birth_date"
                                value="{{ old('birth_date', $user->detail->birth_date ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-current-course') }}</label>
                            <input type="text" name="current_course"
                                value="{{ old('current_course', $user->detail->current_course ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-educational-center') }}</label>
                            <input type="text" name="educational_center"
                                value="{{ old('educational_center', $user->detail->educational_center ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                    @endif

                    @if ($user->role === 'Profesor')
                        <div class="mt-4">
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-specialization') }}</label>
                            <input type="text" name="specialization"
                                value="{{ old('specialization', $user->detail->specialization ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-department') }}</label>
                            <input type="text" name="department"
                                value="{{ old('department', $user->detail->department ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                    @endif

                    @if ($user->role === 'Empresa')
                        <div class="mt-4">
                            <label class="block text-sm font-medium">CIF</label>
                            <input type="text" name="cif" value="{{ old('cif', $user->detail->cif ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-address') }}</label>
                            <input type="text" name="address" value="{{ old('address', $user->detail->address ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-sector') }}</label>
                            <input type="text" name="sector" value="{{ old('sector', $user->detail->sector ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-website') }}</label>
                            <input type="url" name="website" value="{{ old('website', $user->detail->website ?? '') }}"
                                class="w-full border rounded px-3 py-2">
                        </div>
                    @endif

                    <div class="mt-4" x-data="{ cvName: '' }" x-cloak>
                        <label class="block text-sm font-medium">{{ __('messages.profile.upload-cv') }}</label>

                        <label for="cv-upload"
                            class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/80 transition">
                            {{ __('messages.profile.upload-cv') }}
                            <input id="cv-upload" type="file" name="cv" accept=".pdf" class="hidden"
                                @change="cvName = $event.target.files.length ? $event.target.files[0].name : ''">
                        </label>

                        <template x-if="cvName">
                            <p class="mt-2 text-sm text-black dark:text-themeLightGray" x-text="cvName"></p>
                        </template>
                    </div>


                    <div class="mt-6 flex justify-end gap-4">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                            {{ __('messages.button.cancel') }}
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            {{ __('messages.button.save-changes') }}
                        </button>
                    </div>
                </form>
            </x-modal>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const bannerImage = document.getElementById('bannerImage');
            const profileImage = document.getElementById('profileImage');

            const bannerInput = document.getElementById('bannerInput');
            const bannerPreview = document.getElementById('bannerPreview');

            const fotoPerfilInput = document.getElementById('fotoPerfilInput');
            const fotoPerfilPreview = document.getElementById('fotoPerfilPreview');

            bannerPreview.addEventListener('click', () => bannerInput.click());
            fotoPerfilPreview.addEventListener('click', () => fotoPerfilInput.click());

            bannerInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        bannerPreview.src = e.target.result;
                        bannerImage.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });

            fotoPerfilInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        fotoPerfilPreview.src = e.target.result;
                        profileImage.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>

@endsection