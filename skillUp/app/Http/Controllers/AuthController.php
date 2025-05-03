<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:usuario,alumno,profesor,empresa',
        ];

        switch ($request->role) {
            case 'alumno':
                $rules = array_merge($rules, [
                    'birthDate' => 'required|date',
                    'currentCourse' => 'required|string|max:50',
                    'educationalCenter' => 'required|string|max:100',
                ]);
                break;
            case 'profesor':
                $rules = array_merge($rules, [
                    'birthDate' => 'required|date',
                    'specialization' => 'required|string|max:100',
                    'department' => 'required|string|max:100',
                    'validationDocument' => 'required|string|max:255',
                ]);
                break;
            case 'empresa':
                $rules = array_merge($rules, [
                    'cif' => 'required|string|max:50',
                    'address' => 'required|string|max:255',
                    'sector' => 'required|string|max:100',
                    'website' => 'nullable|url|max:255',
                ]);
                break;
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        $details = ['user_id' => $user->id];

        switch ($user->role) {
            case 'alumno':
                $details += [
                    'birth_date' => $request->birthDate,
                    'current_course' => $request->currentCourse,
                    'educational_center' => $request->educationalCenter,
                ];
                break;
            case 'profesor':
                $details += [
                    'birth_date' => $request->birthDate,
                    'specialization' => $request->specialization,
                    'department' => $request->department,
                    'validation_document' => $request->validationDocument,
                ];
                break;
            case 'empresa':
                $details += [
                    'cif' => $request->cif,
                    'address' => $request->address,
                    'sector' => $request->sector,
                    'website' => $request->website,
                ];
                break;
        }

        UserDetail::create($details);
        Auth::login($user);

        $message = match ($user->role) {
            'alumno' => [
                'title' => '¡Bienvenido a SkillUp!',
                'message' => 'Ya puedes explorar ofertas y postularte a proyectos adaptados a tu perfil educativo.',
            ],
            'usuario' => [
                'title' => '¡Bienvenido a SkillUp!',
                'message' => 'Ya puedes explorar ofertas y postularte a proyectos adaptados a tu perfil educativo.',
            ],
            'empresa' => [
                'title' => 'Tu cuenta de empresa está lista',
                'message' => 'Ahora puedes publicar ofertas y recibir candidaturas de estudiantes.',
            ],
            'profesor' => [
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
