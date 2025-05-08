<!DOCTYPE html>
<html lang="es" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-no-repeat bg-cover bg-center flex flex-col min-h-screen transition-all duration-500 text-themeBgDark dark:text-themeLightGray">
    <style>
        body {
            background-image: url({{ asset('images/app-bg.jpg') }});
        }
    </style>
    <x-header/>

    <main class="mt-32 mx-24 flex-grow">
        @yield('content')
    </main>

    <x-footer/>
</body>

<script>
function initDarkMode() {
  // Verificar si hay una preferencia guardada
  const savedTheme = localStorage.getItem('theme');
  
  if (savedTheme === 'dark') {
    // Aplicar modo oscuro si estaba guardado
    document.documentElement.classList.add('dark');
  } else if (savedTheme === 'light') {
    // Aplicar modo claro si estaba guardado
    document.documentElement.classList.remove('dark');
  } else {
    // Si no hay preferencia guardada, usar la preferencia del sistema
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      document.documentElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      document.documentElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
  }
}

// Función para alternar el modo oscuro
function toggleDarkMode() {
  if (document.documentElement.classList.contains('dark')) {
    // Cambiar a modo claro
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme', 'light');
  } else {
    // Cambiar a modo oscuro
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  }
}

// Inicializar el modo oscuro al cargar la página
document.addEventListener('DOMContentLoaded', initDarkMode);

// Ejemplo: conectar a un botón para cambiar el tema
document.getElementById('theme-toggle').addEventListener('click', toggleDarkMode);
</script>

</html>