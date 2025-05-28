<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Autenticación</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('icons/logo.svg') }}">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body class="flex flex-col min-h-screen bg-cover bg-center"
    style="background-image: url('{{ asset('images/welcome-bg.jpg') }}')">

    @if (session('message'))
        <div id="toast"
            class="fixed top-20 right-5 max-w-xs w-full z-50 px-4 py-3 text-sm text-white bg-black rounded-lg shadow-lg animate-slide-in">
            {{ session('message') }}
        </div>

        <style>
            @keyframes slide-in {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                }

                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes slide-out {
                from {
                    opacity: 1;
                    transform: translateX(0);
                }

                to {
                    opacity: 0;
                    transform: translateX(100%);
                }
            }

            .animate-slide-in {
                animation: slide-in 0.5s ease-out forwards;
            }

            .animate-slide-out {
                animation: slide-out 0.5s ease-in forwards;
            }
        </style>
    @endif

    <main class="flex-grow px-12">
        <section x-data="{
                            form: localStorage.getItem('authForm') 
                                ?? '{{ old('name') || old('role') || $errors->has('name') ? 'register' : 'login' }}'
                        }"
                        x-init="$watch('form', value => localStorage.setItem('authForm', value))"

            class="w-full md:w-1/2 lg:w-1/3 mx-auto mt-20 mb-40 px-4 sm:px-6 2md:px-10 py-10 bg-white dark:bg-themeBgDark rounded-2xl shadow-xl transition-all duration-300">

            <div class="text-center mb-6">
                <x-heading level="h2" class="mb-8">
                    <div class="relative h-11 overflow-hidden">
                        <span x-show="form === 'login'" 
                              x-transition:enter="transition ease-out duration-500 transform"
                              x-transition:enter-start="opacity-0 translate-y-8"
                              x-transition:enter-end="opacity-100 translate-y-0"
                              x-transition:leave="transition ease-in duration-300 transform"
                              x-transition:leave-start="opacity-100 translate-y-0"
                              x-transition:leave-end="opacity-0 -translate-y-8"
                              class="absolute inset-0"
                              x-text="'{{ __('messages.auth.login') }}'">
                        </span>
                        <span x-show="form === 'register'" 
                              x-transition:enter="transition ease-out duration-500 transform"
                              x-transition:enter-start="opacity-0 translate-y-8"
                              x-transition:enter-end="opacity-100 translate-y-0"
                              x-transition:leave="transition ease-in duration-300 transform"
                              x-transition:leave-start="opacity-100 translate-y-0"
                              x-transition:leave-end="opacity-0 -translate-y-8"
                              class="absolute inset-0"
                              x-text="'{{ __('messages.auth.register') }}'">
                        </span>
                    </div>
                </x-heading>
            </div>

            @if (session('status'))
                <div class="text-xs md:tex-sm 2md:text-base bg-green-100 border border-green-400 text-green-800 dark:bg-green-200 dark:text-green-900 px-4 py-3 rounded-xl mb-6 shadow-md">
                    {{ session('status') }}
                </div>
            @endif

            <!-- CONTENEDOR PARA FORMULARIOS -->
            <div class="relative">

                <!-- FORMULARIO LOGIN -->
                <div x-show="form === 'login'" 
                     x-transition:enter="transition ease-out duration-600 transform"
                     x-transition:enter-start="opacity-0 -translate-x-full scale-95"
                     x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                     x-transition:leave="transition ease-in duration-400 transform"
                     x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                     x-transition:leave-end="opacity-0 -translate-x-full scale-95"
                     :class="form !== 'login' ? 'absolute inset-0' : ''"
                     class="w-full">
                    
                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                        @csrf
                        @if ($errors->any() && !old('name') && !old('role'))
                            <div x-show="form === 'login'"
                                class="text-xs md:text-sm 2md:text-base bg-red-100 border border-red-400 text-red-700 dark:bg-red-200 dark:text-red-900 px-4 py-3 rounded-xl mb-6 shadow-md"
                                x-transition>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div id="login-errors" class="bg-red-100 text-red-700 p-4 rounded hidden"></div>

                        <div class="space-y-1">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ __('messages.auth.email') }}
                            </label>
                            <input type="email" name="email" id="email"
                                class="w-full px-4 py-2 border text-xs md:text-sm 2md:text-base border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm transition-all duration-300 hover:shadow-md focus:scale-[1.02]"
                                required value="{{ old('email') }}">
                        </div>

                        <div class="space-y-1">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ __('messages.auth.password') }}
                            </label>
                            <input type="password" name="password" id="password"
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm transition-all duration-300 hover:shadow-md focus:scale-[1.02]"
                                required>
                        </div>

                        <div class="flex w-fit items-center gap-2 text-sm text-gray-700 dark:text-gray-300 group">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
                                class="rounded-md border-gray-300 dark:border-gray-600 text-themeBlue focus:ring-2 focus:ring-themeBlue transition-colors duration-200">
                            <label for="remember" class="cursor-pointer group-hover:text-themeBlue transition-colors duration-200">
                                {{ __('messages.auth.remember') }}
                            </label>
                        </div>

                        <button type="submit"
                            class="w-full text-xs md:text-sm 2md:text-base bg-themeBlue text-white py-2 px-4 font-semibold rounded-xl hover:bg-blue-700 active:scale-[.98] transition-all duration-200 shadow-md cursor-pointer hover:shadow-lg transform hover:-translate-y-0.5">
                            {{ __('messages.auth.acceed') }}
                        </button>

                        <a href="{{ route('password.request') }}"
                            class="block text-center text-xs md:text-sm 2md:text-base text-blue-600 hover:underline transition-all duration-200 hover:text-blue-800">
                            {{ __('messages.auth.forgot-password') }}
                        </a>
                        
                        <p class="text-xs md:text-sm 2md:text-base text-center text-gray-500 dark:text-gray-300">
                            <template x-if="form === 'login'">
                                <span>{{ __('messages.auth.no-account') }}
                                    <button @click="form = 'register'"
                                        class="text-blue-500 text-xs md:text-sm 2md:text-base hover:underline cursor-pointer transition-all duration-200 hover:text-blue-700">
                                        {{ __('messages.auth.register') }}
                                    </button>
                                </span>
                            </template>
                        </p>

                        <a href="/auth/google/redirect">
                            <button type="button"
                                class="mt-4 w-full text-xs md:text-sm 2md:text-base bg-themeGrape/90 text-white py-2 px-4 font-semibold rounded-xl hover:bg-themeGrape active:scale-[.98] transition-all duration-200 shadow-md cursor-pointer hover:shadow-lg transform hover:-translate-y-0.5">
                                {{ __('messages.auth.google') }}
                            </button>
                        </a>
                    </form>
                </div>

                <!-- FORMULARIO REGISTRO -->
                <div x-show="form === 'register'" 
                     x-transition:enter="transition ease-out duration-600 transform"
                     x-transition:enter-start="opacity-0 translate-x-full scale-95"
                     x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                     x-transition:leave="transition ease-in duration-400 transform"
                     x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-x-full scale-95"
                     :class="form !== 'register' ? 'absolute inset-0' : ''"
                     class="w-full"
                     x-cloak>
                
                <form method="POST" action="{{ route('register') }}" class="space-y-5"
                    x-data="{ role: '{{ old('role') }}' }">
                    @csrf
                    @if ($errors->any() && (old('name') || old('role')))
                        <div x-show="form === 'register'"
                            class="text-xs md:text-sm 2md:text-base bg-red-100 border border-red-400 text-red-700 dark:bg-red-200 dark:text-red-900 px-4 py-3 rounded-xl mb-6 shadow-md"
                            x-transition>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    <div id="register-errors" class="bg-red-100 text-red-700 p-4 rounded hidden"></div>

                    @foreach (['name' => 'messages.profile.name', 'lastName' => 'messages.profile.last-name', 'email' => 'messages.profile.email',] as $field => $label)
                        <input type="{{ $field === 'email' ? 'email' : 'text' }}" name="{{ $field }}"
                            placeholder="{{ __($label) }}" value="{{ old($field) }}"
                            class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm transition-all duration-300 hover:shadow-md focus:scale-[1.02]"
                            required>
                    @endforeach
                    
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('messages.password-features') }}
                    </span>
                    <input type="password" name="password" placeholder="{{__('messages.auth.ph-password')}}"
                        class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm transition-all duration-300 hover:shadow-md focus:scale-[1.02]"
                        required>

                    <input type="password" name="password_confirmation"
                        placeholder="{{__('messages.auth.ph-password-confirm')}}"
                        class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm transition-all duration-300 hover:shadow-md focus:scale-[1.02]"
                        required>

                    <select name="role" x-model="role" required
                        class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                        <option value="">{{__('messages.auth.select')}}</option>
                        <option value="Usuario">{{__('messages.auth.option-user')}}</option>
                        <option value="Alumno">{{__('messages.auth.option-student')}}</option>
                        <option value="Empresa">{{__('messages.auth.option-company')}}</option>
                    </select>

                    <template x-if="role === 'Alumno'">
                        <div class="space-y-3"
                             x-transition:enter="transition ease-out duration-500 transform"
                             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                            <input type="date" name="birthDate" value="{{ old('birthDate') }}"
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]"
                                required>
                            <input type="text" name="currentCourse" value="{{ old('currentCourse') }}"
                                placeholder="{{__('messages.admin.users.ph-current-course')}}" required
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                            <input type="text" name="educationalCenter" value="{{ old('educationalCenter') }}"
                                placeholder="{{__('messages.admin.users.ph-educational-center')}}" required
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                        </div>
                    </template>

                    <template x-if="role === 'Empresa'">
                        <div class="space-y-3"
                             x-transition:enter="transition ease-out duration-500 transform"
                             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                            <input type="text" name="cif" value="{{ old('cif') }}"
                                placeholder="{{__('messages.profile.cif')}}" required
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                            <input type="text" name="address" value="{{ old('address') }}"
                                placeholder="{{__('messages.profile.address')}}" required
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                            <input type="text" name="sector" value="{{ old('sector') }}"
                                placeholder="{{__('messages.profile.sector')}}" required
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                            <input type="url" name="website" value="{{ old('website') }}"
                                placeholder="{{__('messages.profile.website')}}"
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                        </div>
                    </template>
                    
                    <p class="text-sm text-center text-gray-500 dark:text-gray-300">
                        <template x-if="form === 'register'">
                            <span>{{ __('messages.auth.account')  }}
                                <button @click="form = 'login'"
                                    class="text-blue-500 text-xs md:text-sm 2md:text-base hover:underline cursor-pointer transition-all duration-200 hover:text-blue-700">
                                    {{ __('messages.auth.login') }}
                                </button>
                            </span>
                        </template>
                    </p>

                    <div class="inline-block origin-top-left scale-75 xl:scale-100 transition-transform duration-300">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.nocaptcha.sitekey') }}"></div>
                    </div>


                    <button type="submit"
                        class="w-full bg-themeGrape/90 text-white px-2 py-2 2md:px-4 2md:py-2 text-xs md:text-sm 2md:text-base font-semibold rounded-xl hover:bg-themeGrape active:scale-[.98] transition-all duration-200 shadow-md cursor-pointer hover:shadow-lg transform hover:-translate-y-0.5">
                        {{ __('messages.auth.register') }}
                    </button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <x-footer />

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

        <script>
            document.querySelector('form[action="{{ route('login') }}"]').addEventListener('submit', function (event) {
                const email = this.querySelector('input[name="email"]').value.trim();
                const password = this.querySelector('input[name="password"]').value.trim();
            const errors = [];

                // V alidación de email
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
                else if (password.length < 8 || !/[a-z]/.test(password) || !/[A-Z]/.test(password) || !/\d/.test(password)
                    || !/[^\w]/.test(password))
                    errors.password = "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo."
                        ; if (!role) errors.role = "El rol es obligatorio."; else if (!['Usuario', 'Alumno', 'Profesor'
                            , 'Empresa'].includes(role)) errors.role = "El rol seleccionado no es válido."; if (!recaptcha)
                    errors.recaptcha = "Debes verificar el captcha."; if (role === 'Alumno') {
                        const
                        birthDate = form.querySelector('input[name="birthDate" ]').value; const
                            currentCourse = form.querySelector('input[name="currentCourse" ]').value.trim(); const
                                educationalCenter = form.querySelector('input[name="educationalCenter" ]').value.trim(); if (!birthDate)
                            errors.birthDate = "La fecha de nacimiento es obligatoria."; else if (new Date(birthDate) > new Date())
                            errors.birthDate = "La fecha debe ser anterior o igual a hoy.";

                        if (!currentCourse) errors.currentCourse = "El curso actual es obligatorio.";
                        else if (currentCourse.length > 50) errors.currentCourse = "El curso no puede tener más de 50
                        caracteres.";

                        if (!educationalCenter) errors.educationalCenter = "El centro educativo es obligatorio.";
                        else if (educationalCenter.length > 100) errors.educationalCenter = "El centro no puede tener más de 100
                        caracteres.";
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
                    xfoinsertBefore(box, form.querySelector('button[type="submit"]'));
                }
            });

        </script>

</body>

</html>