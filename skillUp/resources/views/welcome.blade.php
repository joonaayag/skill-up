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

    <main class="flex-grow">
        <section class="grid grid-cols-1 md:grid-cols-5 bg-white mt-20 mb-40 mx-32 w-4/7 rounded-xl">
            <div class="md:col-span-3 ml-16 mt-11">
                @if ($errors->any())
                    <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class="text-black dark:text-white">- {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <x-icon name="skill-up-logo" class="w-72 h-auto -translate-x-8 mb-7" />

                <x-heading level="h1" class="mb-7">{{ __('messages.welcome.title') }}</x-heading>
                <p class="leading-5 mb-7">{{ __('messages.welcome.subtitle') }}</p>
                <x-button href="{{ route('auth') }}">{{ __('messages.welcome.button') }}</x-button>

            </div>
            <div class="md:col-span-2 flex items-center justify-center">
                <img class="h-5/6 w-auto" src="{{ asset('images/phone-mockup.png') }}" alt="phone mockup">

            </div>
        </section>
    </main>

    <x-footer />

</body>

</html>