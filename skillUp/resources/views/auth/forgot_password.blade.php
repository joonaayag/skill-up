<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Skill Up</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <link rel="icon" type="image/x-icon" href="{{ asset('icons/logo.svg') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body class="flex flex-col min-h-screen bg-cover bg-center"
    style="background-image: url('{{ asset('images/welcome-bg.jpg') }}')">

    <main class="flex-grow px-12">
        <section
            class="w-full 2md:w-4/7 mx-auto mt-20 mb-40 px-4 sm:px-6 2md:px-10 py-10 bg-white dark:bg-themeBgDark rounded-2xl shadow-xl grid grid-cols-1 lg:grid-cols-5 gap-10 transition-all duration-300 ease-in-out">

            <!-- Lado izquierdo: texto y formulario -->
            <div class="lg:col-span-3 flex flex-col justify-center md:ml-6">
                <x-icon name="skill-up-logo" class="w-30 sm:w-40 md:w-60 h-auto -translate-x-5 md:-translate-x-9 mb-6" />
                <x-heading level="h1" class="mb-6">
                    {{ __('messages.auth.forgot-password') }}
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

                @if (session('status'))
                    <div class="text-green-600 dark:text-green-400 mb-4 font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block mb-1 tex-sm 2md:text-base font-semibold text-gray-700 dark:text-white">
                            {{ __('messages.auth.email') }}
                        </label>
                        <input type="email" name="email" id="email" required
                            class="w-full h-8 sm:h-10 px-2 py-1 2md:px-4 2md:py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-themeGrape dark:focus:ring-themeGrapeLight">
                    </div>

                    <div class="flex flex-col gap-4">
                        <x-button type="submit" class="w-max h-8 sm:h-10 px-2 py-1 2md:px-4 2md:py-2 cursor-pointer">
                            {{ __('messages.auth.send-link') }}
                        </x-button>
                        <a href="{{ route('auth') }}" class="text-blue-500 hover:underline text-xs md:text-sm 2md:text-base cursor-pointer">Iniciar
                            sesión</a>
                    </div>
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