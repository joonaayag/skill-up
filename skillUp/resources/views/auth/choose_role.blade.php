<form method="POST" action="/elegir-rol">
    @csrf
    <label for="role">¿Cuál es tu perfil para la aplicación?</label>
    <select name="role" id="role" required>
        <option value="usuario">Usuario</option>
        <option value="alumno">Alumno</option>
        <option value="profesor">Profesor</option>
        <option value="empresa">Empresa</option>
    </select>
    <button type="submit">Continuar</button>
</form>
