<header>
    <nav>
        <a href="{{ url('/') }}">Inicio</a> |
        <a href="{{ url('/dashboard') }}">Dashboard</a> |
        @auth
            <span>Hola, {{ auth()->user()->name }}</span>

        @endauth
    </nav>
</header>
