<header>
    <nav>
        <a href="">Inicio</a>
        <a href="">Proyectos</a>
        <a href="">Ofertas de empleo</a>

        @auth
                @php
                    $role = auth()->user()->role;
                @endphp

                @if(in_array($role, ['usuario', 'alumno']))
                    <a href="">Tus proyectos</a>
                @elseif($role === 'profesor')
                    <a href="">Gestión de proyectos escolares</a>
                @elseif($role === 'empresa')
                    <a href="">Gestión de candidaturas</a>
                    <a href="">Tus ofertas</a>
                @endif

                <span>Hola, {{ auth()->user()->name }}</span>
        @endauth
    </nav>

</header>