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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-no-repeat bg-cover bg-center flex flex-col min-h-screen transition-all duration-500 text-themeBgDark dark:text-white"
:style="darkMode ? 'background-image: url(/images/app-dark-bg.png)' : 'background-image: url(/images/app-bg.jpg)'">
    <x-header/>

    <main class="mt-32 mx-24 flex-grow">
        @yield('content')
    </main>

    <x-footer/>
</body>


</html>