<header>
    <nav>
        <a href="{{ route('dashboard') }}">Inicio</a>
        <a href="{{ route('projects.index') }}">Proyectos</a>
        <a href="{{ route('job.offers.index') }}">Ofertas de empleo</a>
        <a href="{{ route('favorites.index') }}">Favoritos</a>

        <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false"
            class="relative inline-block">
            <!-- Botón de notificaciones -->
            <button class="relative px-3 py-2 bg-white text-gray-800">
                🔔 Notificaciones
                @if(auth()->user()->notifications->count() > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">
                        {{ auth()->user()->notifications->count() }}
                    </span>
                @endif
            </button>

            <!-- Modal: justo debajo del botón, alineada con él -->
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


        @auth
            @php
                $role = auth()->user()->role;
            @endphp

            @if(in_array($role, ['usuario', 'alumno']))
                <a href="{{ route('projects.ownProjects') }}">Tus proyectos</a>
            @endif
            @if($role === 'profesor')
                <a href="{{ route('school.projects.index') }}">Gestión de proyectos escolares</a>
            @endif
            @if(in_array($role, ['profesor', 'empresa']))
                <a href="{{ route('applications.index') }}">Gestión de candidaturas</a>
                <a href="{{ route('job.offers.company.index') }}">Tus ofertas</a>
            @endif
            @if($role === 'admin')
                <a href="{{ route('admin.dashboard') }}">Panel de administrador</a>
            @endif

            <a href="{{ route('profile.index') }}">
                <span>Hola, {{ auth()->user()->name }}</span>
            </a>
        @endauth
    </nav>

</header>