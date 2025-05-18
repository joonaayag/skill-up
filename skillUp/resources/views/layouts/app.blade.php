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

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-no-repeat bg-cover bg-center flex flex-col min-h-screen transition-all duration-500 text-themeBgDark dark:text-white"
:style="darkMode ? 'background-image: url(/images/app-dark-bg.jpg)' : 'background-image: url(/images/app-bg.jpg)'">
    <x-header/>

    <main class="mt-32 mx-24 flex-grow">
        @yield('content')
    </main>

    <x-footer/>
<script>
    let logoutTimer;

    function resetLogoutTimer() {
        clearTimeout(logoutTimer);
        logoutTimer = setTimeout(() => {
            window.location.href = "{{ route('user.logout') }}";
        }, 10 * 60 * 1000); 
    }

    ['mousemove', 'keydown', 'click', 'scroll'].forEach(evt => {
        document.addEventListener(evt, resetLogoutTimer);
    });

    resetLogoutTimer();


    let ultimaNotiId = parseInt(localStorage.getItem('ultimaNotiId')) || 0;

    setInterval(() => {
        fetch("/notificaciones/check")
            .then(res => res.json())
            .then(data => {
                data.forEach(noti => {
                    if (noti.id > ultimaNotiId) {
                        mostrarNotificacion(formatearTipo(noti.type), noti.message);

                        if (noti.id > ultimaNotiId) {
                            ultimaNotiId = noti.id;
                            localStorage.setItem('ultimaNotiId', ultimaNotiId);
                        }
                    }
                });
            })
            .catch(err => console.error("Error al cargar notificaciones:", err));
    }, 5000);

    function mostrarNotificacion(titulo, mensaje) {
    const noti = document.createElement('div');
    
    noti.classList.add(
        'fixed', 'top-24', 'bg-blue-600', 'text-white',
        'p-4', 'rounded-lg', 'shadow-lg', 'z-50', 'notification-element'
    );
    
    noti.style.right = '-100%';
    noti.style.transition = 'right 0.5s ease-in-out';
    
    noti.innerHTML = `<strong>${titulo}</strong><br>${mensaje}`;
    
    document.body.appendChild(noti);
    
    noti.offsetHeight;
    
    setTimeout(() => {
        noti.style.right = '1rem'; 
    }, 50);
    
    setTimeout(() => {
        noti.style.right = '-100%';
        
        setTimeout(() => {
            noti.remove();
        }, 500); 
    }, 4500); 
}

function formatearTipo(tipo) {
    switch (tipo) {
        case 'proyecto': return '📝 Proyecto';
        case 'oferta': return '💼 Oferta';
        default: return '📢 Notificación';
    }
}


</script>


</body>


</html>