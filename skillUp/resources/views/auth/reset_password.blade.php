<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Skill Up - {{ __('messages.titles.reset-password') }}</title>
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
                    {{ __('messages.auth.res-password') }}
                </x-heading>

                @if ($errors->any())
                    <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
                        <ul class="text-black dark:text-white list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>-{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <x-label for="email">{{ __('messages.auth.email') }}</x-label>
                        <input type="email" name="email" id="email" value="{{ old('email', $email ?? '') }}" readonly
                            required
                            class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white shadow-sm focus:outline-none" />
                    </div>

                    <div>
                        <x-label for="password">{{ __('messages.auth.ph-password') }}</x-label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-themeGrape" />
                    </div>

                    <div>
                        <x-label for="password_confirmation">{{ __('messages.auth.ph-password-confirm') }}</x-label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-themeGrape" />
                    </div>

                    <x-button type="submit" class="w-max cursor-pointer">
                        {{ __('messages.auth.reset') }}
                    </x-button>
                </form>
            </div>

            <!-- Imagen lateral -->
            <div class="hidden lg:col-span-2 lg:flex items-center justify-center">
                <img class="max-h-[400px] w-auto rounded-lg" src="{{ asset('images/phone-mockup.png') }}"
                    alt="phone mockup">
            </div>
        </section>
    </main>

    <x-footer />
</body>

</html>