<form method="POST" action="completar-perfil"> 
    @csrf

    @if($role === 'Alumno')
        <input type="date" name="birth_date" placeholder="Fecha de nacimiento" required>
        <input type="text" name="current_course" placeholder="Curso actual" required>
        <input type="text" name="specialization" placeholder="Especialidad" required>
        <input type="text" name="educational_center" placeholder="Centro educativo" required>
    @elseif($role === 'Profesor')
        <input type="text" name="department" placeholder="Departamento" required>
        <input type="text" name="educational_center" placeholder="Centro educativo" required>
    @elseif($role === 'Empresa')
        <input type="text" name="cif" placeholder="CIF" required>
        <input type="text" name="address" placeholder="Dirección" required>
        <input type="text" name="sector" placeholder="Sector" required>
        <input type="url" name="website" placeholder="Web (opcional)">
    @endif

    <button type="submit">Guardar perfil</button>
</form>
