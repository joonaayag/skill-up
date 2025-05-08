<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Project;
use App\Models\SchoolProject;
use App\Models\User;
use Illuminate\Http\Request;
use Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        if ($request->filled('author')) {
            $query->whereHas('author', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->author . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('general_category', $request->category);
        }

        if ($request->filled('order')) {
            $query->orderBy($request->order, 'asc');
        } else {
            $query->latest();
        }

        $projects = $query->get();

        $schoolQuery = SchoolProject::query();

        if ($request->filled('title')) {
            $schoolQuery->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('description')) {
            $schoolQuery->where('description', 'like', '%' . $request->description . '%');
        }

        if ($request->filled('author')) {
            $schoolQuery->where('author', 'like', '%' . $request->author . '%');
        }

        if ($request->filled('category')) {
            $schoolQuery->where('general_category', $request->category);
        }

        if ($request->filled('order')) {
            $schoolQuery->orderBy($request->order, 'asc');
        } else {
            $schoolQuery->latest();
        }

        $schoolProjects = $schoolQuery->get();

        return view('projects.index', compact('projects', 'schoolProjects'));
    }


    public function ownProjects(Request $request)
    {
        $query = auth()->user()->projects();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        if ($request->filled('category')) {
            $query->where('general_category', $request->category);
        }

        if ($request->filled('order')) {
            $query->orderBy($request->order, 'asc');
        } else {
            $query->latest();
        }

        $userProjects = $query->get();

        return view('projects.own_projects', compact('userProjects'));
    }


    public function show($id)
    {
        $project = Project::findOrFail($id);
        $project->increment('views');
        return view('projects.project_details', compact('project'));
    }

    public function store(Request $request)
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

    public function update(Request $request, $id)
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
            
            foreach ($request->file('files') as $file) {
                $path = $file->store('project_images', 'public');
                $project->images()->create([
                    'path' => $path,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Proyecto actualizado correctamente.');
    }


    public function destroy($id)
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

        return redirect()->back()->with('success', 'Proyecto eliminado correctamente.');
    }


}
