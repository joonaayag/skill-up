<form method="POST" action="completar-perfil">
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
    @if($role === 'Alumno')
        <input type="date" name="birth_date" placeholder="{{ __('messages.profile.birth-date')  }}" required>
        <input type="text" name="current_course" placeholder="{{ __('messages.profile.current-course')  }}" required>
        <input type="text" name="specialization" placeholder="{{ __('messages.profile.specialization')  }}" required>
        <input type="text" name="educational_center" placeholder="{{ __('messages.profile.educational-center')  }}" required>
    @elseif($role === 'Profesor')
        <input type="text" name="department" placeholder="{{ __('messages.profile.department')  }}" required>
        <input type="text" name="educational_center" placeholder="{{ __('messages.profile.educational-center')  }}" required>
    @elseif($role === 'Empresa')
        <input type="text" name="cif" placeholder="{{ __('messages.profile.cif')  }}" required>
        <input type="text" name="address" placeholder="{{ __('messages.profile.address')  }}" required>
        <input type="text" name="sector" placeholder="{{ __('messages.profile.sector')  }}" required>
        <input type="url" name="website" placeholder="{{ __('messages.profile.website')  }}">
    @endif

    <button type="submit">{{ __('messages.save-profile')  }}</button>
</form>