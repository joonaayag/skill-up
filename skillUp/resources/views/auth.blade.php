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

<body class="flex flex-col min-h-screen bg-center bg-cover bg-fixed"
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

            class="w-full md:w-1/2 xl:w-1/3 mx-auto mt-20 mb-40 px-4 sm:px-6 2md:px-10 py-10 bg-white dark:bg-themeBgDark rounded-2xl shadow-xl transition-all duration-300">

            <div class="text-center px-4 sm:px-6 lg:px-8">
                <a href="/"><x-icon name="logo" class="w-16 h-auto mb-2 mx-auto sm:w-20" /></a>
                
                <x-heading level="h2" class="mb-4 text-2xl sm:text-3xl font-semibold">
                    <div class="relative h-12 sm:h-14 overflow-hidden">
                        <span x-show="form === 'login'"
                            x-transition:enter="transition ease-out duration-500 transform"
                            x-transition:enter-start="opacity-0 translate-y-6"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-300 transform"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-6"
                            class="absolute inset-0 w-full"
                            x-text="'{{ __('messages.auth.login') }}'">
                        </span>
                        <span x-show="form === 'register'"
                            x-transition:enter="transition ease-out duration-500 transform"
                            x-transition:enter-start="opacity-0 translate-y-6"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-300 transform"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-6"
                            class="absolute inset-0 w-full"
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
                    
                    <form id="login" action="{{ route('login') }}" method="POST" class="space-y-4">
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

                        <!-- Email -->
                        <div class="space-y-1">
                            <x-label for="email">{{ __('messages.auth.email') }}<x-red-required /></x-label>
                            <div class="relative w-full transition-all duration-300 hover:shadow-md focus-within:scale-[1.02]">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <x-icon name="mail" class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                                </div>
                                <input type="email" name="email" id="email"
                                    value="{{ old('email') }}"
                                    class="w-full pl-10 pr-4 py-2 border text-xs md:text-sm 2md:text-base border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm"
                                    >
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="space-y-1">
                            <x-label for="password">{{ __('messages.auth.password') }}<x-red-required /></x-label>
                            <div class="relative w-full transition-all duration-300 hover:shadow-md focus-within:scale-[1.02]" x-data="{ showPassword: false }">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <x-icon name="password" class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                                </div>
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                                    class="w-full pl-10 pr-12 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm"
                                    >
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                                    <template x-if="!showPassword">
                                        <x-icon name="eye" class="w-5 h-5" />
                                    </template>
                                    <template x-if="showPassword">
                                        <x-icon name="close-eye" class="w-5 h-5" />
                                    </template>
                                </button>
                            </div>
                        </div>

                        <div class="flex w-full justify-between items-center gap-2 text-xs md:text-sm 2md:text-base text-gray-700 dark:text-gray-300 group">
                            <label for="remember" class="cursor-pointer group-hover:text-themeBlue transition-colors duration-200">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
                                class="rounded-md border-gray-300 dark:border-gray-600 text-themeBlue focus:ring-2 focus:ring-themeBlue transition-colors duration-200">
                                {{ __('messages.auth.remember') }}
                            </label>
                            <a href="{{ route('password.request') }}"
                                class="block text-center text-blue-600 hover:underline transition-all duration-200 hover:text-blue-800">
                                {{ __('messages.auth.forgot-password') }}
                            </a>
                        </div>

                        <button type="submit"
                            class="w-full text-xs md:text-sm 2md:text-base bg-themeBlue text-white py-2 px-4 font-semibold rounded-xl hover:bg-blue-700 active:scale-[.98] transition-all duration-200 shadow-md cursor-pointer hover:shadow-lg transform hover:-translate-y-0.5">
                            {{ __('messages.auth.acceed') }}
                        </button>

                        

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
                
                <form id="register" method="POST" action="{{ route('register') }}" class="space-y-5"
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

                    @foreach (['name' => 'messages.profile.name', 'lastName' => 'messages.profile.last-name', 'email' => 'messages.profile.email'] as $field => $label)
                    @if ($field === 'email')
                        <div class="relative w-full transition-all duration-300 hover:shadow-md focus-within:scale-[1.02]">
                            <!-- Icono -->
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <x-icon name="mail" class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                            </div>

                            <input type="email" name="email"
                                placeholder="{{ __('messages.auth.email') }}"
                                value="{{ old('email') }}"
                                class="w-full pl-10 pr-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm"
                                required>
                        </div>

                    @else
                        <input
                            type="text"
                            name="{{ $field }}"
                            placeholder="{{ __($label) . '*' }}"
                            value="{{ old($field) }}"
                            class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm transition-all duration-300 hover:shadow-md focus:scale-[1.02]"
                            required
                        >
                    @endif
                @endforeach

                    
                    <div x-data="{ showTip: false }" class="relative w-full transition-all duration-300 hover:shadow-md focus-within:scale-[1.02]">
                        <div x-show="showTip"
                            x-transition
                            class="absolute -top-12 sm:-top-10 left-0 w-full bg-black text-white text-xs rounded-lg shadow-lg px-3 py-2 z-10 dark:bg-gray-900"
                            style="display: none;">
                            {{ __('messages.password-features') }}
                        </div>

                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-icon name="password" class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                        </div>

                        <input
                            type="password"
                            name="password"
                            placeholder="{{ __('messages.auth.ph-password') . '*' }}"
                            @input.debounce.300ms="showTip = $event.target.value.length > 0"
                            @blur="showTip = false"
                            class="w-full pl-10 pr-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm"
                            required
                        >
                    </div>



                    <input type="password" name="password_confirmation"
                        placeholder="{{__('messages.auth.ph-password-confirm') . '*'}}"
                        class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm transition-all duration-300 hover:shadow-md focus:scale-[1.02]"
                        required>

                    <select name="role" x-model="role" required
                        class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-themeBlue focus:border-themeBlue shadow-sm transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                        <option value="">{{__('messages.auth.select') . '*'}}</option>
                        <option value="Usuario">{{__('messages.auth.option-user')}}</option>
                        <option value="Alumno">{{__('messages.auth.option-student')}}</option>
                        <option value="Empresa">{{__('messages.auth.option-company')}}</option>
                    </select>

                    <template x-if="role === 'Alumno'">
                        <div class="space-y-3"
                             x-transition:enter="transition ease-out duration-500 transform"
                             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                              <x-label for="birth_date">{{ __('messages.profile.birth-date') . '*' }}</x-label>
                            <input type="date" name="birthDate" value="{{ old('birthDate') }}"
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]"
                                required>
                            <input type="text" name="currentCourse" value="{{ old('currentCourse') }}"
                                placeholder="{{__('messages.admin.users.ph-current-course') . '*'}}" required
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                            <input type="text" name="educationalCenter" value="{{ old('educationalCenter')}}"
                                placeholder="{{__('messages.admin.users.ph-educational-center') . '*'}}" required
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                        </div>
                    </template>

                    <template x-if="role === 'Empresa'">
                        <div class="space-y-3"
                             x-transition:enter="transition ease-out duration-500 transform"
                             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                            <input type="text" name="cif" value="{{ old('cif') }}"
                                placeholder="{{__('messages.profile.cif') . '*'}}" required
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                            <input type="text" name="address" value="{{ old('address') }}"
                                placeholder="{{__('messages.profile.address') . '*'}}" required
                                class="w-full px-4 py-2 text-xs md:text-sm 2md:text-base border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-themeBlue focus:border-themeBlue transition-all duration-300 hover:shadow-md focus:scale-[1.02]">
                            <input type="text" name="sector" value="{{ old('sector') }}"
                                placeholder="{{__('messages.profile.sector') . '*'}}" required
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
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login');
    if (!form) return;

    form.addEventListener('submit', function (event) {
        const emailInput = form.querySelector('input[name="email"]');
        const passwordInput = form.querySelector('input[name="password"]');
        const errors = [];

        const email = emailInput.value.trim();
        const password = passwordInput.value;

        // Validación de email
        if (!email) {
            errors.push("El correo electrónico es obligatorio.");
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.push("El formato del correo no es válido.");
        }

        // Validación de contraseña
        if (!password) {
            errors.push("La contraseña es obligatoria.");
        }

        // Mostrar errores si los hay
        const errorBox = document.getElementById('login-errors');
        if (errors.length > 0) {
            event.preventDefault();

            errorBox.classList.remove('hidden');
            errorBox.innerHTML = `
                <ul class="list-disc list-inside space-y-1">
                    ${errors.map(msg => `<li>${msg}</li>`).join('')}
                </ul>
            `;
        } else {
            errorBox.classList.add('hidden');
            errorBox.innerHTML = '';
        }
    });
});


