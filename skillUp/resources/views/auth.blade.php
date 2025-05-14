<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Autenticación</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('icons/logo.svg') }}">
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
        <input type="email" name="email" required><br>
        <label>Contraseña:</label>
        <input type="password" name="password" required><br>
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
    <form method="POST" action="{{ route('register') }}" class="space-y-4" id="registerForm">
        @csrf

        <input type="text" name="name" placeholder="Nombre" required class="w-full p-3 border rounded">
        <input type="text" name="lastName" placeholder="Apellido" required class="w-full p-3 border rounded">
        <input type="email" name="email" placeholder="Correo electrónico" required class="w-full p-3 border rounded">
        <input type="password" name="password" placeholder="Password" required class="w-full p-3 border rounded">
        <input type="password" name="password_confirmation" placeholder="Confirmar contraseña" required
            class="w-full p-3 border rounded">

        <select name="role" id="role" required class="w-full p-3 border rounded">
            <option value="">Selecciona tu rol</option>
            <option value="Usuario">Usuario</option>
            <option value="Alumno">Estudiante</option>
            <option value="Profesor">Profesor</option>
            <option value="Empresa">Empresa</option>
        </select>

        <!-- Dynamic fields based on role -->
        <div id="additionalFields" class="space-y-4"></div>

        <div class="g-recaptcha" data-sitekey="{{ config('services.nocaptcha.sitekey') }}"></div>


        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
            Register
        </button>
    </form>

    <script>
        document.getElementById('role').addEventListener('change', function () {
            const container = document.getElementById('additionalFields');
            container.innerHTML = '';
            const role = this.value;

            if (role === 'Alumno') {
                container.innerHTML = `
                <input type="date" name="birthDate" placeholder="Fecha de nacimiento" required>
                <input type="text" name="currentCourse" placeholder="Curso actual" required>
                <input type="text" name="educationalCenter" placeholder="Centro educativo" required>
            `;
            } else if (role === 'Profesor') {
                container.innerHTML = `
                <input type="date" name="birthDate" placeholder="Fecha de nacimiento" required>
                <input type="text" name="specialization" placeholder="Especialización" required>
                <input type="text" name="department" placeholder="Departmento" required>
                <input type="text" name="validationDocument" placeholder="Documento que lo valide" required>
            `;
            } else if (role === 'Empresa') {
                container.innerHTML = `
                <input type="text" name="cif" placeholder="CIF" required>
                <input type="text" name="address" placeholder="Dirección" required>
                <input type="text" name="sector" placeholder="Sector" required>
                <input type="url" name="website" placeholder="Sitio web">
            `;
            }
        });
    </script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</body>

</html>