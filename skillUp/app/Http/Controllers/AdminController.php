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
use Illuminate\Validation\Rules\Password;
use Storage;

class AdminController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }
        return view('admin.dashboard');
    }

    public function showUsers()
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }
        $users = User::all();
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
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users')->with('message', __('messages.messages.user-destroy'));
    }
    public function updateUser(Request $request, $id)
    {
        if (auth()->user()->role !== 'Admin') {
            return redirect('/dashboard');
        }

        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:20',
            'last_name' => 'required|string|max:40',
            'email' => 'required|email|string|max:50|unique:users,email,' . $user->id,
            'description' => 'nullable|string|max:300',
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
            if ($user->cv && Storage::disk('public')->exists($user->cv)) {
                Storage::disk('public')->delete($user->cv);
            }
            $user->cv = $request->file('cv')->store('cvs', 'public');
        }

        if ($request->hasFile('profile')) {
            $user->profile = $request->file('profile')->store('perfil', 'public');
        }

        if ($request->hasFile('banner')) {
            $user->banner = $request->file('banner')->store('banners', 'public');
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
            return back()->with('error', 'No tienes permiso para eliminar este comentario.');
        }

        $comment->delete();

        return back()->with('message', __('messages.messages.comment-delete'));

    }

    public function userRegister(Request $request)
    {
        if (auth()->user()->role !== 'Admin') {
            return redirect('/dashboard');
        }
        $rules = [
            'name' => 'required|string|max:20',
            'lastName' => 'nullable|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:Usuario,Alumno,Profesor,Empresa',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
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
                    'educational_center' => ucfirst($request->educationalCenter),
                    'specialization' => ucfirst($request->specialization),
                    'department' => ucfirst($request->department),
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

        return redirect()->route('admin.users')->with('message', __('messages.messages.user-create'));
    }

    public function createProject(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:40',
            'description' => 'required|string|max:300',
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

        Notification::firstOrCreate([
            'user_id' => auth()->id(),
            'type' => 'proyecto',
            'title' => __('messages.notifications.message-create-project.title'),
            'message' => __('messages.notifications.message-create-project.message-1') . $project->title . __('messages.notifications.message-create-project.message-2'),
        ]);
        $otrosUsuarios = User::where('id', '!=', auth()->id())->get();

        foreach ($otrosUsuarios as $usuario) {
            Notification::firstOrCreate([
                'user_id' => $usuario->id,
                'type' => 'proyecto',
                'title' => __('messages.notifications.message-project-available.title'),
                'message' => __('messages.notifications.message-project-available.message'),
            ]);
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $imageFile) {
                $path = $imageFile->store('project_images', 'public');
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
                'title' => __('messages.notifications.message-new-job-offer.title'),
                'message' => __('messages.notifications.message-new-job-offer.message') . $jobOffer->name . '".',
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
            'title' => __('messages.notifications.message-sp-published.title'),
            'message' => __('messages.notifications.message-sp-published.message-1') . $project->title . __('messages.notifications.message-sp-published.message-2'),
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $imageFile) {
                $path = $imageFile->store('project_images', 'public');
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
                    'title' => __('messages.notifications.message-offer-closed.title'),
                    'message' => __('messages.notifications.message-offer-closed.message-1') . $jobOffer->name . __('messages.notifications.message-offer-closed.message-2'),
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
                'title' => 'Oferta retirada',
                'message' => 'La oferta "' . $jobOffer->name . '" a la que te postulaste ha sido eliminada por el administador.',
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
            if ($project->image && Storage::disk('public')->exists($project->image)) {
                Storage::disk('public')->delete($project->image);
            }

            $imagePath = $request->file('image')->store('project_images', 'public');
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
                'title' => __('messages.notifications.message-project-updated.title'),
                'message' => __('messages.notifications.message-project-updated.message-1') . $project->title . __('messages.notifications.message-project-updated.message-2'),
            ]);
        }

        if ($request->hasFile('files')) {

            foreach ($project->images as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }

            foreach ($request->file('files') as $file) {
                $path = $file->store('project_images', 'public');
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
                'title' => __('messages.notifications.message-project-deleted.title'),
                'message' => __('messages.notifications.message-project-deleted.message-1') . $project->title . __('messages.notifications.message-project-deleted.message-2'),
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
            'description' => 'required|string|max:300',
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

        if ($request->hasFile('image')) {
            if ($project->image && Storage::disk('public')->exists($project->image)) {
                Storage::disk('public')->delete($project->image);
            }

            $imagePath = $request->file('image')->store('project_images', 'public');
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
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                $image->delete();
            }

            foreach ($request->file('files') as $file) {
                $path = $file->store('project_images', 'public');
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
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        foreach ($project->images as $image) {
            $path = $image->path;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
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
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        foreach ($project->images as $image) {
            $path = $image->path;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $image->delete();
        }

        $project->delete();
        $projects = SchoolProject::all();
        return view('admin.projects', compact('projects'))->with('message', __('messages.messages.project-delete'));
    }

}
