<header>
    <nav>
        <a href="{{ route('dashboard') }}">Inicio</a>
        <a href="{{ route('projects.index') }}">Proyectos</a>
        <a href="{{ route('job.offers.index') }}">Ofertas de empleo</a>
        <a href="{{ route('favorites.index') }}">Favoritos</a>

        @auth
            @php
                $role = auth()->user()->role;
            @endphp

            @if(in_array($role, ['usuario', 'alumno']))
                <a href="">Tus proyectos</a>
            @elseif($role === 'profesor')
                <a href="{{ route('school.projects.index') }}">Gestión de proyectos escolares</a>
            @elseif($role === 'empresa')
                <a href="{{ route('applications.index') }}">Gestión de candidaturas</a>
                <a href="{{ route('job.offers.company.index') }}">Tus ofertas</a>
            @endif

            <a href="{{ route('profile.index') }}">
                <span>Hola, {{ auth()->user()->name }}</span>
            </a>
        @endauth
    </nav>

</header>