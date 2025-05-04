<h2>Restablecer contraseña</h2>
@if ($errors->any())
    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <label for="email">Correo electrónico</label>
    <input type="email" name="email" value="{{ old('email', $email ?? '') }}" readonly required class="bg-gray-100">



    <label for="password">Nueva contraseña</label>
    <input type="password" name="password" required>

    <label for="password_confirmation">Confirmar contraseña</label>
    <input type="password" name="password_confirmation" required>

    <button type="submit">Restablecer</button>
</form>