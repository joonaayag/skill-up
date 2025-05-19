<h2>{{ __('messages.auth.res-password') }}</h2>
@if ($errors->any())
    <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
        <ul>
            @foreach ($errors->all() as $error)
                <li class="text-black dark:text-white">- {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <label for="email">{{ __('messages.auth.email')  }}</label>
    <input type="email" name="email" value="{{ old('email', $email ?? '') }}" readonly required class="bg-gray-100">



    <label for="password">{{ __('messages.auth.ph-password')  }}</label>
    <input type="password" name="password" required>

    <label for="password_confirmation">{{ __('messages.auth.ph-password-confirm') }}</label>
    <input type="password" name="password_confirmation" required>

    <button type="submit">{{ __('messages.auth.reset')  }}</button>
</form>