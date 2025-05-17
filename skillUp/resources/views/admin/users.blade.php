@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-10">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">{{ __('messages.admin.users.title') }}</h1>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
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
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.users.table-last-name') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.users.table-email') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.users.table-role') }}</th>
                        <th class="px-4 py-3 border dark:border-gray-700">{{ __('messages.admin.users.table-actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->id }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->name }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->last_name }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ $user->email }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700">{{ ucfirst($user->role) }}</td>
                            <td class="px-4 py-3 border dark:border-gray-700 space-x-2 whitespace-nowrap">

                                <div x-data="{ openEdit: false, role: '{{ $user->role }}' }" class="inline-block" x-cloak>
                                    <button @click="openEdit = true"
                                        class="bg-themeBlue border-2 border-themeBlue hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition cursor-pointer">{{ __('messages.button.edit') }}</button>

                                    <x-modal :show="'openEdit'" @click.outside="openEdit = false">
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.users.edit') }}</x-heading>
                                        <form action="{{ route('admin.user.update', $user->id) }}" method="POST"
                                            enctype="multipart/form-data"
                                            class="max-w-2xl mx-auto dark:bg-themeBgDark bg-white p-6 rounded shadow ">
                                            @csrf
                                            @method('PUT')

                                            <div class="relative mb-8">
                                                <img id="bannerPreview"
                                                    src="{{ auth()->user()->banner ? asset('storage/' . auth()->user()->banner) : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e' }}"
                                                    class="w-full h-40 object-cover cursor-pointer" alt="Banner">
                                                <input type="file" name="banner" id="bannerInput" accept="image/*"
                                                    class="hidden">

                                                <div class="absolute -bottom-10 left-1/6 transform -translate-x-1/2">
                                                    <img id="fotoPerfilPreview"
                                                        src="{{ auth()->user()->profile ? asset('storage/' . auth()->user()->profile) : 'https://randomuser.me/api/portraits/men/32.jpg' }}"
                                                        class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg cursor-pointer"
                                                        alt="Foto de perfil">
                                                    <input type="file" name="profile" id="fotoPerfilInput" accept="image/*"
                                                        class="hidden">
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

                                            <div class="mt-4">
                                                <label
                                                    class="block text-sm font-medium">{{ __('messages.profile.label-email') }}</label>
                                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                                    class="w-full border rounded px-3 py-2" required>
                                            </div>

                                            <div class="mt-4">
                                                <label
                                                    class="block text-sm font-medium">{{ __('messages.profile.label-description') }}</label>
                                                <textarea name="description" class="w-full border rounded px-3 py-2"
                                                    rows="4">{{ old('description', $user->description) }}</textarea>
                                            </div>

                                            @if ($user->role === 'Alumno')
                                                <div class="mt-4">
                                                    <label
                                                        class="block text-sm font-medium">{{ __('messages.profile.label-birth-date') }}</label>
                                                    <input type="date" name="birth_date"
                                                        value="{{ old('birth_date', $user->detail->birth_date ?? '') }}"
                                                        class="w-full border rounded px-3 py-2">
                                                </div>
                                                <div class="mt-4">
                                                    <label
                                                        class="block text-sm font-medium">{{ __('messages.profile.label-current-course') }}</label>
                                                    <input type="text" name="current_course"
                                                        value="{{ old('current_course', $user->detail->current_course ?? '') }}"
                                                        class="w-full border rounded px-3 py-2">
                                                </div>
                                                <div class="mt-4">
                                                    <label
                                                        class="block text-sm font-medium">{{ __('messages.profile.label-educational-center') }}</label>
                                                    <input type="text" name="educational_center"
                                                        value="{{ old('educational_center', $user->detail->educational_center ?? '') }}"
                                                        class="w-full border rounded px-3 py-2">
                                                </div>
                                            @endif

                                            @if ($user->role === 'Profesor')
                                                <div class="mt-4">
                                                    <label
                                                        class="block text-sm font-medium">{{ __('messages.profile.label-specialization') }}</label>
                                                    <input type="text" name="specialization"
                                                        value="{{ old('specialization', $user->detail->specialization ?? '') }}"
                                                        class="w-full border rounded px-3 py-2">
                                                </div>
                                                <div class="mt-4">
                                                    <label
                                                        class="block text-sm font-medium">{{ __('messages.profile.label-department') }}</label>
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
                                                    <label
                                                        class="block text-sm font-medium">{{ __('messages.profile.label-address') }}</label>
                                                    <input type="text" name="address"
                                                        value="{{ old('address', $user->detail->address ?? '') }}"
                                                        class="w-full border rounded px-3 py-2">
                                                </div>
                                                <div class="mt-4">
                                                    <label
                                                        class="block text-sm font-medium">{{ __('messages.profile.label-sector') }}</label>
                                                    <input type="text" name="sector"
                                                        value="{{ old('sector', $user->detail->sector ?? '') }}"
                                                        class="w-full border rounded px-3 py-2">
                                                </div>
                                                <div class="mt-4">
                                                    <label
                                                        class="block text-sm font-medium">{{ __('messages.profile.label-website') }}</label>
                                                    <input type="url" name="website"
                                                        value="{{ old('website', $user->detail->website ?? '') }}"
                                                        class="w-full border rounded px-3 py-2">
                                                </div>
                                            @endif

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
                                        <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.users.heading-confirm') }}</x-heading>
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

        <div x-data="{ showCreateUser: false }" x-cloak class="inline-block mt-5">
            <button @click="showCreateUser = true"
                class="flex  gap-2 items-center bg-themeBlue/80 border-2 border-themeBlbg-themeBlue/80 hover:bg-themeBlue text-white font-semibold py-2 px-4 rounded-lg transition cursor-pointer"><x-icon
                    name="plus" class="w-5 h-auto" /> {{ __('messages.button.create') }}</button>

            <x-modal :show="'showCreateUser'" @close="showCreateUser = false">
                <x-heading level="h2" class="mb-4 text-center pb-4 border-b-2 border-b-themeBlue">{{ __('messages.admin.users.create-user') }}</x-heading>
                <form method="POST" action="{{ route('admin.register') }}" class="space-y-4"
                    x-data="{ role: '{{ old('role') }}' }">
                    @csrf

                    <input type="text" name="name" placeholder="{{ __('messages.admin.users.ph-name') }}" required>
                    <input type="text" name="lastName" placeholder="{{ __('messages.admin.users.ph-last-name') }}" required>
                    <input type="email" name="email" placeholder="{{ __('messages.admin.users.ph-email') }}" required>
                    <input type="password" name="password" placeholder="{{ __('messages.admin.users.ph-password') }}" required>
                    <input type="password" name="password_confirmation" placeholder="{{ __('messages.admin.users.ph-password-confirmation') }}" required>

                    <select name="role" x-model="role" required>
                        <option value="">{{ __('messages.admin.users.select-role') }}</option>
                        <option value="Usuario">{{ __('messages.admin.users.user') }}</option>
                        <option value="Alumno">{{ __('messages.admin.users.student') }}</option>
                        <option value="Profesor">{{ __('messages.admin.users.teacher') }}</option>
                        <option value="Empresa">{{ __('messages.admin.users.company') }}</option>
                    </select>

                    <!-- Campos adicionales según rol -->
                    <template x-if="role === 'Alumno'">
                        <div class="space-y-2">
                            <input type="date" name="birthDate" placeholder="{{ __('messages.admin.users.ph-birth-date') }}" required>
                            <input type="text" name="currentCourse" placeholder="{{ __('messages.admin.users.ph-current-course') }}" required>
                            <input type="text" name="educationalCenter" placeholder="{{ __('messages.admin.users.ph-educational-center') }}" required>
                        </div>
                    </template>

                    <template x-if="role === 'Profesor'">
                        <div class="space-y-2">
                            <input type="date" name="birthDate" placeholder="{{ __('messages.admin.users.ph-birth-date') }}" required>
                            <input type="text" name="specialization" placeholder="{{ __('messages.admin.users.ph-specialization') }}" required>
                            <input type="text" name="department" placeholder="{{ __('messages.admin.users.ph-department') }}" required>
                            <input type="text" name="validationDocument" placeholder="{{ __('messages.admin.users.ph-validation-document') }}" required>
                        </div>
                    </template>

                    <template x-if="role === 'Empresa'">
                        <div class="space-y-2">
                            <input type="text" name="cif" placeholder="{{ __('messages.admin.users.ph-cif') }}" required>
                            <input type="text" name="address" placeholder="{{ __('messages.admin.users.ph-address') }}" required>
                            <input type="text" name="sector" placeholder="{{ __('messages.admin.users.ph-sector') }}" required>
                            <input type="url" name="website" placeholder="{{ __('messages.admin.users.ph-website') }}">
                        </div>
                    </template>

                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
                        {{ __('messages.button.register') }}
                    </button>

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