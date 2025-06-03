document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('edit-comment-form');
    
    if (editForm) {
        editForm.addEventListener('submit', function (event) {
            const form = event.target;
            const contentInput = form.querySelector('input[name="content"]');
            const content = contentInput.value.trim();
            const errors = [];

            // Validaciones
            if (!content) {
                errors.push("El contenido es requerido");
            } else if (content.length > 100) {
                errors.push("El contenido no puede exceder los 100 caracteres");
            }

            // Mostrar errores
            const errorBox = document.getElementById('comment-errors');
            const errorList = errorBox.querySelector('ul');
            errorList.innerHTML = '';

            if (errors.length > 0) {
                event.preventDefault();
                errorBox.classList.remove('hidden');
                errors.forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    errorList.appendChild(li);
                });
                window.scrollTo({ top: errorBox.offsetTop - 20, behavior: 'smooth' });
            } else {
                errorBox.classList.add('hidden');
            }
        });
    }
});