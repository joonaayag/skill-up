<?php

namespace App\Http\Controllers;

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

    public function destroyUser($id)
    {
        if (auth()->user()->role !== 'Admin') {
            abort(403, 'Acceso denegado');
        }
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users');
    }
    public function updateUser(Request $request, $id)
    {

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:20',
            'last_name' => 'required|string|max:40',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'description' => 'nullable|string|max:300',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'cv' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 20 caracteres.',

            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.max' => 'El apellido no puede tener más de 40 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            'description.max' => 'La descripción no puede superar los 300 caracteres.',

            'profile.image' => 'La foto de perfil debe ser una imagen.',
            'profile.mimes' => 'La foto de perfil debe ser un archivo JPG, JPEG o PNG.',
            'profile.max' => 'La foto de perfil no puede superar los 2MB.',

            'banner.image' => 'El banner debe ser una imagen.',
            'banner.mimes' => 'El banner debe ser un archivo JPG, JPEG o PNG.',
            'banner.max' => 'El banner no puede superar los 4MB.',

            'cv.file' => 'El currículum debe ser un archivo.',
            'cv.mimes' => 'El currículum debe estar en formato PDF.',
            'cv.max' => 'El currículum no puede superar los 2MB.',
        ]);

        if ($request->hasFile('cv')) {
            if ($request->cv && Storage::disk('public')->exists($request->cv)) {
                Storage::disk('public')->delete($request->cv);
            }

            $cvPath = $request->file('cv')->store('cvs', 'public');
            $user->cv = $cvPath;
        }

        $user->name = $validated['name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->description = $validated['description'];


        if ($request->hasFile('profile')) {
            $path = $request->file('profile')->store('perfil', 'public');
            $user->profile = $path;
        }

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('banners', 'public');
            $user->banner = $path;
        }

        $user->save();

        $detail = $user->detail ?? new UserDetail(['user_id' => $user->id]);


        if ($user->role === 'Alumno') {
            $detail->birth_date = $request->birth_date;
            $detail->current_course = $request->current_course;
            $detail->educational_center = $request->educational_center;
        }

        if ($user->role === 'Profesor') {
            $detail->specialization = $request->specialization;
            $detail->department = $request->department;
        }

        if ($user->role === 'Empresa') {
            $detail->cif = $request->cif;
            $detail->address = $request->address;
            $detail->sector = $request->sector;
            $detail->website = $request->website;
        }

        $detail->save();

        return redirect()->route('admin.users');
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

        return redirect()->route('admin.users');
    }

    public function createProject(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:40',
            'description' => 'required|string',
            'tags' => 'required|string|max:50',
            'sector_category' => 'required|string|max:40',
            'creation_date' => 'required|date',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'files.*' => 'nullable|file|max:4096',
        ], [
            'title.required' => 'El título del proyecto es obligatorio.',
            'title.string' => 'El título del proyecto debe ser una cadena de texto.',
            'title.max' => 'El título del proyecto no puede tener más de 40 caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',

            'tags.required' => 'Las etiquetas son obligatorias.',
            'tags.string' => 'Las etiquetas deben ser una cadena de texto.',
            'tags.max' => 'Las etiquetas no pueden superar los 50 caracteres.',

            'sector_category.required' => 'La categoría del sector es obligatoria.',
            'sector_category.string' => 'La categoría del sector debe ser una cadena de texto.',
            'sector_category.max' => 'La categoría del sector no puede tener más de 40 caracteres.',

            'creation_date.required' => 'La fecha de creación es obligatoria.',
            'creation_date.date' => 'La fecha de creación debe ser válida.',

            'link.url' => 'El enlace debe tener un formato de URL válido.',
            'link.max' => 'El enlace no puede tener más de 255 caracteres.',

            'image.*.image' => 'Cada imagen debe ser un archivo de imagen válido.',
            'image.*.mimes' => 'Las imágenes deben ser en formato jpeg, png, jpg o gif.',
            'image.*.max' => 'Cada imagen no puede superar los 4MB.',

            'files.*.file' => 'Cada archivo debe ser un archivo válido.',
            'files.*.max' => 'Cada archivo no puede superar los 4MB.',
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
            'title' => 'Proyecto registrado',
            'message' => 'Tu proyecto "' . $project->title . '" ha sido creado correctamente.',
        ]);
        $otrosUsuarios = User::where('id', '!=', auth()->id())->get();

        foreach ($otrosUsuarios as $usuario) {
            Notification::create([
                'user_id' => $usuario->id,
                'type' => 'proyecto',
                'title' => 'Nuevos proyectos disponible',
                'message' => 'Se han publicado nuevos proyectos recientemente, ve a descubrirlos! ',
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


        return redirect()->back();
    }

    public function createJobOffer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'sector_category' => 'required|string',
            'general_category' => 'required|string',
            'state' => 'required|in:abierta,cerrada',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 40 caracteres.',

            'subtitle.string' => 'El subtítulo debe ser una cadena de texto.',
            'subtitle.max' => 'El subtítulo no puede tener más de 255 caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',

            'sector_category.required' => 'La categoría del sector es obligatoria.',
            'sector_category.string' => 'La categoría del sector debe ser una cadena de texto.',

            'general_category.required' => 'La categoría general es obligatoria.',
            'general_category.string' => 'La categoría general debe ser una cadena de texto.',

            'state.required' => 'El estado es obligatorio.',
            'state.in' => 'El estado debe ser "abierta" o "cerrada".',

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
                'title' => '¡Nueva oferta disponible!',
                'message' => 'Se ha publicado una nueva oferta: "' . $jobOffer->name . '".',
            ]);
        }


        return back();
    }

    public function createSchoolProject(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:40',
            'author' => 'required|string|max:50',
            'creation_date' => 'required|date',
            'description' => 'required|string',
            'tags' => 'required|in:TFG,TFM,Tesis,Individual,Grupal,Tecnología,Ciencias,Artes,Ingeniería',
            'general_category' => 'nullable|string|max:40',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'files.*' => 'nullable|file|max:4096',
        ], [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no puede tener más de 40 caracteres.',

            'author.required' => 'El autor es obligatorio.',
            'author.string' => 'El autor debe ser una cadena de texto.',
            'author.max' => 'El autor no puede tener más de 50 caracteres.',

            'creation_date.required' => 'La fecha de creación es obligatoria.',
            'creation_date.date' => 'La fecha de creación debe ser una fecha válida.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',

            'tags.string' => 'Las etiquetas deben ser una cadena de texto.',
            'tags.max' => 'Las etiquetas no pueden tener más de 50 caracteres.',

            'general_category.string' => 'La categoría general debe ser una cadena de texto.',
            'general_category.max' => 'La categoría general no puede tener más de 40 caracteres.',

            'images.*.image' => 'Cada imagen debe ser un archivo de imagen válido.',
            'images.*.mimes' => 'Las imágenes deben ser en formato jpeg, png, jpg o gif.',
            'images.*.max' => 'Cada imagen no puede superar los 4MB.',

            'files.*.file' => 'Cada archivo debe ser un archivo válido.',
            'files.*.max' => 'Cada archivo no puede superar los 4MB.',
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
            'title' => 'Proyecto escolar publicado',
            'message' => 'Tu proyecto "' . $project->title . '" ha sido registrado correctamente.',
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $imageFile) {
                $path = $imageFile->store('project_images', 'public');
                $project->images()->create([
                    'path' => $path,
                ]);
            }
        }

        return back();
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
            'sector_category' => 'required|string',
            'general_category' => 'required|string',
            'state' => 'required|in:abierta,cerrada',
            'logo' => 'nullable|string|max:255',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 40 caracteres.',

            'subtitle.string' => 'El subtítulo debe ser una cadena de texto.',
            'subtitle.max' => 'El subtítulo no puede tener más de 255 caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',

            'sector_category.required' => 'La categoría del sector es obligatoria.',
            'sector_category.string' => 'La categoría del sector debe ser una cadena de texto.',

            'general_category.required' => 'La categoría general es obligatoria.',
            'general_category.string' => 'La categoría general debe ser una cadena de texto.',

            'state.required' => 'El estado es obligatorio.',
            'state.in' => 'El estado debe ser "abierta" o "cerrada".',

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
                    'title' => 'Oferta cerrada',
                    'message' => 'La oferta "' . $jobOffer->name . '" ha sido cerrada por el administrador.',
                ]);
            }
        }
        $offers = JobOffer::all();
        return view('admin.offers', compact('offers'));
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
        return view('admin.offers', compact('offers'));
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
            'tags' => 'nullable|string|max:50',
            'general_category' => 'nullable|string|max:40',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'files.*' => 'nullable|file|max:4096',
        ], [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no puede tener más de 40 caracteres.',

            'author.required' => 'El autor es obligatorio.',
            'author.string' => 'El autor debe ser una cadena de texto.',
            'author.max' => 'El autor no puede tener más de 50 caracteres.',

            'creation_date.required' => 'La fecha de creación es obligatoria.',
            'creation_date.date' => 'La fecha de creación debe ser una fecha válida.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',

            'tags.string' => 'Las etiquetas deben ser una cadena de texto.',
            'tags.max' => 'Las etiquetas no pueden tener más de 50 caracteres.',

            'general_category.string' => 'La categoría general debe ser una cadena de texto.',
            'general_category.max' => 'La categoría general no puede tener más de 40 caracteres.',

            'link.url' => 'El enlace debe tener un formato de URL válido.',
            'link.max' => 'El enlace no puede tener más de 255 caracteres.',

            'image.image' => 'Cada imagen debe ser un archivo de imagen válido.',
            'image.mimes' => 'Las imágenes deben ser en formato jpeg, png, jpg o gif.',
            'image.max' => 'Cada imagen no puede superar los 4MB.',

            'files.*.file' => 'Cada archivo debe ser un archivo válido.',
            'files.*.max' => 'Cada archivo no puede superar los 4MB.',
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
                'title' => 'Tu proyecto ha sido actualizado',
                'message' => 'El proyecto "' . $project->title . '" ha sido actualizado por el administrador.',
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


        return redirect()->route('admin.school_project.details', $project->id);
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
                'title' => 'Tu proyecto ha sido eliminado',
                'message' => 'El proyecto "' . $project->title . '" ha sido eliminado por el administrador.',
            ]);
        }


        $project->delete();

        return redirect()->route('admin.school_projects');
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
            'description' => 'required|string',
            'tags' => 'required|string|max:50',
            'sector_category' => 'required|string|max:40',
            'creation_date' => 'required|date',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'files.*' => 'nullable|file|max:4096',
        ], [
            'title.required' => 'El nombre del proyecto es obligatorio.',
            'title.string' => 'El nombre del proyecto debe ser una cadena de texto.',
            'title.max' => 'El nombre del proyecto no puede tener más de 40 caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',

            'tags.required' => 'Las etiquetas son obligatorias.',
            'tags.string' => 'Las etiquetas deben ser una cadena de texto.',
            'tags.max' => 'Las etiquetas no pueden superar los 50 caracteres.',

            'sector_category.required' => 'La categoría del sector es obligatoria.',
            'sector_category.string' => 'La categoría del sector debe ser una cadena de texto.',
            'sector_category.max' => 'La categoría del sector no puede tener más de 40 caracteres.',

            'creation_date.required' => 'La fecha de creación es obligatoria.',
            'creation_date.date' => 'La fecha de creación debe ser válida.',

            'link.url' => 'El enlace debe tener un formato de URL válido.',
            'link.max' => 'El enlace no puede tener más de 255 caracteres.',

            'image.*.image' => 'Cada imagen debe ser un archivo de imagen válido.',
            'image.*.mimes' => 'Las imágenes deben ser en formato jpeg, png, jpg o gif.',
            'image.*.max' => 'Cada imagen no puede superar los 4MB.',

            'files.*.file' => 'Cada archivo debe ser un archivo válido.',
            'files.*.max' => 'Cada archivo no puede superar los 4MB.',
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

        return redirect()->route('admin.project.details', $project->id);
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
        return view('admin.projects', compact('projects'));
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
        return view('admin.projects', compact('projects'));
    }

}
