<header
    class="fixed top-0 left-1/2 -translate-x-1/2 w-[90%] max-w-7xl flex items-center justify-between bg-red
  dark:bg-themeBgDark bg-white text-black dark:text-[#e8e8e8] px-8 py-2 rounded-bl-4xl rounded-br-4xl shadow z-50"
    id="main-header">

    <div class="flex flex-grow basis-0">
        <x-icon name="skill-up-logo" class="w-48 h-auto -translate-x-5" />

    </div>

    <nav class="flex [&>a]:inline-block [&>a]:px-3 [&>a]:py-2" id="main-navigation">
        <a href="{{ route('dashboard') }}">Inicio</a>
        <a href="{{ route('projects.index') }}">Proyectos</a>
        <a href="{{ route('job.offers.index') }}">Ofertas de empleo</a>
        <a href="{{ route('favorites.index') }}">Favoritos</a>

        @auth
            @php
                $role = auth()->user()->role;
            @endphp

            @if(in_array($role, ['usuario', 'alumno']))
                <a href="{{ route('projects.ownProjects') }}">Tus proyectos</a>
            @endif
            @if($role === 'profesor')
                <a href="{{ route('school.projects.index') }}">Proyectos escolares</a>
            @endif
            @if(in_array($role, ['profesor', 'empresa']))
                <a href="{{ route('applications.index') }}">Candidaturas</a>
                <a href="{{ route('job.offers.company.index') }}">Tus ofertas</a>
            @endif
            @if($role === 'admin')
                <a href="{{ route('admin.dashboard') }}">Panel de administrador</a>
            @endif
        @endauth

        <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
            class="relative inline-block">
            <button class="relative px-3 py-2">
                🔔
                @if(auth()->user()->notifications->count() > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">
                        {{ auth()->user()->notifications->count() }}
                    </span>
                @endif
            </button>

            <div x-show="open" x-transition
                class="absolute left-0 mt-2 w-72 bg-white border border-gray-300 shadow-xl rounded-md z-50">
                <ul class="max-h-64 overflow-y-auto divide-y">
                    @forelse(auth()->user()->notifications as $notification)
                        <li class="flex justify-between items-start p-3 hover:bg-gray-100">
                            <span class="text-sm text-gray-800">{{ $notification->title ?? 'Notificación' }}</span>
                            <span class="text-sm text-gray-800">{{ $notification->message ?? 'Mensaje' }}</span>
                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 text-sm">❌</button>
                            </form>
                        </li>
                    @empty
                        <li class="p-3 text-sm text-gray-500">Sin notificaciones</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <button id="theme-toggle" @click="darkMode = !darkMode" class="rounded-full cursor-pointer">
        <x-icon name="dark-light" />
        </button>

    </nav>

    <nav class="flex flex-grow justify-end basis-0 [&>a]:inline-block">
        @auth
            <a href="{{ route('profile.index') }}">
                <span>Hola, {{ auth()->user()->name }}</span>
            </a>
        @endauth
    </nav>

    <div id="menu-backdrop"
        class="absolute bg-black/5 dark:bg-white/9 border-b-2 border-b-themeBlue backdrop-blur-lg rounded translate-x-[var(--left)] translate-y-[var(--top)] left-0 top-0 w-[var(--width)]
         h-[var(--height)] transition-all duration-200 ease-in-out opacity-0 -z-10">
    </div>

</header>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const listItem = document.querySelectorAll('#main-navigation a')
        const menuBackdrop = document.getElementById('menu-backdrop')
        const header = document.getElementById('main-header')

        listItem.forEach((item) => {
            item.addEventListener('mouseenter', (e) => {
                const headerRect = header.getBoundingClientRect()
                const { left, top, width, height } = e.target.getBoundingClientRect()

                const relativeLeft = left - headerRect.left
                const relativeTop = top - headerRect.top

                menuBackdrop.style.setProperty('--left', `${relativeLeft}px`)
                menuBackdrop.style.setProperty('--top', `${relativeTop}px`)
                menuBackdrop.style.setProperty('--width', `${width}px`)
                menuBackdrop.style.setProperty('--height', `${height}px`)

                menuBackdrop.style.opacity = '1'
                menuBackdrop.style.visibility = 'visible'
            })

            item.addEventListener('mouseleave', () => {
                menuBackdrop.style.opacity = '0'
                menuBackdrop.style.visibility = 'hidden'
            })
        })

        window.addEventListener('resize', function () {
            menuBackdrop.style.opacity = '0'
            menuBackdrop.style.visibility = 'hidden'
        })
    })
</script>