// Validación JavaScript para formulario de registro
document.addEventListener('DOMContentLoaded', function() {
    console.log("VALIDACNDO")
    const form = document.getElementById('register');
    const errorContainer = document.getElementById('register-errors');

    // Función para mostrar errores
    function showErrors(errors) {
        if (errors.length > 0) {
            errorContainer.innerHTML = '<ul class="list-disc list-inside space-y-1">' + 
                errors.map(error => `<li>${error}</li>`).join('') + '</ul>';
            errorContainer.classList.remove('hidden');
            errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            errorContainer.classList.add('hidden');
        }
    }

    // Función para limpiar errores
    function clearErrors() {
        errorContainer.classList.add('hidden');
    }

    // Validación de contraseña
    function validatePassword(password) {
        const errors = [];
        
        if (password.length < 8) {
            errors.push('La contraseña debe tener al menos 8 caracteres');
        }
        
        if (!/[a-z]/.test(password)) {
            errors.push('La contraseña debe contener al menos una letra minúscula');
        }
        
        if (!/[A-Z]/.test(password)) {
            errors.push('La contraseña debe contener al menos una letra mayúscula');
        }
        
        if (!/\d/.test(password)) {
            errors.push('La contraseña debe contener al menos un número');
        }
        
        if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(password)) {
            errors.push('La contraseña debe contener al menos un símbolo especial');
        }
        
        return errors;
    }

    // Validación de email
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Validación de fecha de nacimiento
    function validateBirthDate(date) {
        const today = new Date();
        const birthDate = new Date(date);
        return birthDate <= today;
    }

    // Validación de URL
    function validateURL(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }

    // Función principal de validación
    function validateForm() {
        const errors = [];
        
        // Obtener valores del formulario
        const name = form.querySelector('[name="name"]').value.trim();
        const lastName = form.querySelector('[name="lastName"]').value.trim();
        const email = form.querySelector('[name="email"]').value.trim();
        const password = form.querySelector('[name="password"]').value;
        const passwordConfirmation = form.querySelector('[name="password_confirmation"]').value;
        const role = form.querySelector('[name="role"]').value;
        const recaptcha = grecaptcha?.getResponse();

        // Validación del nombre
        if (!name) {
            errors.push('El nombre es obligatorio');
        } else if (name.length > 20) {
            errors.push('El nombre no puede tener más de 20 caracteres');
        }

        // Validación del apellido (opcional pero con límite)
        if (lastName && lastName.length > 40) {
            errors.push('El apellido no puede tener más de 40 caracteres');
        }

        // Validación del email
        if (!email) {
            errors.push('El email es obligatorio');
        } else if (!validateEmail(email)) {
            errors.push('El formato del email no es válido');
        } else if (email.length > 50) {
            errors.push('El email no puede tener más de 50 caracteres');
        }

        // Validación de la contraseña
        if (!password) {
            errors.push('La contraseña es obligatoria');
        } else {
            const passwordErrors = validatePassword(password);
            errors.push(...passwordErrors);
        }

        // Validación de confirmación de contraseña
        if (!passwordConfirmation) {
            errors.push('La confirmación de contraseña es obligatoria');
        } else if (password !== passwordConfirmation) {
            errors.push('Las contraseñas no coinciden');
        }

        // Validación del rol
        if (!role) {
            errors.push('Debe seleccionar un rol');
        } else if (!['Usuario', 'Alumno', 'Empresa'].includes(role)) {
            errors.push('El rol seleccionado no es válido');
        }

        // Validación del reCaptcha
        if (!recaptcha || recaptcha.length === 0) {
            errors.push('Debe completar el reCaptcha');
        }

        // Validaciones específicas según el rol
        if (role === 'Alumno') {
            const birthDate = form.querySelector('[name="birthDate"]')?.value;
            const currentCourse = form.querySelector('[name="currentCourse"]')?.value.trim();
            const educationalCenter = form.querySelector('[name="educationalCenter"]')?.value.trim();

            if (!birthDate) {
                errors.push('La fecha de nacimiento es obligatoria para estudiantes');
            } else if (!validateBirthDate(birthDate)) {
                errors.push('La fecha de nacimiento no puede ser futura');
            }

            if (!currentCourse) {
                errors.push('El curso actual es obligatorio para estudiantes');
            } else if (currentCourse.length > 50) {
                errors.push('El curso actual no puede tener más de 50 caracteres');
            }

            if (!educationalCenter) {
                errors.push('El centro educativo es obligatorio para estudiantes');
            } else if (educationalCenter.length > 100) {
                errors.push('El centro educativo no puede tener más de 100 caracteres');
            }
        }

        if (role === 'Empresa') {
            const cif = form.querySelector('[name="cif"]')?.value.trim();
            const address = form.querySelector('[name="address"]')?.value.trim();
            const sector = form.querySelector('[name="sector"]')?.value.trim();
            const website = form.querySelector('[name="website"]')?.value.trim();

            if (!cif) {
                errors.push('El CIF es obligatorio para empresas');
            } else if (cif.length > 50) {
                errors.push('El CIF no puede tener más de 50 caracteres');
            }

            if (!address) {
                errors.push('La dirección es obligatoria para empresas');
            } else if (address.length > 255) {
                errors.push('La dirección no puede tener más de 255 caracteres');
            }

            if (!sector) {
                errors.push('El sector es obligatorio para empresas');
            } else if (sector.length > 100) {
                errors.push('El sector no puede tener más de 100 caracteres');
            }

            if (website && website.length > 0) {
                if (!validateURL(website)) {
                    errors.push('El formato de la página web no es válido');
                } else if (website.length > 255) {
                    errors.push('La página web no puede tener más de 255 caracteres');
                }
            }
        }

        return errors;
    }

    // Validación en tiempo real para algunos campos
    const passwordField = form.querySelector('[name="password"]');
    const passwordConfirmField = form.querySelector('[name="password_confirmation"]');
    const emailField = form.querySelector('[name="email"]');

    // Validación en tiempo real del email
    emailField.addEventListener('blur', function() {
        const email = this.value.trim();
        if (email && !validateEmail(email)) {
            this.style.borderColor = '#ef4444';
        } else {
            this.style.borderColor = '';
        }
    });

    // Validación en tiempo real de la confirmación de contraseña
    passwordConfirmField.addEventListener('input', function() {
        const password = passwordField.value;
        const confirmation = this.value;
        
        if (confirmation && password !== confirmation) {
            this.style.borderColor = '#ef4444';
        } else {
            this.style.borderColor = '';
        }
    });

    // Limpiar errores cuando el usuario comience a escribir
    form.addEventListener('input', function() {
        if (!errorContainer.classList.contains('hidden')) {
            setTimeout(clearErrors, 1000); // Limpiar después de 1 segundo
        }
    });

    // Validación al enviar el formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const errors = validateForm();
        
        if (errors.length > 0) {
            showErrors(errors);
            return false;
        }
        
        // Si no hay errores, enviar el formulario
        clearErrors();
        
        // Aquí podrías agregar una validación adicional del email único via AJAX
        // Por ahora, simplemente enviamos el formulario
        this.submit();
    });

    // Función para validar email único (opcional - requiere endpoint AJAX)
    async function checkEmailUnique(email) {
        try {
            const response = await fetch('/check-email-unique', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
                },
                body: JSON.stringify({ email: email })
            });
            
            const data = await response.json();
            return data.unique;
        } catch (error) {
            console.log('Error checking email uniqueness:', error);
            return true; // Asumir que es único si hay error
        }
    }

    // Validar email único cuando pierda el foco (opcional)
    emailField.addEventListener('blur', async function() {
        const email = this.value.trim();
        if (email && validateEmail(email)) {
            const isUnique = await checkEmailUnique(email);
            if (!isUnique) {
                this.style.borderColor = '#ef4444';
                showErrors(['Este email ya está registrado']);
            }
        }
    });
});
            
        </script>

</body>

</html>