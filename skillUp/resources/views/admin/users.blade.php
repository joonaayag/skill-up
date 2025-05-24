@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-10">
        <x-heading level="h1" class="mb-10">{{ __('messages.admin.users.title') }}</x-heading>

        
        @if(session('errors'))
            <div class="alert alert-danger">
                <strong>Se encontraron errores al importar:</strong>
                <ul>
                    @foreach (session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-lg shadow">
            <table
                class="min-w-full bg-white dark:bg-themeDarkGray text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 border dark:border-gray-700">ID</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.users.table-name') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.users.table-last-name') }}
                        </th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.users.table-email') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.users.table-role') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.users.table-actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->id }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->name }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->last_name }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->email }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">
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
                            </td>
                            <td class="px-4 py-3 border dark:border-gray-700 space-x-2 whitespace-nowrap">

                                <div x-data="{ openEdit: false, role: '{{ $user->role }}' }" class="inline-block" x-cloak>
                                    <button @click="openEdit = true"
                                        class="bg-themeBlue border-2 border-themeBlue hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition cursor-pointer">{{ __('messages.button.edit') }}</button>

                                    <x-modal :show="'openEdit'" @click.outside="openEdit = false">
                                        <x-heading level="h2"
                                            class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.users.edit') }}</x-heading>
                                        <form action="{{ route('admin.user.update', $user->id) }}" method="POST"
                                            enctype="multipart/form-data"
                                            class="max-w-2xl mx-auto dark:bg-themeBgDark bg-white p-6 rounded shadow "
                                            x-data="{ role: '{{ old('role', $user->role) }}' }"
