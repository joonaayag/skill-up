<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\JobOffer;
use App\Models\Notification;
use App\Models\Project;
use App\Models\SchoolProject;
use App\Models\User;
use App\Models\UserDetail;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Storage;

class AdminController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'Admin' && auth()->user()->role !== 'Profesor') {
            abort(403, 'Acceso denegado');
        }
        return view('admin.dashboard');
    }

    public function showUsers()
    {
        $user = auth()->user();

        if (!in_array($user->role, ['Admin', 'Profesor'])) {
            abort(403, 'Acceso denegado');
        }

        if ($user->role === 'Profesor') {
            $users = User::whereHas('detail', function ($query) use ($user) {
                $query->where('educational_center', $user->detail?->educational_center);
            })->where('id', '!=', $user->id)->with('detail')->get();

            $students = User::where('role', 'Alumno')
                ->whereHas('detail', function ($q) use ($user) {
                    $q->where('educational_center', $user->detail?->educational_center);
                })->where('id', '!=', $user->id)->get();

            return view('admin.users', compact('users', 'students'));

        } else {
            $users = User::where('id', '!=', auth()->id())->with('detail')->get();
        }

        return view('admin.users', compact('users'));
    }


    public function showComments()
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }
        $comments = Comment::all();
        return view('admin.comments', compact('comments'));
    }

    public function destroyUser($id)
    {
        if (!in_array(auth()->user()->role, ['Admin', 'Profesor'])) {
            abort(403, 'Acceso denegado');
        }
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users')->with('message', __('messages.messages.user-destroy'));
    }
    public function updateUser(Request $request, $id)
    {
        if (auth()->user()->role !== 'Admin' && auth()->user()->role !== 'Profesor') {
            return redirect('/dashboard');
        }

        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:20',
            'last_name' => 'required|string|max:40',
            'email' => 'required|email|string|max:50|unique:users,email,' . $user->id,
            'description' => 'nullable|string|max:600',
            'role' => 'required|in:Usuario,Alumno,Profesor,Empresa',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'cv' => 'nullable|file|mimes:pdf|max:2048',
        ];

        switch ($request->role) {
            case 'Alumno':
                $rules += [
                    'birthDate' => 'required|date|before_or_equal:' . date('Y-m-d'),
                    'currentCourse' => 'required|string|max:50',
                    'educationalCenter' => 'required|string|max:100',
                ];
                break;
            case 'Profesor':
                $rules += [
                    'educationalCenter' => 'required|string|max:100',
                    'specialization' => 'required|string|max:100',
                    'department' => 'required|string|max:100',
                ];
                break;
            case 'Empresa':
                $rules += [
                    'cif' => 'required|string|max:50',
                    'address' => 'required|string|max:255',
                    'sector' => 'required|string|max:100',
                    'website' => 'nullable|url|max:255',
                ];
                break;
        }

        $validated = $request->validate($rules);

        // Archivos
        if ($request->hasFile('cv')) {
            if ($user->cv && Storage::disk('s3')->exists($user->cv)) {
                Storage::disk('s3')->delete($user->cv);
            }
            $user->cv = $request->file('cv')->store('cvs', 's3');
        }

        if ($request->hasFile('profile')) {
            $user->profile = $request->file('profile')->store('perfil', 's3');
        }

        if ($request->hasFile('banner')) {
            $user->banner = $request->file('banner')->store('banners', 's3');
        }

        // Datos generales del usuario
        $user->name = ucfirst($validated['name']);
        $user->last_name = ucfirst($validated['last_name']);
        $user->email = $validated['email'];
        $user->description = $validated['description'];
        $user->role = $validated['role'];
        $user->save();

        // Detalles del usuario
        $detail = $user->detail;

        if (!$detail) {
            $detail = new UserDetail();
            $detail->user_id = $user->id;
        }



        switch ($user->role) {
            case 'Alumno':
                $detail->birth_date = $request->birthDate;
                $detail->current_course = ucfirst($request->currentCourse);
                $detail->educational_center = ucfirst($request->educationalCenter);
                break;
            case 'Profesor':
                $detail->educational_center = ucfirst($request->educationalCenter);
                $detail->birth_date = $request->birthDate;
                $detail->specialization = ucfirst($request->specialization);
                $detail->department = ucfirst($request->department);
                break;
            case 'Empresa':
                $detail->cif = strtoupper($request->cif);
                $detail->address = ucfirst($request->address);
                $detail->sector = ucfirst($request->sector);
                $detail->website = $request->website;
                break;
        }

        $detail->save();

        return redirect()->route('admin.users')->with('message', __('messages.messages.user-update'));
    }

    public function importStudents(Request $request)
    {
        // Nombre;Apellido;Correo;Fecha de nacimiento(AAAA-MM-DD);Curso actual
        $user = auth()->user();

        if ($user->role !== 'Profesor') {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'students_file' => 'required|file|mimes:txt|max:2048',
        ]);

        $path = $request->file('students_file')->getRealPath();
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $errors = [];
        $lineNumber = 1;

        foreach ($lines as $line) {
            $parts = explode(';', $line);

            if (count($parts) < 5) {
                $errors[] = __('messages.admin.users.lines') . ' ' . $lineNumber . ": No tiene suficientes campos.";
                $lineNumber++;
                continue;
            }

            [$name, $last_name, $email, $birthDate, $currentCourse] = array_map('trim', $parts);

            $data = [
                'name' => $name,
                'last_name' => $last_name,
                'email' => $email,
                'birthDate' => $birthDate,
                'currentCourse' => $currentCourse,
            ];

            $validator = Validator::make($data, [
                'name' => 'required|string|max:20',
                'last_name' => 'required|string|max:40',
                'email' => 'required|email|string|max:50|unique:users,email',
                'birthDate' => 'required|date|before_or_equal:' . date('Y-m-d'),
                'currentCourse' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                $errors[] = __('messages.admin.users.lines') . ' ' . $lineNumber . ": " . implode(' | ', $validator->errors()->all());
                $lineNumber++;
                continue;
            }

            $newUser = new User();
            $newUser->name = ucfirst($name);
            $newUser->last_name = ucfirst($last_name);
            $newUser->email = $email;
            $newUser->description = null;
            $newUser->role = 'Alumno';
            $newUser->password = bcrypt('Password1@');
            $newUser->save();

            $detail = new UserDetail();
            $detail->user_id = $newUser->id;
            $detail->birth_date = $birthDate;
            $detail->current_course = ucfirst($currentCourse);
            $detail->educational_center = $user->detail->educational_center;
            $detail->save();

            $lineNumber++;
        }

        if (!empty($errors)) {
            return back()->with('message', __('messages.admin.users.import-error'))->with('errors', $errors);
        }

        return back()
            ->with('message', __('messages.admin.users.import-error'))
            ->with('importErrors', $errors);

    }


    public function importTeachers(Request $request)
{
    $user = auth()->user();

    if (!in_array($user->role, ['Administrador', 'Profesor'])) {
        abort(403, 'Acceso denegado');
    }

    $request->validate([
        'teachers_file' => 'required|file|mimes:txt|max:2048',
    ]);

    $path = $request->file('teachers_file')->getRealPath();
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $errors = [];
    $lineNumber = 1;

    foreach ($lines as $line) {
        $parts = explode(';', $line);

        if (count($parts) < 5) {
            $errors[] = __('messages.admin.users.lines') . ' ' . $lineNumber . ": No tiene suficientes campos.";
            $lineNumber++;
            continue;
        }

        [$name, $last_name, $email, $specialization, $department] = array_map('trim', $parts);

        $data = [
            'name' => $name,
            'last_name' => $last_name,
            'email' => $email,
            'specialization' => $specialization,
            'department' => $department,
            'educationalCenter' => $user->detail->educational_center, // asignado automáticamente
        ];

        $rules = [
            'name' => 'required|string|max:20',
            'last_name' => 'required|string|max:40',
            'email' => 'required|email|string|max:50|unique:users,email',
            'specialization' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'educationalCenter' => 'required|string|max:100',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $errors[] = __('messages.admin.users.lines') . ' ' . $lineNumber . ": " . implode(' | ', $validator->errors()->all());
            $lineNumber++;
            continue;
        }

        $newUser = new User();
        $newUser->name = ucfirst($name);
        $newUser->last_name = ucfirst($last_name);
        $newUser->email = $email;
        $newUser->password = bcrypt('Password1@');
        $newUser->role = 'Profesor';
        $newUser->save();

        $detail = new UserDetail();
        $detail->user_id = $newUser->id;
        $detail->educational_center = $data['educationalCenter'];
        $detail->specialization = ucfirst($specialization);
        $detail->department = ucfirst($department);
        $detail->save();

        $lineNumber++;
    }

    if (!empty($errors)) {
        return back()->with('message', __('messages.admin.teachers.import-failed'))->with('errors', $errors);
    }

        return back()
            ->with('message', __('messages.admin.teachers.import-failed'))
            ->with('importErrors', $errors);
    }


    public function resetPasswords(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
        ]);

        $profesor = auth()->user();

        if ($profesor->role !== 'Profesor') {
            abort(403, 'Acceso denegado');
        }

        $center = $profesor->detail->educational_center;

        $newPassword = 'Password1@';

        if ($request->student_id === 'all') {
            $students = User::where('role', 'Alumno')
                ->whereHas('detail', function ($query) use ($center) {
                    $query->where('educational_center', $center);
                })->get();

            foreach ($students as $student) {
                $student->password = bcrypt($newPassword);
                $student->save();
            }

            return back()->with('message', __('messages.admin.users.reset-all'));
        }

        $student = User::where('id', $request->student_id)
            ->where('role', 'Alumno')
            ->whereHas('detail', function ($query) use ($center) {
                $query->where('educational_center', $center);
            })->first();

        if (!$student) {
            return back()->with('message', __('messages.admin.users.student-not'));
        }

        $student->password = bcrypt($newPassword);
        $student->save();

        return back()->with('message', __('messages.admin.users.password-change') . ' ' . $student->name . ' ' . $student->last_name);
    }


    public function updateComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:100',
        ], [
            'content.required' => __('messages.errors.comment.required'),
            'content.string' => __('messages.errors.comment.string'),
            'content.max' => __('messages.errors.comment.max'),
        ]);

        $comment = Comment::findOrFail($id);

        $comment->update([
            'content' => $request->content
        ]);
        return back()->with('message', __('messages.messages.comment-update'));

    }

    public function destroyComment($id)
    {
        $comment = Comment::findOrFail($id);
        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return back()->with('error', __('messages.admin.comments.not-allowed'));
        }

        $comment->delete();

        return back()->with('message', __('messages.messages.comment-delete'));

    }

    public function userRegister(Request $request)
    {
        $user = auth()->user();

        // Solo pueden acceder Admin o Profesor
        if (!$user || !in_array($user->role, ['Admin', 'Profesor'])) {
            return redirect('/dashboard');
        }

        // Si es profesor, solo puede crear alumnos
        if ($user->role === 'Profesor' && $request->role !== 'Alumno') {
            return redirect()->back()->withErrors([
                'role' => __('messages.admin.users.only-students'),
            ])->withInput();
        }

        $rules = [
            'name' => 'required|string|max:20',
            'lastName' => 'nullable|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:Usuario,Alumno,Profesor,Empresa,Admin',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->mixedCase()->letters()->numbers()->symbols(),
            ],
        ];

        switch ($request->role) {
            case 'Alumno':
                if (auth()->user()->role === 'Profesor') {
                    $rules = array_merge($rules, [
                        'birthDate' => 'required|date|before_or_equal:' . date('Y-m-d'),
                        'currentCourse' => 'required|string|max:50',
                    ]);
                } else {
                    $rules = array_merge($rules, [
                        'birthDate' => 'required|date|before_or_equal:' . date('Y-m-d'),
                        'currentCourse' => 'required|string|max:50',
                        'educationalCenter' => 'required|string|max:100',
                    ]);
                }
                break;
            case 'Profesor':
                $rules = array_merge($rules, [
                    'educationalCenter' => 'required|string|max:100',
                    'specialization' => 'required|string|max:100',
                    'department' => 'required|string|max:100',
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

        $request->validate($rules); // puedes incluir tus mensajes personalizados si quieres

        $newUser = User::create([
            'name' => ucfirst($request->name),
            'last_name' => ucfirst($request->lastName),
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'profile' => null,
            'banner' => null,
        ]);

        $details = ['user_id' => $newUser->id];

        switch ($newUser->role) {
            case 'Alumno':
                $details += [
                    'birth_date' => $request->birthDate,
                    'current_course' => ucfirst($request->currentCourse),
                    // Si lo crea un profesor, se impone su centro educativo
                    'educational_center' => $user->role === 'Profesor'
                        ? $user->detail?->educational_center
                        : ucfirst($request->educationalCenter),
                ];
                break;
            case 'Profesor':
                $details += [
                    'educational_center' => ucfirst($request->educationalCenter),
                    'specialization' => ucfirst($request->specialization),
                    'department' => ucfirst($request->department),
                ];
                break;
            case 'Empresa':
                $details += [
                    'cif' => strtoupper($request->cif),
                    'address' => ucfirst($request->address),
                    'sector' => ucfirst($request->sector),
                    'website' => $request->website,
                ];
                break;
        }

        UserDetail::create($details);

        $message = match ($newUser->role) {
            'Alumno' => [
                'title' => 'messages.notifications.message-student.title',
                'message' => 'messages.notifications.message-student.message',
            ],
            'Usuario' => [
                'title' => 'messages.notifications.message-user.title',
                'message' => 'messages.notifications.message-user.message',
            ],
            'Empresa' => [
                'title' => 'messages.notifications.message-company.title',
                'message' => 'messages.notifications.message-company.message',
            ],
            'Profesor' => [
                'title' => 'messages.notifications.message-teacher.title',
                'message' => 'messages.notifications.message-teacher.message',
            ],
            default => null,
        };

        if ($message) {
            Notification::create([
                'user_id' => $newUser->id,
                'type' => 'mensaje',
                'title' => $message['title'],
                'message' => $message['message'],
            ]);
        }

        return redirect()->route('admin.users')->with('message', __('messages.messages.user-create'));
    }


    public function createProject(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:40',
            'description' => 'required|string|max:600',
            'tags' => 'required|in:TFG,TFM,Tesis,Individual,Grupal,Tecnología,Ciencias,Artes,Ingeniería',
            'sector_category' => 'required|in:Administración y negocio,Ciencia y salud,Comunicación,Diseño y comunicación,Educación,Industria,Otro,Tecnología y desarrollo',
            'creation_date' => 'required|date',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'files.*' => 'nullable|file|max:4096',
        ], [
            'title.required' => __('messages.errors.title.required'),
            'title.string' => __('messages.errors.title.string'),
            'title.max' => __('messages.errors.title.max'),

            'description.required' => __('messages.errors.description.required'),
            'description.string' => __('messages.errors.description.string'),
            'description.max' => __('messages.errors.description.max'),

            'tags.required' => __('messages.errors.tags.required'),
            'tags.in' => __('messages.errors.tags.in'),

            'sector_category.required' => __('messages.errors.sector.required'),
            'sector_category.in' => __('messages.errors.sector.in'),

            'creation_date.required' => __('messages.errors.creation_date.required'),
            'creation_date.date' => __('messages.errors.creation_date.date'),

            'link.url' => __('messages.errors.link.url'),
            'link.max' => __('messages.errors.link.max'),

            'image.*.image' => __('messages.errors.image.image'),
            'image.*.mimes' => __('messages.errors.image.mimes'),
            'image.*.max' => __('messages.errors.image.max'),

            'files.*.file' => __('messages.errors.file.file'),
            'files.*.max' => __('messages.errors.file.max'),
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('project_images', 'public')
            : null;


        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags,
            'sector_category' => $request->sector_category,
            'creation_date' => $request->creation_date,
            'link' => $request->link,
            'image' => $imagePath,
            'author_id' => auth()->id(),
        ]);

        Notification::create([
            'user_id' => auth()->id(),
            'type' => 'proyecto',
            'title' => 'messages.notifications.message-create-project.title',
            'message' => 'messages.notifications.message-create-project.message',
            'data' => [
                'project_title' => $project->title,
            ],
        ]);
        $otrosUsuarios = User::where('id', '!=', auth()->id())->get();

        foreach ($otrosUsuarios as $usuario) {
            Notification::firstOrCreate([
                'user_id' => $usuario->id,
                'type' => 'proyecto',
                'title' => 'messages.notifications.message-project-available.title',
                'message' => 'messages.notifications.message-project-available.message',
            ]);
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $imageFile) {
                $path = $imageFile->store('project_images', 's3');
                $project->images()->create([
                    'path' => $path,
                ]);
            }
        }


        return redirect()->back()->with('message', __('messages.messages.project-create'));
    }

    public function createJobOffer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'sector_category' => 'required|in:' . implode(',', [
                'Agricultura/Medio ambiente',
                'Arte/Cultura',
                'Automoción',
                'Ciberseguridad',
                'Community Manager',
                'Construcción',
                'Coordinación Educativa',
                'Diseño Gráfico',
                'Electricidad y fontanería',
                'Energía/Renovables',
                'Farmacia',
                'Finanzas y contabilidad',
                'Fotografía/vídeo',
                'Hostelería/turismo',
                'AI',
                'Investigación/laboratorio',
                'Legal',
                'Logística',
                'Mecánica',
                'Medicina/Enfermería',
                'Nutrición',
                'Operador Industrial',
                'Orientación',
                'Periodismo',
                'Enseñanza',
                'Psicología',
                'Publicidad',
                'Redes y Sistemas',
                'RRHH',
                'Seguridad',
                'SEO/SEM',
                'Terapias/Rehabilitación',
                'Traducción',
                'Transporte/Entrega',
                'Ventas'
            ]),
            'general_category' => 'required|in:Administración y negocio,Ciencia y salud,Comunicación,Diseño y comunicación,Educación,Industria,Otro,Tecnología y desarrollo',
            'state' => 'required|in:abierta,cerrada',
        ], [
            'name.required' => __('messages.errors.name.required'),
            'name.string' => __('messages.errors.name.string'),
            'name.max' => __('messages.errors.name.max'),

            'subtitle.string' => __('messages.errors.subtitle.string'),
            'subtitle.max' => __('messages.errors.subtitle.max'),

            'description.required' => __('messages.errors.description.required'),
            'description.string' => __('messages.errors.description.string'),

            'sector_category.required' => __('messages.errors.sector_offer.required'),
            'sector_category.in' => __('messages.errors.sector_offer.in'),

            'general_category.required' => __('messages.errors.sector.required'),
            'general_category.in' => __('messages.errors.sector.in'),

            'state.required' => __('messages.errors.state.required'),
            'state.in' => __('messages.errors.state.in'),

        ]);

        $jobOffer = JobOffer::create([
            'name' => $request->name,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'sector_category' => $request->sector_category,
            'general_category' => $request->general_category,
            'state' => $request->state,
            'company_id' => auth()->id(),
        ]);

        $usuarios = User::where('id', '!=', auth()->id())->get();

        foreach ($usuarios as $usuario) {
            Notification::create([
                'user_id' => $usuario->id,
                'type' => 'oferta',
                'title' => 'messages.notifications.message-new-job-offer.title',
                'message' => 'messages.notifications.message-new-job-offer.message',
                'data' => [
                    'offer' => $jobOffer->name,
                ],
            ]);
        }


        return back()->with('message', __('messages.messages.job-offer-create'));
    }

    public function createSchoolProject(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:40',
            'author' => 'required|string|max:50',
            'creation_date' => 'required|date',
            'description' => 'required|string',
            'tags' => 'required|in:TFG,TFM,Tesis,Individual,Grupal,Tecnología,Ciencias,Artes,Ingeniería',
            'general_category' => 'required|in:Administración y negocio,Ciencia y salud,Comunicación,Diseño y comunicación,Educación,Industria,Otro,Tecnología y desarrollo',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'files.*' => 'nullable|file|max:4096',
        ], [
            'title.required' => __('messages.errors.title.required'),
            'title.string' => __('messages.errors.title.string'),
            'title.max' => __('messages.errors.title.max'),

            'author.required' => __('messages.errors.author.required'),
            'author.string' => __('messages.errors.author.string'),
            'author.max' => __('messages.errors.author.max'),

            'creation_date.required' => __('messages.errors.creation_date.required'),
            'creation_date.date' => __('messages.errors.creation_date.date'),

            'description.required' => __('messages.errors.description.required'),
            'description.string' => __('messages.errors.description.string'),

            'tags.in' => __('messages.errors.tags.in'),

            'general_category.in' => __('messages.errors.sector.in'),
            'general_category.required' => __('messages.errors.sector.required'),

            'images.*.image' => __('messages.errors.image.image'),
            'images.*.mimes' => __('messages.errors.image.mimes'),
            'images.*.max' => __('messages.errors.image.max'),

            'files.*.file' => __('messages.errors.file.file'),
            'files.*.max' => __('messages.errors.file.max'),
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('project_images', 'public')
            : null;

        $project = SchoolProject::create([
            'title' => $validated['title'],
            'teacher_id' => auth()->id(),
            'author' => $validated['author'],
            'creation_date' => $validated['creation_date'],
            'description' => $validated['description'],
            'tags' => $validated['tags'] ?? null,
            'general_category' => $validated['general_category'] ?? null,
            'image' => $imagePath,
        ]);

        Notification::create([
            'user_id' => auth()->id(),
            'type' => 'proyecto',
            'title' => 'messages.notifications.message-sp-published.title',
            'message' => 'messages.notifications.message-sp-published.message',
            'data' => [
                'project_title' => $project->title,
            ],
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $imageFile) {
                $path = $imageFile->store('project_images', 's3');
                $project->images()->create([
                    'path' => $path,
                ]);
            }
        }

        return back()->with('message', __('messages.messages.sp-create'));
    }

    public function showOffers()
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }
        $offers = JobOffer::all();
        return view('admin.offers', compact('offers'));
    }
    public function updateOffer(Request $request, $id)
    {
        $jobOffer = JobOffer::findOrFail($id);

        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'name' => 'required|string|max:40',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'sector_category' => 'required|in:' . implode(',', [
                'Agricultura/Medio ambiente',
                'Arte/Cultura',
                'Automoción',
                'Ciberseguridad',
                'Community Manager',
                'Construcción',
                'Coordinación Educativa',
                'Diseño Gráfico',
                'Electricidad y fontanería',
                'Energía/Renovables',
                'Farmacia',
                'Finanzas y contabilidad',
                'Fotografía/vídeo',
                'Hostelería/turismo',
                'AI',
                'Investigación/laboratorio',
                'Legal',
                'Logística',
                'Mecánica',
                'Medicina/Enfermería',
                'Nutrición',
                'Operador Industrial',
                'Orientación',
                'Periodismo',
                'Enseñanza',
                'Psicología',
                'Publicidad',
                'Redes y Sistemas',
                'RRHH',
                'Seguridad',
                'SEO/SEM',
                'Terapias/Rehabilitación',
                'Traducción',
                'Transporte/Entrega',
                'Ventas'
            ]),
            'general_category' => 'required|in:Administración y negocio,Ciencia y salud,Comunicación,Diseño y comunicación,Educación,Industria,Otro,Tecnología y desarrollo',
            'state' => 'required|in:abierta,cerrada',
            'logo' => 'nullable|string|max:255',
        ], [
            'name.required' => __('messages.errors.name.required'),
            'name.string' => __('messages.errors.name.string'),
            'name.max' => __('messages.errors.name_offer.max'),

            'subtitle.string' => __('messages.errors.subtitle.string'),
            'subtitle.max' => __('messages.errors.subtitle_offer.max'),

            'description.required' => __('messages.errors.description.required'),
            'description.string' => __('messages.errors.description.string'),

            'sector_category.required' => __('messages.errors.sector_offer.required'),
            'sector_category.string' => __('messages.errors.sector_offer.string'),
            'sector_category.in' => __('messages.errors.sector_offer.in'),

            'general_category.required' => __('messages.errors.sector.required'),
            'general_category.string' => __('messages.errors.sector.string'),
            'general_category.in' => __('messages.errors.sector.in'),

            'state.required' => __('messages.errors.state.required'),
            'state.in' => __('messages.errors.state.in'),

            'logo.string' => 'El logo debe ser una cadena de texto.',
            'logo.max' => 'La URL del logo no puede superar los 255 caracteres.',
        ]);

        $jobOffer->update([
            'name' => $request->name,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'sector_category' => $request->sector_category,
            'general_category' => $request->general_category,
            'state' => $request->state,
            'logo' => $request->logo,
        ]);

        $jobOffer->update($request->all());

        if ($jobOffer->status === 'cerrada') {
            $applications = $jobOffer->applications()->with('user')->get();

            foreach ($applications as $application) {
                Notification::create([
                    'user_id' => $application->user->id,
                    'type' => 'oferta',
                    'title' => 'messages.notifications.message-offer-closed.title',
                    'message' => 'messages.notifications.message-offer-closed.message',
                    'data' => [
                        'offer_name' => $jobOffer->name,
                    ],
                ]);
            }
        }
        $offers = JobOffer::all();
        return view('admin.offers', compact('offers'))->with('message', __('messages.messages.success.offer-updated'));
    }

    public function destroyOffer($id)
    {
        $jobOffer = JobOffer::findOrFail($id);

        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }

        $jobOffer = JobOffer::findOrFail($id);
        $applications = $jobOffer->applications()->with('user')->get();

        foreach ($applications as $application) {
            Notification::create([
                'user_id' => $application->user->id,
                'type' => 'oferta',
                'title' => 'messages.notifications.message-offer-deleted.title',
                'message' => 'messages.notifications.message-offer-deleted.message',
                'data' => [
                    'offer_name' => $jobOffer->name,
                    'deleted_by' => auth()->user()->role === 'Empresa' ? 'la empresa' : 'el profesor',
                ],
            ]);
        }

        $jobOffer->delete();

        $offers = JobOffer::all();
        return view('admin.offers', compact('offers'))->with('message', __('messages.messages.success.offer-deleted'));
    }

    public function showSchoolProjects()
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }
        $schoolProjects = SchoolProject::all();
        return view('admin.school_projects', compact('schoolProjects'));
    }

    public function detailsSchoolProject($id)
    {
        $schoolProject = SchoolProject::with(['images', 'ratings', 'comments'])->findOrFail($id);
        return view('admin.school_project_details', compact('schoolProject'));
    }
    public function updateSchoolProject(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:40',
            'author' => 'required|string|max:50',
            'creation_date' => 'required|date',
            'description' => 'required|string',
            'tags' => 'required|in:TFG,TFM,Tesis,Individual,Grupal,Tecnología,Ciencias,Artes,Ingeniería',
            'general_category' => 'required|in:Administración y negocio,Ciencia y salud,Comunicación,Diseño y comunicación,Educación,Industria,Otro,Tecnología y desarrollo',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'files.*' => 'nullable|file|max:4096',
        ], [
            'title.required' => __('messages.errors.title.required'),
            'title.string' => __('messages.errors.title.string'),
            'title.max' => __('messages.errors.title.max'),

            'author.required' => __('messages.errors.author.required'),
            'author.string' => __('messages.errors.author.string'),
            'author.max' => __('messages.errors.author.max'),

            'creation_date.required' => __('messages.errors.creation_date.required'),
            'creation_date.date' => __('messages.errors.creation_date.date'),

            'description.required' => __('messages.errors.description.required'),
            'description.string' => __('messages.errors.description.string'),

            'tags.in' => __('messages.errors.tags.in'),
            'tags.required' => __('messages.errors.tags.required'),

            'general_category.in' => __('messages.errors.sector.in'),
            'general_category.required' => __('messages.errors.sector.required'),

            'images.*.image' => __('messages.errors.image.image'),
            'images.*.mimes' => __('messages.errors.image.mimes'),
            'images.*.max' => __('messages.errors.image.max'),

            'files.*.file' => __('messages.errors.file.file'),
            'files.*.max' => __('messages.errors.file.max'),
        ]);

        $project = SchoolProject::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($project->image && Storage::disk('s3')->exists($project->image)) {
                Storage::disk('s3')->delete($project->image);
            }

            $imagePath = $request->file('image')->store('project_images', 's3');
            $project->image = $imagePath;
        }

        $project->update([
            'title' => $request->title,
            'author' => $request->author,
            'creation_date' => $request->creation_date,
            'description' => $request->description,
            'tags' => $request->tags,
            'general_category' => $request->general_category,
            'link' => $request->link,
        ]);

        if ($project->teacher_id !== auth()->id()) {
            Notification::create([
                'user_id' => $project->teacher_id,
                'type' => 'proyecto',
                'title' => 'messages.notifications.message-project-updated.title',
                'message' => 'messages.notifications.message-project-updated.message',
                'data' => [
                    'project_title' => $project->title,
                ],
            ]);
        }

        if ($request->hasFile('files')) {

            foreach ($project->images as $img) {
                Storage::disk('s3')->delete($img->path);
                $img->delete();
            }

            foreach ($request->file('files') as $file) {
                $path = $file->store('project_images', 's3');
                $project->images()->create([
                    'path' => $path,
                ]);
            }
        }


        return redirect()->route('admin.school_project.details', $project->id)->with('message', __('messages.messages.sp-update'));
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }
        $project = SchoolProject::findOrFail($id);

        if ($project->user_id) {
            Notification::create([
                'user_id' => $project->user_id,
                'type' => 'proyecto',
                'title' => 'messages.notifications.message-project-deleted.title',
                'message' => 'messages.notifications.message-project-deleted.message',
                'data' => [
                    'project_title' => $project->title,
                ],
            ]);
        }


        $project->delete();

        return redirect()->route('admin.school_projects')->with('message', __('messages.messages.sp-delete'));
    }

    public function ProjectsShow()
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }
        $projects = Project::all();
        return view('admin.projects', compact('projects'));
    }
    public function detailsProject($id)
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }
        $project = Project::with(['author', 'images', 'ratings', 'comments'])->findOrFail($id);
        return view('admin.project_details', compact('project'));
    }
    public function updateProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:40',
            'description' => 'required|string|max:600',
            'tags' => 'required|in:TFG,TFM,Tesis,Individual,Grupal,Tecnología,Ciencias,Artes,Ingeniería',
            'sector_category' => 'required|in:Administración y negocio,Ciencia y salud,Comunicación,Diseño y comunicación,Educación,Industria,Otro,Tecnología y desarrollo',
            'creation_date' => 'required|date',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'files.*' => 'nullable|file|max:4096',
        ], [
            'title.required' => __('messages.errors.title.required'),
            'title.string' => __('messages.errors.title.string'),
            'title.max' => __('messages.errors.title.max'),

            'description.required' => __('messages.errors.description.required'),
            'description.string' => __('messages.errors.description.string'),
            'description.max' => __('messages.errors.description.max'),

            'tags.required' => __('messages.errors.tags.required'),
            'tags.in' => __('messages.errors.tags.in'),

            'sector_category.required' => __('messages.errors.sector.required'),
            'sector_category.in' => __('messages.errors.sector.in'),

            'creation_date.required' => __('messages.errors.creation_date.required'),
            'creation_date.date' => __('messages.errors.creation_date.date'),

            'link.url' => __('messages.errors.link.url'),
            'link.max' => __('messages.errors.link.max'),

            'image.image' => __('messages.errors.image.image'),
            'image.mimes' => __('messages.errors.image.mimes'),
            'image.max' => __('messages.errors.image.max'),

            'files.*.file' => __('messages.errors.file.file'),
            'files.*.max' => __('messages.errors.file.max'),
        ]);

        if ($request->hasFile('image')) {
            if ($project->image && Storage::disk('s3')->exists($project->image)) {
                Storage::disk('s3')->delete($project->image);
            }

            $imagePath = $request->file('image')->store('project_images', 's3');
            $project->image = $imagePath;
        }

        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags,
            'sector_category' => $request->sector_category,
            'creation_date' => $request->creation_date,
            'link' => $request->link,
        ]);

        if ($request->hasFile('files')) {
            foreach ($project->images as $image) {
                if (Storage::disk('s3')->exists($image->path)) {
                    Storage::disk('s3')->delete($image->path);
                }
                $image->delete();
            }

            foreach ($request->file('files') as $file) {
                $path = $file->store('project_images', 's3');
                $project->images()->create([
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.project.details', $project->id)->with('message', __('messages.messages.project-update'));
    }
    public function destroyProject($id)
    {
        $project = Project::findOrFail($id);

        if ($project->image) {
            $path = $project->image;
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        }

        foreach ($project->images as $image) {
            $path = $image->path;
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
            $image->delete();
        }

        $project->delete();
        $projects = Project::all();
        return view('admin.projects', compact('projects'))->with('message', __('messages.messages.project-delete'));
    }

    public function destroySchoolProject($id)
    {
        $project = SchoolProject::findOrFail($id);

        if ($project->image) {
            $path = $project->image;
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        }

        foreach ($project->images as $image) {
            $path = $image->path;
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
            $image->delete();
        }

        $project->delete();
        $projects = SchoolProject::all();
        return view('admin.projects', compact('projects'))->with('message', __('messages.messages.project-delete'));
    }

}
