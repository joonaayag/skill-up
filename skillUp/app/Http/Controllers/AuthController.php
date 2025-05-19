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
        if (auth()->check()) {
            return redirect('/dashboard');
        }
        return view('auth');
    }

    public function login(Request $request)
    {
        if (auth()->check()) {
            return redirect('/dashboard');
        }

        $remember = $request->has('remember');
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials, $remember)) {
            return redirect('/dashboard')->with('message', __('messages.messages.login'));
        }

        return back()->withErrors(['email' => __('messages.errors.wrong-credentials')])->withInput();
    }

    public function register(Request $request)
    {
        if (auth()->check()) {
            return redirect('/dashboard');
        }
        $rules = [
            'name' => 'required|string|max:20',
            'lastName' => 'nullable|string|max:40',
            'email' => 'required|string|email|max:50|unique:users',
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
                    'birthDate' => 'required|date|before_or_equal:' . date('Y-m-d'),
                    'currentCourse' => 'required|string|max:50',
                    'educationalCenter' => 'required|string|max:100',
                ]);
                break;
            case 'Profesor':
                $rules = array_merge($rules, [
                    'birthDate' => 'required|date|before_or_equal:' . date('Y-m-d'),
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
            'name.required' => __('messages.errors.name.required'),
            'name.string' => __('messages.errors.name.string'),
            'name.max' => __('messages.errors.name.max'),

            'lastName.required' => __('messages.errors.last_name.required'),
            'lastName.string' => __('messages.errors.last_name.string'),
            'lastName.max' => __('messages.errors.last_name.max'),

            'email.required' => __('messages.errors.email.required'),
            'email.string' => __('messages.errors.email.string'),
            'email.email' => __('messages.errors.email.email'),
            'email.max' => __('messages.errors.email.max'),
            'email.unique' => __('messages.errors.email.unique'),

            'password.required' => __('messages.errors.password.required'),
            'password.confirmed' => __('messages.errors.password.confirmed'),
            'password.min' => __('messages.errors.password.min'),
            'password.string' => __('messages.errors.password.string'),
            'password.*' => __('messages.errors.password.regex'),

            'role.required' => __('messages.errors.role.required'),
            'role.in' => __('messages.errors.role.in'),

            'g-recaptcha-response.required' => __('messages.errors.recaptcha.required'),

            'birthDate.required' => __('messages.errors.birth_date.required'),
            'birthDate.date' => __('messages.errors.birth_date.date'),
            'birthDate.before_or_equal' => __('messages.errors.birth_date.before_or_equal'),

            'currentCourse.required' => __('messages.errors.current_course.required'),
            'currentCourse.string' => __('messages.errors.current_course.string'),
            'currentCourse.max' => __('messages.errors.current_course.max'),

            'educationalCenter.required' => __('messages.errors.educational_center.required'),
            'educationalCenter.string' => __('messages.errors.educational_center.string'),
            'educationalCenter.max' => __('messages.errors.educational_center.max'),

            'specialization.required' => __('messages.errors.specialization.required'),
            'specialization.string' => __('messages.errors.specialization.string'),
            'specialization.max' => __('messages.errors.specialization.max'),

            'department.required' => __('messages.errors.department.required'),
            'department.string' => __('messages.errors.department.string'),
            'department.max' => __('messages.errors.department.max'),

            'validationDocument.required' => __('messages.errors.validation_document.required'),
            'validationDocument.string' => __('messages.errors.validation_document.string'),
            'validationDocument.max' => __('messages.errors.validation_document.max'),

            'cif.required' => __('messages.errors.cif.required'),
            'cif.string' => __('messages.errors.cif.string'),
            'cif.max' => __('messages.errors.cif.max'),

            'address.required' => __('messages.errors.address.required'),
            'address.string' => __('messages.errors.address.string'),
            'address.max' => __('messages.errors.address.max'),

            'sector.required' => __('messages.errors.sector.required'),
            'sector.string' => __('messages.errors.sector.string'),
            'sector.max' => __('messages.errors.sector.max'),

            'website.url' => __('messages.errors.website.url'),
            'website.max' => __('messages.errors.website.max'),
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
                'title' => __('messages.notifications.message-student.title'),
                'message' => __('messages.notifications.message-student.message'),
            ],
            'Usuario' => [
                'title' => __('messages.notifications.message-user.title'),
                'message' => __('messages.notifications.message-user.message'),
            ],
            'Empresa' => [
                'title' => __('messages.notifications.message-company.title'),
                'message' => __('messages.notifications.message-company.message'),
            ],
            'Profesor' => [
                'title' => __('messages.notifications.message-teacher.title'),
                'message' => __('messages.notifications.message-teacher.message'),
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

        return redirect('/dashboard')->with('message', __('messages.messages.register'));
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
