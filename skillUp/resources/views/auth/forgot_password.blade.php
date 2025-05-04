<h2>¿Has olvidado tu contraseña?</h2>

@if (session('status'))
    <div class="text-green-500">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <label for="email">Correo electrónico</label>
    <input type="email" name="email" required>

    @error('email')
        <div class="text-red-500">{{ $message }}</div>
    @enderror

    <button type="submit">Enviar enlace de recuperación</button>
</form>
