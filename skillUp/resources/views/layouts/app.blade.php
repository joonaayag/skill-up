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

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

const toast = document.getElementById('toast');
        if (toast) {
            setTimeout(() => {
                toast.classList.add('animate-slide-out');
            }, 3000); 
            setTimeout(() => {
                toast.remove();
            }, 4000);
        }


</script>


</body>


</html>