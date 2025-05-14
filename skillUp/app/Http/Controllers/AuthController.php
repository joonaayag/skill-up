<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show()
    {
        return view('auth');
    }

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect('/dashboard');
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:20',
            'lastName' => 'nullable|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
            'role' => 'required|in:Usuario,Alumno,Profesor,Empresa',
            'g-recaptcha-response' => 'required',
        ];

        switch ($request->role) {
            case 'Alumno':
                $rules = array_merge($rules, [
                    'birthDate' => 'required|date',
                    'currentCourse' => 'required|string|max:50',
                    'educationalCenter' => 'required|string|max:100',
                ]);
                break;
            case 'Profesor':
                $rules = array_merge($rules, [
                    'birthDate' => 'required|date',
                    'specialization' => 'required|string|max:100',
                    'department' => 'required|string|max:100',
                    'validationDocument' => 'required|string|max:255',
                ]);
                break;
            case 'Empresa':
                $rules = array_merge($rules, [
                    'cif' => 'required|string|max:50',
                    'address' => 'required|string|max:255',
                    'sector' => 'required|string|max:100',
                    'website' => 'nullable|url|max:255',
                ]);
                break;
        }

        $messages = [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 20 caracteres.',

            'lastName.string' => 'El apellido debe ser una cadena de texto.',
            'lastName.max' => 'El apellido no puede tener más de 50 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser una cadena de texto.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.*' => 'La contraseña debe incluir mayúsculas, minúsculas, números y símbolos.',



            'role.required' => 'El rol es obligatorio.',
            'role.in' => 'El rol seleccionado no es válido.',

            'g-recaptcha-response.required' => 'Por favor, verifica que no eres un robot.',

            'birthDate.required' => 'La fecha de nacimiento es obligatoria.',
            'birthDate.date' => 'La fecha de nacimiento debe ser válida.',
            'currentCourse.required' => 'El curso actual es obligatorio.',
            'currentCourse.string' => 'El curso actual debe ser una cadena de texto.',
            'currentCourse.max' => 'El curso actual no puede tener más de 50 caracteres.',
            'educationalCenter.required' => 'El centro educativo es obligatorio.',
            'educationalCenter.string' => 'El centro educativo debe ser una cadena de texto.',
            'educationalCenter.max' => 'El centro educativo no puede tener más de 100 caracteres.',

            'specialization.required' => 'La especialización es obligatoria.',
            'specialization.string' => 'La especialización debe ser una cadena de texto.',
            'specialization.max' => 'La especialización no puede tener más de 100 caracteres.',
            'department.required' => 'El departamento es obligatorio.',
            'department.string' => 'El departamento debe ser una cadena de texto.',
            'department.max' => 'El departamento no puede tener más de 100 caracteres.',
            'validationDocument.required' => 'El documento de validación es obligatorio.',
            'validationDocument.string' => 'El documento de validación debe ser una cadena de texto.',
            'validationDocument.max' => 'El documento de validación no puede tener más de 255 caracteres.',

            'cif.required' => 'El CIF es obligatorio.',
            'cif.string' => 'El CIF debe ser una cadena de texto.',
            'cif.max' => 'El CIF no puede tener más de 50 caracteres.',
            'address.required' => 'La dirección es obligatoria.',
            'address.string' => 'La dirección debe ser una cadena de texto.',
            'address.max' => 'La dirección no puede tener más de 255 caracteres.',
            'sector.required' => 'El sector es obligatorio.',
            'sector.string' => 'El sector debe ser una cadena de texto.',
            'sector.max' => 'El sector no puede tener más de 100 caracteres.',
            'website.url' => 'La página web debe tener un formato de URL válido.',
            'website.max' => 'La página web no puede tener más de 255 caracteres.',
        ];

        $request->validate($rules, $messages);


        $user = User::create([
            'name' => ucfirst($request->name),
            'last_name' => ucfirst($request->lastName),
            'email' => ucfirst($request->email),
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'profile' => null,
            'banner' => null
        ]);

        $details = ['user_id' => $user->id];

        switch ($user->role) {
            case 'Alumno':
                $details += [
                    'birth_date' => $request->birthDate,
                    'current_course' => ucfirst($request->currentCourse),
                    'educational_center' => ucfirst($request->educationalCenter),
                ];
                break;
            case 'Profesor':
                $details += [
                    'birth_date' => $request->birthDate,
                    'specialization' => ucfirst($request->specialization),
                    'department' => ucfirst($request->department),
                    'validation_document' => $request->validationDocument,
                ];
                break;
            case 'Empresa':
                $details += [
                    'cif' => ucfirst($request->cif),
                    'address' => ucfirst($request->address),
                    'sector' => ucfirst($request->sector),
                    'website' => $request->website,
                ];
                break;
        }

        UserDetail::create($details);
        Auth::login($user);

        $message = match ($user->role) {
            'Alumno' => [
                'title' => '¡Bienvenido a SkillUp!',
                'message' => 'Ya puedes explorar ofertas de trabajo, aplicar a ellas y ver proyectos de otros usuarios.',
            ],
            'Usuario' => [
                'title' => '¡Bienvenido a SkillUp!',
                'message' => 'Ya puedes explorar ofertas de trabajo, aplicar a ellas y ver proyectos de otros usuarios.',
            ],
            'Empresa' => [
                'title' => 'Tu cuenta de empresa está lista',
                'message' => 'Ahora puedes publicar ofertas y recibir candidaturas de estudiantes.',
            ],
            'Profesor' => [
                'title' => 'Perfil de profesor creado',
                'message' => 'Puedes gestionar y validar proyectos académicos desde tu panel.',
            ],
            default => null,
        };

        if ($message) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'mensaje',
                'title' => $message['title'],
                'message' => $message['message'],
            ]);
        }

        return redirect('/dashboard');

    }
}
