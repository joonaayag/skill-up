<!DOCTYPE html>
<html lang="es" x-data="{ 
        darkMode: localStorage.getItem('theme') === 'dark' || 
                 (localStorage.getItem('theme') === null && 
                  window.matchMedia('(prefers-color-scheme: dark)').matches) 
    }" 
    :class="{ 'dark': darkMode }" 
    x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))">  

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <link rel="icon" type="image/x-icon" href="{{ asset('icons/logo.svg') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

</head>

<body class="bg-no-repeat bg-cover bg-center flex flex-col min-h-screen transition-all duration-500 text-themeBgDark dark:text-white"
:style="darkMode ? 'background-image: url(/images/app-dark-bg.jpg)' : 'background-image: url(/images/app-bg.jpg)'">
    <x-header/>

    @if (session('message'))
    <div 
        id="toast"
        class="fixed top-20 right-5 max-w-xs w-full z-50 px-4 py-3 text-sm text-white bg-black rounded-lg shadow-lg animate-slide-in"
    >
        {{ session('message') }}
    </div>

    <style>
        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slide-out {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        .animate-slide-in {
            animation: slide-in 0.5s ease-out forwards;
        }

        .animate-slide-out {
            animation: slide-out 0.5s ease-in forwards;
        }
    </style>
    @endif


    <main class="mt-32 mx-8 sm:mx-24 flex-grow">
        @yield('content')
    </main>

    <x-footer/>


</body>


</html>