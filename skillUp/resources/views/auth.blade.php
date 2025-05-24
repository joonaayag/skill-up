<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Autenticación</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('icons/logo.svg') }}">
    <!-- @vite('resources/css/app.css')
    @vite('resources/js/app.js') -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    @if (session('status'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-black dark:text-white">- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2>{{__('messages.auth.login')}}</h2>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div id="login-errors" class="bg-red-300 text-black p-4 rounded mb-4 hidden"></div>
        <label>{{__('messages.auth.email')}}</label>
        <input type="email" name="email" value="{{ old('email') }}" required><br>
        <label>{{__('messages.auth.password')}}</label>
        <input type="password" name="password" required><br>
        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
        <span>{{__('messages.auth.remember')}}</span>
        <button type="submit">{{__('messages.auth.acceed')}}</button>
    </form>
    <p class="text-sm text-center mt-4">
        <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">
            {{__('messages.auth.forgot-password')}}
        </a>
    </p>

    <a href="/auth/google/redirect">
        <button>{{__('messages.auth.google')}}</button>
    </a>

    <hr>

    <h2>{{__('messages.auth.register')}}</h2>
    <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ role: '{{ old('role') }}' }">
        @csrf

        <div id="register-errors" class="bg-red-100 text-red-700 p-4 rounded mt-4"></div>

        <input type="text" name="name" placeholder="{{__('messages.profile.name')}}" value="{{ old('name') }}" required>
        <input type="text" name="lastName" placeholder="{{__('messages.profile.last-name')}}" value="{{ old('lastName') }}" required>
        <input type="email" name="email" placeholder="{{__('messages.profile.email')}}" value="{{ old('email') }}" required>
        <input type="password" name="password" placeholder="{{__('messages.auth.ph-password')}}" required>
        <input type="password" name="password_confirmation" placeholder="{{__('messages.auth.ph-password-confirm')}}" required>

        <select name="role" x-model="role" required>
            <option value="">{{__('messages.auth.select')}}</option>
            <option value="Usuario">{{__('messages.auth.option-user')}}</option>
            <option value="Alumno">{{__('messages.auth.option-student')}}</option>
            <option value="Empresa">{{__('messages.auth.option-company')}}</option>
        </select>

        <template x-if="role === 'Alumno'">
            <div class="space-y-2">
                <input type="date" name="birthDate" value="{{ old('birthDate') }}" placeholder="{{__('messages.admin.users.ph-birth-date')}}"
                    required>
                <input type="text" name="currentCourse" value="{{ old('currentCourse') }}" placeholder="{{__('messages.admin.users.ph-current-course')}}"
                    required>
                <input type="text" name="educationalCenter" value="{{ old('educationalCenter') }}"
                    placeholder="{{__('messages.admin.users.ph-educational-center')}}" required>
            </div>
        </template>

        <template x-if="role === 'Empresa'">
            <div class="space-y-2">
                <input type="text" name="cif" value="{{ old('cif') }}" placeholder="{{__('messages.profile.cif')}}" required>
                <input type="text" name="address" value="{{ old('address') }}" placeholder="{{__('messages.profile.adress')}}" required>
                <input type="text" name="sector" value="{{ old('sector') }}" placeholder="{{__('messages.profile.sector')}}" required>
                <input type="url" name="website" value="{{ old('website') }}" placeholder="{{__('messages.profile.website')}}">
            </div>
        </template>

        <div class="g-recaptcha" data-sitekey="{{ config('services.nocaptcha.sitekey') }}"></div>

        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
            {{ __('messages.auth.register') }}
        </button>
    </form>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
        document.querySelector('form[action="{{ route('login') }}"]').addEventListener('submit', function(event) {
        const email = this.querySelector('input[name="email"]').value.trim();
        const password = this.querySelector('input[name="password"]').value.trim();
        const errors = [];

        // Validación de email
        if (!email) {
            errors.push("{{ __('validation.required', ['attribute' => 'email']) }}");
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.push("{{ __('validation.email', ['attribute' => 'email']) }}");
        }

        // Validación de contraseña
        if (!password) {
            errors.push("{{ __('validation.required', ['attribute' => 'password']) }}");
        }

        // Mostrar errores
        if (errors.length > 0) {
            event.preventDefault();

            let errorBox = document.getElementById('login-errors');
            if (!errorBox) {
                errorBox = document.createElement('div');
                errorBox.id = 'login-errors';
                errorBox.className = 'bg-red-300 text-black p-4 rounded mb-4';
                const form = this;
                form.parentNode.insertBefore(errorBox, form);
            }

            errorBox.innerHTML = '<ul class="list-disc list-inside">' +
                errors.map(e => `<li>${e}</li>`).join('') +
                '</ul>';
        }
    });
    

        document.querySelector('form[action$="/register"]').addEventListener('submit', function (event) {
            const form = event.target;
            const role = form.querySelector('select[name="role"]').value;
            const errors = {};

            const name = form.querySelector('input[name="name"]').value.trim();
            const lastName = form.querySelector('input[name="lastName"]').value.trim();
            const email = form.querySelector('input[name="email"]').value.trim();
            const password = form.querySelector('input[name="password"]').value;
            const passwordConfirmation = form.querySelector('input[name="password_confirmation"]').value;
            const recaptcha = form.querySelector('[name="g-recaptcha-response"]')?.value;

            if (!name) errors.name = "El nombre es obligatorio.";
            else if (name.length > 20) errors.name = "El nombre no puede tener más de 20 caracteres.";

            if (lastName.length > 40) errors.lastName = "El apellido no puede tener más de 40 caracteres.";

            if (!email) errors.email = "El correo electrónico es obligatorio.";
            else if (!/^[\w-.]+@[\w-]+\.[a-z]{2,}$/i.test(email)) errors.email = "El formato del correo no es válido.";
            else if (email.length > 50) errors.email = "El correo no puede tener más de 50 caracteres.";

            if (!password) errors.password = "La contraseña es obligatoria.";
            else if (password !== passwordConfirmation) errors.password_confirmation = "Las contraseñas no coinciden.";
            else if (password.length < 8 || !/[a-z]/.test(password) || !/[A-Z]/.test(password) || !/\d/.test(password) || !/[^\w]/.test(password))
                errors.password = "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.";

            if (!role) errors.role = "El rol es obligatorio.";
            else if (!['Usuario', 'Alumno', 'Profesor', 'Empresa'].includes(role)) errors.role = "El rol seleccionado no es válido.";

            if (!recaptcha) errors.recaptcha = "Debes verificar el captcha.";

            if (role === 'Alumno') {
                const birthDate = form.querySelector('input[name="birthDate"]').value;
                const currentCourse = form.querySelector('input[name="currentCourse"]').value.trim();
                const educationalCenter = form.querySelector('input[name="educationalCenter"]').value.trim();

                if (!birthDate) errors.birthDate = "La fecha de nacimiento es obligatoria.";
                else if (new Date(birthDate) > new Date()) errors.birthDate = "La fecha debe ser anterior o igual a hoy.";

                if (!currentCourse) errors.currentCourse = "El curso actual es obligatorio.";
                else if (currentCourse.length > 50) errors.currentCourse = "El curso no puede tener más de 50 caracteres.";

                if (!educationalCenter) errors.educationalCenter = "El centro educativo es obligatorio.";
                else if (educationalCenter.length > 100) errors.educationalCenter = "El centro no puede tener más de 100 caracteres.";
            }

            if (role === 'Empresa') {
                const cif = form.querySelector('input[name="cif"]').value.trim();
                const address = form.querySelector('input[name="address"]').value.trim();
                const sector = form.querySelector('input[name="sector"]').value.trim();
                const website = form.querySelector('input[name="website"]').value.trim();

                if (!cif) errors.cif = "El CIF es obligatorio.";
                else if (cif.length > 50) errors.cif = "El CIF no puede tener más de 50 caracteres.";

                if (!address) errors.address = "La dirección es obligatoria.";
                else if (address.length > 255) errors.address = "La dirección no puede tener más de 255 caracteres.";

                if (!sector) errors.sector = "El sector es obligatorio.";
                else if (sector.length > 100) errors.sector = "El sector no puede tener más de 100 caracteres.";

                if (website && website.length > 255) {
                    errors.website = "La URL del sitio web no es válida o es demasiado larga.";
                }
            }

            // Limpiar errores anteriores
            const existingBox = document.getElementById('register-errors');
            if (existingBox) {
                existingBox.remove();
            }

            if (Object.keys(errors).length > 0) {
                event.preventDefault();

                const box = document.createElement('div');
                box.id = 'register-errors';
                box.className = 'bg-red-100 text-red-700 p-4 rounded mt-4';
                const ul = document.createElement('ul');
                Object.values(errors).forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    ul.appendChild(li);
                });
                box.appendChild(ul);
                form.insertBefore(box, form.querySelector('button[type="submit"]'));
            }
        });

    </script>

</body>

</html>