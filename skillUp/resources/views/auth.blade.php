<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autenticación</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Contraseña:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Entrar</button>
    </form>

    <hr>

    <h2>Registrarse</h2>
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <label>Nombre:</label>
        <input type="text" name="name" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Contraseña:</label>
        <input type="password" name="password" required><br>
        <label>Confirmar Contraseña:</label>
        <input type="password" name="password_confirmation" required><br>
        <button type="submit">Registrarse</button>
    </form>
</body>
</html>
