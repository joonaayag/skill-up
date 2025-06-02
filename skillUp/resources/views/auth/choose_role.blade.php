<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Skill Up - {{ __('messages.titles.last-step') }}</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <link rel="icon" type="image/x-icon" href="{{ asset('icons/logo.svg') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body class="flex flex-col min-h-screen bg-cover bg-center text-gray-800 dark:text-white"
    style="background-image: url('{{ asset('images/welcome-bg.jpg') }}')">

    <main class="flex-grow px-12">
        <section
            class="w-full 2md:w-4/7 mx-auto mt-20 mb-40 px-4 sm:px-6 2md:px-10 py-10 bg-white dark:bg-themeBgDark rounded-2xl shadow-xl grid grid-cols-1 lg:grid-cols-5 gap-10 transition-all duration-300 ease-in-out">

            <div class="lg:col-span-3 flex flex-col justify-center md:ml-6">
                <x-heading level="h1" class="mb-6">
                    {{ __('messages.messages.last-step') }}
                </x-heading>

                @if ($errors->any())
                    <div class="text-xs md:text-sm 2md:text-base bg-red-100 border border-red-400 text-red-700 dark:bg-red-200 dark:text-red-900 px-4 py-3 rounded-xl mb-6 shadow-md">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/elegir-rol" class="space-y-6"
                    x-data="{ role: '{{ old('role') ?? '' }}' }">
                    @csrf

                    <div>
                        <x-label for="role">{{ __('messages.choose.role') }}</x-label>
                        <select name="role" id="role" x-model="role" required
                            class="w-full text-xs md:text-sm 2md:text-base px-2 py-1 2md:px-4 2md:py-2 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape dark:focus:ring-themeGrapeLight">
                            <option value="Usuario">{{ __('messages.choose.option-user') }}</option>
                            <option value="Alumno">{{ __('messages.choose.option-student') }}</option>
                            <option value="Empresa">{{ __('messages.choose.option-company') }}</option>
                        </select>
                    </div>

                    <template x-if="role === 'Alumno'">
                        <div class="space-y-4">
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" required
                                class="w-full text-xs md:text-sm 2md:text-base px-2 py-1 2md:px-4 2md:py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />

                            <input type="text" name="current_course" value="{{ old('current_course') }}" required
                                placeholder="{{ __('messages.admin.users.ph-current-course') }}"
                                class="w-full text-xs md:text-sm 2md:text-base px-2 py-1 2md:px-4 2md:py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />

                            <input type="text" name="educational_center" value="{{ old('educational_center') }}" required
                                placeholder="{{ __('messages.admin.users.ph-educational-center') }}"
                                class="w-full text-xs md:text-sm 2md:text-base px-2 py-1 2md:px-4 2md:py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />
                        </div>
                    </template>

                    <template x-if="role === 'Empresa'">
                        <div class="space-y-4">
                            <input type="text" name="cif" value="{{ old('cif') }}" required
                                placeholder="{{ __('messages.profile.cif') }}"
                                class="w-full text-xs md:text-sm 2md:text-base px-2 py-1 2md:px-4 2md:py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />

                            <input type="text" name="address" value="{{ old('address') }}" required
                                placeholder="{{ __('messages.profile.address') }}"
                                class="w-full text-xs md:text-sm 2md:text-base px-2 py-1 2md:px-4 2md:py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />

                            <input type="text" name="sector" value="{{ old('sector') }}" required
                                placeholder="{{ __('messages.profile.sector') }}"
                                class="w-full text-xs md:text-sm 2md:text-base px-2 py-1 2md:px-4 2md:py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />

                            <input type="url" name="website" value="{{ old('website') }}"
                                placeholder="{{ __('messages.profile.website') }}"
                                class="w-full text-xs md:text-sm 2md:text-base px-2 py-1 2md:px-4 2md:py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeGrape shadow-sm transition" />
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
