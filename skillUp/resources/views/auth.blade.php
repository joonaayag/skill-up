<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Autenticación</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('icons/logo.svg') }}">
    <!-- @vite('resources/css/app.css')
    @vite('resources/js/app.js') -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    @if (session('status'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    <h2>Iniciar Sesión</h2>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <label>Email:</label>
        <input type="email" name="email" value="{{ old('email') }}" required><br>
        <label>Contraseña:</label>
        <input type="password" name="password" required><br>
        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
        <span>Recordarme</span>
        <button type="submit">Entrar</button>
    </form>
    <p class="text-sm text-center mt-4">
        <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">
            ¿Has olvidado tu contraseña?
        </a>
    </p>

    <a href="/auth/google/redirect">
        <button>Continuar con Google</button>
    </a>

    <hr>

    <h2>Registrarse</h2>
    @if ($errors->any())
        <div class="error-box">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ role: '{{ old('role') }}' }">
        @csrf

        <input type="text" name="name" placeholder="Nombre" value="{{ old('name') }}" required>
        <input type="text" name="lastName" placeholder="Apellido" value="{{ old('lastName') }}" required>
        <input type="email" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" required>

        <select name="role" x-model="role" required>
            <option value="">Selecciona tu rol</option>
            <option value="Usuario">Usuario</option>
            <option value="Alumno">Estudiante</option>
            <option value="Profesor">Profesor</option>
            <option value="Empresa">Empresa</option>
        </select>

        <!-- Campos adicionales según rol -->
        <template x-if="role === 'Alumno'">
            <div class="space-y-2">
                <input type="date" name="birthDate" value="{{ old('birthDate') }}" placeholder="Fecha de nacimiento"
                    required>
                <input type="text" name="currentCourse" value="{{ old('currentCourse') }}" placeholder="Curso actual"
                    required>
                <input type="text" name="educationalCenter" value="{{ old('educationalCenter') }}"
                    placeholder="Centro educativo" required>
            </div>
        </template>

        <template x-if="role === 'Profesor'">
            <div class="space-y-2">
                <input type="date" name="birthDate" value="{{ old('birthDate') }}" placeholder="Fecha de nacimiento"
                    required>
                <input type="text" name="specialization" value="{{ old('specialization') }}"
                    placeholder="Especialización" required>
                <input type="text" name="department" value="{{ old('department') }}" placeholder="Departamento"
                    required>
                <input type="text" name="validationDocument" value="{{ old('validationDocument') }}"
                    placeholder="Documento que lo valide" required>
            </div>
        </template>

        <template x-if="role === 'Empresa'">
            <div class="space-y-2">
                <input type="text" name="cif" value="{{ old('cif') }}" placeholder="CIF" required>
                <input type="text" name="address" value="{{ old('address') }}" placeholder="Dirección" required>
                <input type="text" name="sector" value="{{ old('sector') }}" placeholder="Sector" required>
                <input type="url" name="website" value="{{ old('website') }}" placeholder="Sitio web">
            </div>
        </template>

        <div class="g-recaptcha" data-sitekey="{{ config('services.nocaptcha.sitekey') }}"></div>

        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
            Register
        </button>
    </form>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</body>

</html>