>
                                            @csrf
                                            @method('PUT')

                                            <div id="form-errors" class="bg-red-200 text-red-700 p-4 rounded mb-4 hidden">
                                                <ul class="list-disc list-inside"></ul>
                                            </div>


                                            <div class="relative mb-8">
                                                <img src="{{ $user->banner ? asset('storage/' . $user->banner) : asset('images/defaultBanner.jpg') }}"
                                                    class="w-full h-40 object-cover cursor-pointer banner-preview" alt="Banner">
                                                <input type="file" name="banner" accept="image/*" class="hidden banner-input">

                                                <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                                                    <img src="{{ $user->profile ? asset('storage/' . $user->profile) : asset('images/defaultProfile.png') }}"
                                                        class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg cursor-pointer profile-preview"
                                                        alt="Foto de perfil">
                                                    <input type="file" name="profile" accept="image/*"
                                                        class="hidden profile-input">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium">{{ __('messages.profile.label-name') }}</label>
                                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                                        class="w-full border rounded px-3 py-2" required>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium">{{ __('messages.profile.label-last-name') }}</label>
                                                    <input type="text" name="last_name"
                                                        value="{{ old('last_name', $user->last_name) }}"
                                                        class="w-full border rounded px-3 py-2">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4 mt-4">
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium">{{ __('messages.profile.label-email') }}</label>
                                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                                        class="w-full border rounded px-3 py-2" required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium">Rol</label>
                                                    <select name="role"
                                                        class="w-full border rounded px-3 py-2 dark:bg-themeBgDark"
                                                        x-model="role">
                                                        @php
                                                            $roleMap = [
                                                                'Usuario' => 'user',
                                                                'Alumno' => 'student',
                                                                'Profesor' => 'teacher',
                                                                'Empresa' => 'company',
                                                            ];
                                                        @endphp

                                                        <option value="Usuario" {{ old('role') == 'Usuario' ? 'selected' : '' }}>
                                                            {{ __('messages.roles.' . ($roleMap['Usuario'] ?? 'Usuario')) }}
                                                        </option>
                                                        <option value="Alumno" {{ old('role') == 'Alumno' ? 'selected' : '' }}>
                                                            {{ __('messages.roles.' . ($roleMap['Alumno'] ?? 'Alumno')) }}
                                                        </option>
                                                        <option value="Profesor" {{ old('role') == 'Profesor' ? 'selected' : '' }}>
                                                            {{ __('messages.roles.' . ($roleMap['Profesor'] ?? 'Profesor')) }}
                                                        </option>
                                                        <option value="Empresa" {{ old('role') == 'Empresa' ? 'selected' : '' }}>
                                                            {{ __('messages.roles.' . ($roleMap['Empresa'] ?? 'Empresa')) }}
                                                        </option>

                                                    </select>

                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <label
                                                    class="block text-sm font-medium">{{ __('messages.profile.label-description') }}</label>
                                                <textarea name="description" class="w-full border rounded px-3 py-2 resize-none"
                                                    rows="4">{{ old('description', $user->description) }}</textarea>
                                            </div>

                                            <template x-if="role === 'Alumno'">
                                                <div class="space-y-4 mt-4">
                                                    <div>
                                                        <label class="block text-sm font-medium">{{ __('messages.profile.label-birth-date') }}</label>
                                                        <input type="date" name="birthDate" value="{{ old('birthDate', $user->detail?->birth_date) }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium">{{ __('messages.profile.label-current-course') }}</label>
                                                        <input type="text" name="currentCourse"
                                                            value="{{ old('currentCourse', $user->detail?->current_course) }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium">{{ __('messages.profile.label-educational-center') }}</label>
                                                        <input type="text" name="educationalCenter"
                                                            value="{{ old('educationalCenter', $user->detail?->educational_center) }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                </div>
                                            </template>


                                            <template x-if="role === 'Profesor'">
                                                <div class="space-y-4 mt-4">
                                                    <div>
                                                        <label class="block text-sm font-medium">{{ __('messages.profile.label-educational-center') }}</label>
                                                        <input type="text" name="educationalCenter"
                                                            value="{{ old('educationalCenter', $user->detail?->educational_center) }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium">{{ __('messages.profile.label-specialization') }}</label>
                                                        <input type="text" name="specialization"
                                                            value="{{ old('specialization', $user->detail?->specialization) }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium">{{ __('messages.profile.label-department') }}</label>
                                                        <input type="text" name="department"
                                                            value="{{ old('department', $user->detail?->department) }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                    
                                                </div>
                                            </template>


                                            <template x-if="role === 'Empresa'">
                                                <div class="space-y-4 mt-4">
                                                    <div>
                                                        <label class="block text-sm font-medium">CIF</label>
                                                        <input type="text" name="cif" value="{{ old('cif', $user->detail?->cif) }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium">{{ __('messages.profile.label-address') }}</label>
                                                        <input type="text" name="address" value="{{ old('address', $user->detail?->address) }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium">{{ __('messages.profile.label-sector') }}</label>
                                                        <input type="text" name="sector" value="{{ old('sector', $user->detail?->sector) }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium">{{ __('messages.profile.label-website') }}</label>
                                                        <input type="url" name="website" value="{{ old('website', $user->detail?->website) }}"
                                                            class="w-full border rounded px-3 py-2">
                                                    </div>
                                                </div>
                                            </template>


                                            <div class="mt-4" x-data="{ cvName: '' }" x-cloak>
                                                <label
                                                    class="block text-sm font-medium">{{ __('messages.profile.upload-cv') }}</label>

                                                <label for="cv-upload"
                                                    class="flex items-center justify-center w-full px-4 py-2 bg-themeGrape text-white font-medium rounded cursor-pointer hover:bg-themeGrape/80 transition">
                                                    {{ __('messages.profile.upload-cv') }}
                                                    <input id="cv-upload" type="file" name="cv" accept=".pdf" class="hidden"
                                                        @change="cvName = $event.target.files.length ? $event.target.files[0].name : ''">
                                                </label>

                                                <template x-if="cvName">
                                                    <p class="mt-2 text-sm text-black dark:text-themeLightGray" x-text="cvName">
                                                    </p>
                                                </template>
                                            </div>


                                            <div class="mt-6 flex justify-end gap-4">
                                                <button type="button" @click="openEdit = false"
                                                    class="px-4 py-2 bg-themeLightGray text-gray-800 rounded hover:bg-gray-400 transition cursor-pointer">
                                                    {{ __('messages.button.cancel') }}
                                                </button>
                                                <button type="submit"
                                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                                    {{ __('messages.button.save-changes') }}
                                                </button>
                                            </div>
                                        </form>
                                    </x-modal>

                                </div>

                                <div x-data="{ open: false }" x-cloak class="inline-block">
                                    <button @click="open = true"
                                        class="bg-red-500 border-2 border-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded transition cursor-pointer">{{ __('messages.button.delete') }}</button>

                                    <x-modal :show="'open'" @close="open = false">
                                        <x-heading level="h2"
                                            class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.users.heading-confirm') }}</x-heading>
                                        <p class="mb-4 text-gray-600 dark:text-gray-300 break-words">
                                            {{ __('messages.admin.users.text-confirm-1') }} <strong>{{ $user->name }}
                                                {{ $user->last_name }}</strong> {{ __('messages.admin.users.text-confirm-2') }}
                                        </p>
                                        <div class="flex justify-end gap-4">
                                            <button @click="open = false"
                                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-sm rounded hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">
                                                {{ __('messages.button.cancel') }}
                                            </button>

                                            <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700 cursor-pointer">
                                                    {{ __('messages.button.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </x-modal>

                                </div>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-5 text-center text-gray-500 dark:text-gray-300">
                                {{ __('messages.admin.users.no-users') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        <div x-data="{ showCreateUser: false }" x-cloak class="flex gap-2 mt-5">
            
            <x-modal :show="'showCreateUser'" @close="showCreateUser = false">
                <x-heading level="h2"
                class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.users.create-user') }}</x-heading>
                <form method="POST" action="{{ route('admin.register') }}" enctype="multipart/form-data"
                class="max-w-2xl mx-auto dark:bg-themeBgDark bg-white p-6 rounded shadow space-y-6"
                x-data="{ role: '{{ old('role') }}' }">
                @csrf
                
                <div class="relative mb-8">
                        <img src="{{ asset('images/defaultBanner.jpg') }}"
                        class="w-full h-40 object-cover cursor-pointer banner-preview" alt="Banner">
                        <input type="file" name="banner" accept="image/*" class="hidden banner-input">

                        <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                            <img src="{{ asset('images/defaultProfile.png') }}"
                            class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg cursor-pointer profile-preview"
                            alt="Foto de perfil">
                            <input type="file" name="profile" accept="image/*" class="hidden profile-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-name') }}</label>
                            <input type="text" name="name" placeholder="{{ __('messages.admin.users.ph-name') }}"
                            value="{{ old('name') }}" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">{{ __('messages.profile.label-last-name') }}</label>
                            <input type="text" name="lastName" placeholder="{{ __('messages.admin.users.ph-last-name') }}"
                                value="{{ old('lastName') }}" required class="w-full border rounded px-3 py-2">
                            </div>
                        </div>

                    <div>
                        <label class="block text-sm font-medium">{{ __('messages.profile.label-email') }}</label>
                        <input type="email" name="email" placeholder="{{ __('messages.admin.users.ph-email') }}"
                            value="{{ old('email') }}" required class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">{{ __('messages.admin.users.ph-password') }}</label>
                            <input type="password" name="password" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label
                            class="block text-sm font-medium">{{ __('messages.admin.users.ph-password-confirmation') }}</label>
                            <input type="password" name="password_confirmation" required
                            class="w-full border rounded px-3 py-2">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">{{ __('messages.admin.users.select-role') }}</label>
                        <select name="role" x-model="role" required class="w-full border rounded px-3 py-2 dark:bg-themeBgDark">
                            <option value="">{{ __('messages.admin.users.select-role') }}</option>
                            @php
                                $roleMap = [
                                    'Usuario' => 'user',
                                    'Alumno' => 'student',
                                    'Profesor' => 'teacher',
                                    'Empresa' => 'company',
                                ];
                            @endphp

                            <option value="Usuario" {{ old('role') == 'Usuario' ? 'selected' : '' }}>
                                {{ __('messages.roles.' . ($roleMap['Usuario'] )) }}
                            </option>
                            <option value="Alumno" {{ old('role') == 'Alumno' ? 'selected' : '' }}>
                                {{ __('messages.roles.' . ($roleMap['Alumno'] )) }}
                            </option>
                            <option value="Profesor" {{ old('role') == 'Profesor' ? 'selected' : '' }}>
                                {{ __('messages.roles.' . ($roleMap['Profesor'] )) }}
                            </option>
                            <option value="Empresa" {{ old('role') == 'Empresa' ? 'selected' : '' }}>
                                {{ __('messages.roles.' . ($roleMap['Empresa'] )) }}
                            </option>
                            
                        </select>
                    </div>
                    
                    <template x-if="role === 'Alumno'">
                        <div class="space-y-4">
                            <div>
                                <label
                                class="block text-sm font-medium">{{ __('messages.profile.label-birth-date') }}</label>
                                <input type="date" name="birthDate" value="{{ old('birthDate') }}"
                                class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label
                                class="block text-sm font-medium">{{ __('messages.profile.label-current-course') }}</label>
                                <input type="text" name="currentCourse" value="{{ old('currentCourse') }}"
                                placeholder="{{ __('messages.admin.users.ph-current-course') }}"
                                class="w-full border rounded px-3 py-2">
                            </div>
                            @if(auth()->user()->role !== 'Profesor')
                                <div>
                                    <label
                                    class="block text-sm font-medium">{{ __('messages.profile.label-educational-center') }}</label>
                                    <input type="text" name="educationalCenter" value="{{ old('educationalCenter') }}"
                                    placeholder="{{ __('messages.admin.users.ph-educational-center') }}"
                                    class="w-full border rounded px-3 py-2">
                                </div>
                            @endif

                        </div>
                    </template>

                    <template x-if="role === 'Profesor'">
                        <div class="space-y-4">
                            <div>
                                <label
                                class="block text-sm font-medium">{{ __('messages.profile.label-educational-center') }}</label>
                                <input type="text" name="educationalCenter" value="{{ old('educationalCenter') }}"
                                placeholder="{{ __('messages.admin.users.ph-educational-center') }}"
                                class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label
                                class="block text-sm font-medium">{{ __('messages.profile.label-specialization') }}</label>
                                <input type="text" name="specialization" value="{{ old('specialization') }}"
                                placeholder="{{ __('messages.admin.users.ph-specialization') }}"
                                class="w-full border rounded px-3 py-2">
                            </div>
                            <div>
                                <label
                                class="block text-sm font-medium">{{ __('messages.profile.label-department') }}</label>
                                <input type="text" name="department" value="{{ old('department') }}"
                                placeholder="{{ __('messages.admin.users.ph-department') }}"
                                class="w-full border rounded px-3 py-2">
                            </div>
                        </div>
                    </template>
                    
                    <template x-if="role === 'Empresa'">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium">CIF</label>
                                <input type="text" name="cif" value="{{ old('cif') }}"
                                placeholder="{{ __('messages.admin.users.ph-cif') }}"
                                    class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">{{ __('messages.profile.label-address') }}</label>
                                    <input type="text" name="address" value="{{ old('address') }}"
                                    placeholder="{{ __('messages.admin.users.ph-address') }}"
                                    class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">{{ __('messages.profile.label-sector') }}</label>
                                    <input type="text" name="sector" value="{{ old('sector') }}"
                                    placeholder="{{ __('messages.admin.users.ph-sector') }}"
                                    class="w-full border rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium">{{ __('messages.profile.label-website') }}</label>
                                    <input type="url" name="website" value="{{ old('website') }}"
                                    placeholder="{{ __('messages.admin.users.ph-website') }}"
                                    class="w-full border rounded px-3 py-2">
                                </div>
                            </div>
                    </template>

                    <div x-data="{ cvName: '' }" x-cloak>
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
                
                <div class="flex justify-end pt-4 gap-2">
                    <button type="button" @click="showCreateUser = false"
                    class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition cursor-pointer">
                    {{ __('messages.button.cancel') }}
                </button>
                <button type="submit"
                class="bg-themeBlue/80 text-white px-4 py-2 rounded hover:bg-themeBlue transition cursor-pointer">
                {{ __('messages.button.register') }}
            </button>
        </div>
                </form>
    
            </x-modal>
            <button @click="showCreateUser = true"
                class="flex gap-2 items-center bg-themeBlue/80  hover:bg-themeBlue text-white font-semibold px-4 rounded-lg transition cursor-pointer"><x-icon
                name="plus" class="w-5 h-auto" /> {{ __('messages.button.create') }}
            </button>

        
            @if (auth()->user()->role == 'Profesor')
                <div x-data="importStudentsModal()" x-cloak>
                    <form x-ref="uploadForm" action="{{ route('professor.import.students') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="flex items-center justify-center w-full px-4 py-4 bg-themeGrape text-white font-medium rounded-lg cursor-pointer hover:bg-themeGrape/80 transition">
                            <input type="file" name="students_file" id="students_file" accept=".txt" required class="hidden" @change="handleFileChosen($event)">
                            Importar alumnos
                        </label>
                    </form>

                    <div :show="'confirmUpload'" @close="confirmUpload = false">
                        <x-modal :show="'confirmUpload'" @close="confirmUpload = false">
                                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">¿Estás seguro?</x-heading>
                                <p class="text-center text-gray-800 dark:text-themeLightGray">¿Quieres importar los alumnos de este archivo?</p>
                                <p x-show="fileName" class="mt-2 mb-10 text-sm text-center text-gray-700 dark:text-themeLightGray">
                                    Archivo seleccionado: <span class="font-semibold" x-text="fileName"></span>
                                </p>

                                <button @click="confirmUpload = false"
                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                                {{ __('Cancelar') }}
                                </button>
                                <button @click="submitForm"
                                class="px-4 py-2 bg-themeBlue text-white rounded hover:bg-themeBlue/90 transition">
                                {{ __('Subir') }}
                                </button>
                        </x-modal>
                    </div>
                </div>

                @if (isset($students) && $students->isNotEmpty())

                <div x-cloak x-data="{ showModal: false }"
                    x-init="$watch('showModal', val => document.body.classList.toggle('overflow-hidden', val))" class="relative z-10">

                    <button @click="showModal = true"
                        class="p-2 bg-themeBlue text-white h-full rounded-xl hover:bg-themeHoverBlue transition cursor-pointer">
                        Restablecer contrase as
                    </button>


                    <x-modal>
                        <x-heading level="h2" class="mb-8 text-center pb-4 border-b-2 border-b-themeBlue">Restablecer contrase as</x-heading>
                            <form action="{{ route('professor.reset.passwords') }}" method="POST" class="space-y-4 [&>select]:h-full
                                [&>select]:bg-white
                                [&>select]:rounded-lg
                                [&>select]:border-2
                                [&>select]:border-themeLightGray
                                [&>select]:px-4
                                [&>select]:text-themeLightGray
                                [&>select]:placeholder:text
                                dark:[&>select]:bg-themeBgDark">
                                @csrf
                                <label for="student_id">Selecciona alumno:</label>
                                <select name="student_id" id="student_id" required class="border rounded p-2">
                                    <option value="all">Todos los alumnos del centro</option>
                                    @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} {{ $student->last_name }} ({{ $student->email }})</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                                    Restablecer contrase a(s)
                                </button>
                            </form>
                    </x-modal>

                </div>
                @endif
                

            @endif

        </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.banner-preview').forEach((bannerImg, index) => {
                const bannerInput = bannerImg.parentElement.querySelector('.banner-input');
                bannerImg.addEventListener('click', () => bannerInput.click());
                bannerInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            bannerImg.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            });

            document.querySelectorAll('.profile-preview').forEach((profileImg, index) => {
                const profileInput = profileImg.parentElement.querySelector('.profile-input');
                profileImg.addEventListener('click', () => profileInput.click());
                profileInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            profileImg.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            });
        });
        function importStudentsModal() {
        return {
            confirmUpload: false,
            fileName: '',
            handleFileChosen(event) {
                const file = event.target.files[0];
                if (file) {
                    this.fileName = file.name;
                    this.confirmUpload = true;
                }
            },
            submitForm() {
                this.$refs.uploadForm.submit();
            }
        }
    }   

    document.addEventListener('DOMContentLoaded', function() {
    // Manejo de previsualización de imágenes
    const bannerPreview = document.querySelector('.banner-preview');
    const bannerInput = document.querySelector('.banner-input');
    const profilePreview = document.querySelector('.profile-preview');
    const profileInput = document.querySelector('.profile-input');

    // Click en banner para seleccionar archivo
    bannerPreview.addEventListener('click', function() {
        bannerInput.click();
    });

    // Click en perfil para seleccionar archivo
    profilePreview.addEventListener('click', function() {
        profileInput.click();
    });

    // Previsualización de banner
    bannerInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                bannerPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Previsualización de perfil
    profileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Validación del formulario
    const form = document.querySelector('form[action*="admin.user.update"]');
    
    form.addEventListener('submit', function(event) {
        const errors = {};
        
        // Obtener valores del formulario
        const name = form.querySelector('input[name="name"]').value.trim();
        const lastName = form.querySelector('input[name="last_name"]').value.trim();
        const email = form.querySelector('input[name="email"]').value.trim();
        const description = form.querySelector('textarea[name="description"]').value.trim();
        const role = form.querySelector('select[name="role"]').value;
        
        // Archivos
        const profileFile = form.querySelector('input[name="profile"]').files[0];
        const bannerFile = form.querySelector('input[name="banner"]').files[0];
        const cvFile = form.querySelector('input[name="cv"]').files[0];

        // Validaciones generales
        if (!name) {
            errors.name = "El nombre es obligatorio.";
        } else if (name.length > 20) {
            errors.name = "El nombre no puede tener más de 20 caracteres.";
        }

        if (!lastName) {
            errors.last_name = "El apellido es obligatorio.";
        } else if (lastName.length > 40) {
            errors.last_name = "El apellido no puede tener más de 40 caracteres.";
        }

        if (!email) {
            errors.email = "El correo electrónico es obligatorio.";
        } else if (!/^[\w-.]+@[\w-]+\.[a-z]{2,}$/i.test(email)) {
            errors.email = "El formato del correo no es válido.";
        } else if (email.length > 50) {
            errors.email = "El correo no puede tener más de 50 caracteres.";
        }

        if (description && description.length > 300) {
            errors.description = "La descripción no puede tener más de 300 caracteres.";
        }

        if (!role) {
            errors.role = "El rol es obligatorio.";
        } else if (!['Usuario', 'Alumno', 'Profesor', 'Empresa'].includes(role)) {
            errors.role = "El rol seleccionado no es válido.";
        }

        // Validación de archivos
        if (profileFile) {
            const allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedImageTypes.includes(profileFile.type)) {
                errors.profile = "La imagen de perfil debe ser JPG, JPEG o PNG.";
            } else if (profileFile.size > 2048 * 1024) { // 2MB
                errors.profile = "La imagen de perfil no puede ser mayor a 2MB.";
            }
        }

        if (bannerFile) {
            const allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedImageTypes.includes(bannerFile.type)) {
                errors.banner = "La imagen de banner debe ser JPG, JPEG o PNG.";
            } else if (bannerFile.size > 4096 * 1024) { // 4MB
                errors.banner = "La imagen de banner no puede ser mayor a 4MB.";
            }
        }

        if (cvFile) {
            if (cvFile.type !== 'application/pdf') {
                errors.cv = "El CV debe ser un archivo PDF.";
            } else if (cvFile.size > 2048 * 1024) { // 2MB
                errors.cv = "El CV no puede ser mayor a 2MB.";
            }
        }

        // Validaciones específicas por rol
        if (role === 'Alumno') {
            const birthDate = form.querySelector('input[name="birthDate"]')?.value;
            const currentCourse = form.querySelector('input[name="currentCourse"]')?.value?.trim();
            const educationalCenter = form.querySelector('input[name="educationalCenter"]')?.value?.trim();

            if (!birthDate) {
                errors.birthDate = "La fecha de nacimiento es obligatoria para estudiantes.";
            } else if (new Date(birthDate) > new Date()) {
                errors.birthDate = "La fecha de nacimiento debe ser anterior o igual a hoy.";
            }

            if (!currentCourse) {
                errors.currentCourse = "El curso actual es obligatorio para estudiantes.";
            } else if (currentCourse.length > 50) {
                errors.currentCourse = "El curso actual no puede tener más de 50 caracteres.";
            }

            if (!educationalCenter) {
                errors.educationalCenter = "El centro educativo es obligatorio para estudiantes.";
            } else if (educationalCenter.length > 100) {
                errors.educationalCenter = "El centro educativo no puede tener más de 100 caracteres.";
            }
        }

        if (role === 'Profesor') {
            const educationalCenter = form.querySelector('input[name="educationalCenter"]')?.value?.trim();
            const specialization = form.querySelector('input[name="specialization"]')?.value?.trim();
            const department = form.querySelector('input[name="department"]')?.value?.trim();

            if (!educationalCenter) {
                errors.educationalCenter = "El centro educativo es obligatorio para profesores.";
            } else if (educationalCenter.length > 100) {
                errors.educationalCenter = "El centro educativo no puede tener más de 100 caracteres.";
            }

            if (!specialization) {
                errors.specialization = "La especialización es obligatoria para profesores.";
            } else if (specialization.length > 100) {
                errors.specialization = "La especialización no puede tener más de 100 caracteres.";
            }

            if (!department) {
                errors.department = "El departamento es obligatorio para profesores.";
            } else if (department.length > 100) {
                errors.department = "El departamento no puede tener más de 100 caracteres.";
            }
        }

        if (role === 'Empresa') {
            const cif = form.querySelector('input[name="cif"]')?.value?.trim();
            const address = form.querySelector('input[name="address"]')?.value?.trim();
            const sector = form.querySelector('input[name="sector"]')?.value?.trim();
            const website = form.querySelector('input[name="website"]')?.value?.trim();

            if (!cif) {
                errors.cif = "El CIF es obligatorio para empresas.";
            } else if (cif.length > 50) {
                errors.cif = "El CIF no puede tener más de 50 caracteres.";
            }

            if (!address) {
                errors.address = "La dirección es obligatoria para empresas.";
            } else if (address.length > 255) {
                errors.address = "La dirección no puede tener más de 255 caracteres.";
            }

            if (!sector) {
                errors.sector = "El sector es obligatorio para empresas.";
            } else if (sector.length > 100) {
                errors.sector = "El sector no puede tener más de 100 caracteres.";
            }

            if (website && (!/^https?:\/\/.+/i.test(website) || website.length > 255)) {
                errors.website = "La URL del sitio web no es válida o es demasiado larga.";
            }
        }

        // Mostrar errores si existen
        const errorContainer = document.getElementById('form-errors');
        const errorList = errorContainer.querySelector('ul');

        if (Object.keys(errors).length > 0) {
            event.preventDefault();

            // Limpiar errores anteriores
            errorList.innerHTML = '';

            // Agregar nuevos errores
            Object.values(errors).forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            });

            // Mostrar contenedor de errores
            errorContainer.classList.remove('hidden');

            // Scroll hacia arriba para mostrar errores
            errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            // Ocultar errores si no hay ninguno
            errorContainer.classList.add('hidden');
        }
    });
});

    </script>


@endsection