<header>
    <nav>
        <a href="{{ route('dashboard') }}">Inicio</a>
        <a href="{{ route('projects.index') }}">Proyectos</a>
        <a href="{{ route('job.offers.index') }}">Ofertas de empleo</a>

        @auth
                @php
                    $role = auth()->user()->role;
                @endphp

                @if(in_array($role, ['usuario', 'alumno']))
                    <a href="">Tus proyectos</a>
                @elseif($role === 'profesor')
                    <a href="{{ route('school.projects.index') }}">Gestión de proyectos escolares</a>
                @elseif($role === 'empresa')
                    <a href="">Gestión de candidaturas</a>
                    <a href="{{ route('job.offers.company.index') }}">Tus ofertas</a>
                @endif

                <span>Hola, {{ auth()->user()->name }}</span>
        @endauth
    </nav>

</header>