<form method="POST" action="/elegir-rol">
    @csrf
    <label for="role">¿Cuál es tu perfil para la aplicación?</label>
    <select name="role" id="role" required>
        <option value="Usuario">Usuario</option>
        <option value="Alumno">Alumno</option>
        <option value="Profesor">Profesor</option>
        <option value="Empresa">Empresa</option>
    </select>
    <button type="submit">Continuar</button>
</form>
