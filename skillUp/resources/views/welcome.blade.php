<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SkillUp</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body>
    <h1 class="bg-pink-900">Bienvenido a la plataforma</h1>
    <a href="{{ route('auth') }}">Iniciar sesión o registrarse</a>
</body>
</html>
