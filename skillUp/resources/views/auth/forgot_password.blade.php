<h2>{{ __('messages.auth.forgot-password') }}</h2>
@if ($errors->any())
    <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
        <ul>
            @foreach ($errors->all() as $error)
                <li class="text-black dark:text-white">- {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('status'))
    <div class="text-green-500">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <label for="email">{{ __('messages.auth.password')  }}</label>
    <input type="email" name="email" required>

    <button type="submit">{{ __('messages.auth.send-link') }}</button>
</form>