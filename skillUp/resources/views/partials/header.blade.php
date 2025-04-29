<header>
    <nav>
        <a href="{{ url('/') }}">Inicio</a> |
        <a href="{{ url('/dashboard') }}">Dashboard</a> |
        @auth
            <span>Hola, {{ auth()->user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        @endauth
    </nav>
</header>
