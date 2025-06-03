document.getElementById('mi-form').addEventListener('submit', function (event) {
            const formData = {
                name: document.getElementById('name')?.value.trim() || '',
                subtitle: document.getElementById('subtitle')?.value.trim() || '',
                description: document.getElementById('description')?.value.trim() || '',
                sector_category: document.getElementById('sector_category')?.value || '',
                general_category: document.getElementsByName('general_category')[0]?.value || '',
                state: document.getElementsByName('state')[0]?.value || ''
            };

            const errors = {};

            if (!formData.name) {
                errors.name = "{{ __('messages.errors.name.required') }}";
            } else if (formData.name.length > 40) {
                errors.name = "{{ __('messages.errors.name.max') }}";
            }

            if (formData.subtitle && formData.subtitle.length > 255) {
                errors.subtitle = "{{ __('messages.errors.subtitle.max') }}";
            }

            if (!formData.description) {
                errors.description = "{{ __('messages.errors.description.required') }}";
            }

            const validSectors = [
                'Agricultura/Medio ambiente', 'Arte/Cultura', 'Automoción', 'Ciberseguridad', 'Community Manager', 'Construcción',
                'Coordinación Educativa', 'Diseño Gráfico', 'Electricidad y fontanería', 'Energía/Renovables', 'Farmacia', 'Finanzas y contabilidad',
                'Fotografía/vídeo', 'Hostelería/turismo', 'AI', 'Investigación/laboratorio', 'Legal', 'Logística', 'Mecánica', 'Medicina/Enfermería',
                'Nutrición', 'Operador Industrial', 'Orientación', 'Periodismo', 'Enseñanza', 'Psicología', 'Publicidad', 'Redes y Sistemas',
                'RRHH', 'Seguridad', 'SEO/SEM', 'Terapias/Rehabilitación', 'Traducción', 'Transporte/Entrega', 'Ventas'
            ];
            if (!formData.sector_category) {
                errors.sector_category = "{{ __('messages.errors.sector_offer.required') }}";
            } else if (!validSectors.includes(formData.sector_category)) {
                errors.sector_category = "{{ __('messages.errors.sector_offer.in') }}";
            }

            const validCategories = [
                'Administración y negocio', 'Ciencia y salud', 'Comunicación', 'Diseño y comunicación',
                'Educación', 'Industria', 'Otro', 'Tecnología y desarrollo'
            ];
            if (!formData.general_category) {
                errors.general_category = "{{ __('messages.errors.sector.required') }}";
            } else if (!validCategories.includes(formData.general_category)) {
                errors.general_category = "{{ __('messages.errors.sector.in') }}";
            }

            if (!formData.state) {
                errors.state = "{{ __('messages.errors.state.required') }}";
            } else if (!['abierta', 'cerrada'].includes(formData.state)) {
                errors.state = "{{ __('messages.errors.state.in') }}";
            }

            const errorBox = document.getElementById('form-errors');
            const errorList = document.getElementById('error-list');

            if (Object.keys(errors).length > 0) {
                event.preventDefault();

                errorList.innerHTML = '';
                errorBox.classList.remove('hidden');

                Object.values(errors).forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    errorList.appendChild(li);
                });
            } else {
                errorBox.classList.add('hidden');
            }
        });
    