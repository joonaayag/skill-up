<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Skill Up</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <link rel="icon" type="image/x-icon" href="{{ asset('icons/logo.svg') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="flex flex-col min-h-screen bg-cover bg-center text-gray-800 dark:text-white"
    style="background-image: url('{{ asset('images/welcome-bg.jpg') }}')">

    <main class="flex-grow px-12">
        <section
            class="w-full 2md:w-4/7 mx-auto mt-20 mb-40 px-4 sm:px-6 2md:px-10 py-10 bg-white dark:bg-themeBgDark rounded-2xl shadow-xl grid grid-cols-1 lg:grid-cols-5 gap-10 transition-all duration-300 ease-in-out">

            <div class="lg:col-span-3 flex flex-col justify-center md:ml-6">
                <x-heading level="h1" class="mb-6">
                    Un último paso para empezar con Skill Up
                </x-heading>

                @if ($errors->any())
                    <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class="text-black dark:text-white">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/elegir-rol" class="space-y-6"
                    x-data="{ role: '{{ old('role') ?? '' }}' }">
                    @csrf

                    <div>
                        <label for="role" class="block mb-1 font-semibold">
                            {{ __('messages.choose.role') }}
                        </label>
                        <select name="role" id="role" x-model="role" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape dark:focus:ring-themeGrapeLight">
                            <option value="Usuario">{{ __('messages.choose.option-user') }}</option>
                            <option value="Alumno">{{ __('messages.choose.option-student') }}</option>
                            <option value="Empresa">{{ __('messages.choose.option-company') }}</option>
                        </select>
                    </div>

                    <template x-if="role === 'Alumno'">
                        <div class="space-y-4">
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />

                            <input type="text" name="current_course" value="{{ old('current_course') }}" required
                                placeholder="{{ __('messages.admin.users.ph-current-course') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />

                            <input type="text" name="educational_center" value="{{ old('educational_center') }}" required
                                placeholder="{{ __('messages.admin.users.ph-educational-center') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />
                        </div>
                    </template>

                    <template x-if="role === 'Empresa'">
                        <div class="space-y-4">
                            <input type="text" name="cif" value="{{ old('cif') }}" required
                                placeholder="{{ __('messages.profile.cif') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />

                            <input type="text" name="address" value="{{ old('address') }}" required
                                placeholder="{{ __('messages.profile.address') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />

                            <input type="text" name="sector" value="{{ old('sector') }}" required
                                placeholder="{{ __('messages.profile.sector') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />

                            <input type="url" name="website" value="{{ old('website') }}"
                                placeholder="{{ __('messages.profile.website') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />
                        </div>
                    </template>

                    <x-button type="submit" class="w-max">
                        {{ __('messages.choose.continue') }}
                    </x-button>
                </form>
            </div>

            <div class="hidden lg:col-span-2 lg:flex items-center justify-center">
                <img class="max-h-[400px] w-auto rounded-lg" src="{{ asset('images/phone-mockup.png') }}"
                    alt="phone mockup">
            </div>
        </section>
    </main>

    <x-footer />
</body>

</html>
