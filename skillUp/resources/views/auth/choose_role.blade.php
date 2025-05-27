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
                <form method="POST" action="/elegir-rol">
                    @csrf
                    @if ($errors->any())
                        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="text-black dark:text-white">- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <label for="role">{{ __('messages.choose.role')  }}</label>
                    <select name="role" id="role" required>
                        <option value="Usuario">{{ __('messages.choose.option-user') }}</option>
                        <option value="Alumno">{{ __('messages.choose.option-student') }}</option>
                        <option value="Empresa">{{ __('messages.choose.option-company') }}</option>
                    </select>
                    <button type="submit">{{ __('messages.choose.continue') }}</button>
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