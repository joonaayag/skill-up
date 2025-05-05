<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\Project;
use App\Models\SchoolProject;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Notification;
use Storage;

class AdminController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Acceso denegado');
        }
        return view('admin.dashboard');
    }

    public function showUsers()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Acceso denegado');
        }
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function destroyUser($id)
    {
        if (auth()->user()->role !== 'admin') {
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
            'foto_perfil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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

            'foto_perfil.image' => 'La foto de perfil debe ser una imagen.',
            'foto_perfil.mimes' => 'La foto de perfil debe ser un archivo JPG, JPEG o PNG.',
            'foto_perfil.max' => 'La foto de perfil no puede superar los 2MB.',

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


        if ($request->hasFile('foto_perfil')) {
            $path = $request->file('foto_perfil')->store('perfil', 'public');
            $user->foto_perfil = $path;
        }

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('banners', 'public');
            $user->banner = $path;
        }

        $user->save();

        $detail = $user->detail ?? new UserDetail(['user_id' => $user->id]);


        if ($user->role === 'alumno') {
            $detail->birth_date = $request->birth_date;
            $detail->current_course = $request->current_course;
            $detail->educational_center = $request->educational_center;
        }

        if ($user->role === 'profesor') {
            $detail->specialization = $request->specialization;
            $detail->department = $request->department;
        }

        if ($user->role === 'empresa') {
            $detail->cif = $request->cif;
            $detail->address = $request->address;
            $detail->sector = $request->sector;
            $detail->website = $request->website;
        }

        $detail->save();

        return redirect()->route('admin.users');
    }

    public function showOffers()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Acceso denegado');
        }
        $offers = JobOffer::all();
        return view('admin.offers', compact('offers'));
    }
    public function updateOffer(Request $request, $id)
    {
        $jobOffer = JobOffer::findOrFail($id);

        if (auth()->user()->role !== 'admin') {
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

        if (auth()->user()->role !== 'admin') {
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

    public function schoolProjectsShow()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Acceso denegado');
        }
        $schoolProjects = SchoolProject::all();
        return view('admin.school_projects', compact('schoolProjects'));
    }
    public function ProjectsShow()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Acceso denegado');
        }
        $projects = Project::all();
        return view('admin.projects', compact('projects'));
    }

}
