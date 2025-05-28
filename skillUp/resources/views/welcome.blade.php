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

            <div class="col-span-5 lg:col-span-3 flex flex-col justify-center md:ml-6">
                @if ($errors->any())
                    <div class="text-xs md:tex-sm 2md:text-base bg-red-100 border border-red-400 text-red-700 dark:bg-red-200 dark:text-red-900 px-4 py-3 rounded-xl mb-6 shadow-md">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <x-icon name="skill-up-logo" class="w-40 sm:w-56 md:w-72 h-auto -translate-x-4 mb-6" />

                <x-heading level="h1" class="mb-6">
                    {{ __('messages.welcome.title') }}
                </x-heading>

                <p class="text-xs md:text-sm 2md:text-base text-gray-600 dark:text-themeLightGray mb-6 leading-relaxed">
                    {{ __('messages.welcome.subtitle') }}
                </p>

                <x-button href="{{ route('auth') }}" class="w-max ">
                    {{ __('messages.welcome.button') }}
                </x-button>
            </div>

            <div class="hidden lg:col-span-2 lg:flex items-center justify-center">
                <img class="max-h-[400px] w-auto rounded-lg " src="{{ asset('images/phone-mockup.png') }}"
                    alt="phone mockup">
            </div>
        </section>
    </main>

    <x-footer />
</body>




</html>