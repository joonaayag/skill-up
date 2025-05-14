<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Skill Up</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="flex flex-col min-h-screen bg-cover bg-center"
    style="background-image: url('{{ asset('images/welcome-bg.jpg') }}')">

    <main class="flex-grow">
        <section class="grid grid-cols-5 bg-white mt-20 mb-40 ml-32 w-4/7 rounded-xl">
            <div class="col-span-3 ml-16 mt-11">
                <x-icon name="skill-up-logo" class="w-72 h-auto -translate-x-8 mb-7" />

                <x-heading level="h1" class="mb-7">Descubre proyectos TFG y oportunidades de trabajo</x-heading>
                <p class="leading-5 mb-7">Explora una variedad de proyectos académicos y ofertas de trabajo adaptadas
                    para estudiantes y empresas. Conéctate, colabora y haz avanzar tu carrera con facilidad.</p>
                <x-button href="{{ route('auth') }}">Empezar ahora</x-button>

            </div>
            <div class="col-span-2 flex items-center justify-center">
                <img class="h-5/6 w-auto" src="{{ asset('images/phone-mockup.png') }}" alt="phone mockup">

            </div>
        </section>
    </main>

    <x-footer />
</body>

</html>