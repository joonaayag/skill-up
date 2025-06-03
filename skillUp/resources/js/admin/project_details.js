document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('rating-form');
            const stars = document.querySelectorAll('.rating-star');
            const ratingValue = document.getElementById('rating-value');
            const messageDiv = document.getElementById('rating-message');

            if (!form) return; // Si no hay formulario (usuario no autenticado)

            const projectId = form.dataset.projectId;
            const csrfToken = form.querySelector('input[name="_token"]').value;

            // Agregar event listeners a todas las estrellas
            stars.forEach(star => {
                star.addEventListener('click', function (e) {
                    e.preventDefault();
                    const rating = parseInt(this.dataset.rating);
                    submitRating(rating);
                });
            });

            function submitRating(rating) {
                // Deshabilitar botones durante la petición
                stars.forEach(star => star.disabled = true);

                fetch(`/projects/${projectId}/rate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        rating: rating
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Actualizar el promedio mostrado
                            updateAverageRating(data.averageRating);

                            // Actualizar las estrellas seleccionadas
                            updateStarSelection(rating);

                        } else {
                            throw new Error(data.message || 'Error al enviar la calificación');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        stars.forEach(star => star.disabled = false);
                    });
            }

            function updateAverageRating(newAverage) {
                if (newAverage && newAverage > 0) {
                    ratingValue.textContent = parseFloat(newAverage).toFixed(1);
                } else {
                    ratingValue.textContent = 'N/A';
                }
            }

            function updateStarSelection(userRating) {
                stars.forEach((star, index) => {
                    const starValue = index + 1;
                    if (starValue <= userRating) {
                        star.classList.remove('text-gray-400');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-400');
                    }
                });
            }
        });
    