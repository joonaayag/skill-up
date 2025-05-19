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

        <input type="text" name="name" placeholder="{{__('messages.profile.name')}}" value="{{ old('name') }}" required>
        <input type="text" name="lastName" placeholder="{{__('messages.profile.last-name')}}" value="{{ old('lastName') }}" required>
        <input type="email" name="email" placeholder="{{__('messages.profile.email')}}" value="{{ old('email') }}" required>
        <input type="password" name="password" placeholder="{{__('messages.auth.ph-password')}}" required>
        <input type="password" name="password_confirmation" placeholder="{{__('messages.auth.ph-password-confirm')}}" required>

        <select name="role" x-model="role" required>
            <option value="">{{__('messages.auth.select')}}</option>
            <option value="Usuario">{{__('messages.auth.option-user')}}</option>
            <option value="Alumno">{{__('messages.auth.option-student')}}</option>
            <option value="Profesor">{{__('messages.auth.option-teacher')}}</option>
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

        <template x-if="role === 'Profesor'">
            <div class="space-y-2">
                <input type="date" name="birthDate" value="{{ old('birthDate') }}" placeholder="{{ __('messages.admin.users.ph-birth-date') }}"
                    required>
                <input type="text" name="specialization" value="{{ old('specialization') }}"
                    placeholder="{{__('messages.admin.users.ph-specialization')}}" required>
                <input type="text" name="department" value="{{ old('department') }}" placeholder="{{__('messages.admin.users.ph-department')}}"
                    required>
                <input type="text" name="validationDocument" value="{{ old('validationDocument') }}"
                    placeholder="{{__('messages.admin.users.ph-validation-document')}}" required>
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

</body>

</html>