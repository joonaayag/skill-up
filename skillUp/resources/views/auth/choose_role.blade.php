<form method="POST" action="/elegir-rol">
    @csrf
    @if ($errors->any())
        <div class="bg-red-300 border dark:bg-red-300/60 border-red-400 p-4 mb-6 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-black dark:text-white">- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <label for="role">{{ __('messages.choose.role')  }}</label>
    <select name="role" id="role" required>
        <option value="Usuario">{{ __('messages.choose.option-user') }}</option>
        <option value="Alumno">{{ __('messages.choose.option-student') }}</option>
        <option value="Empresa">{{ __('messages.choose.option-company') }}</option>
    </select>
    <button type="submit">{{ __('messages.choose.continue') }}</button>
</form>