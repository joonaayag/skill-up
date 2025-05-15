<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\SchoolProject;
use Illuminate\Http\Request;
use Storage;

class SchoolProjectController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'Profesor') {
            abort(403, 'Acceso denegado');
        }

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

        if ($request->filled('tags')) {
            $categories = $request->input('tags');

            if (is_array($categories)) {
                $schoolQuery->whereIn('tags', $categories);
            } else {
                $schoolQuery->where('tags', $categories);
            }
        }

        if ($request->filled('academic_year')) {
            $years = $request->input('academic_year');

            $schoolQuery->where(function ($query) use ($years) {
                foreach ($years as $yearRange) {
                    [$startYear, $endYear] = explode('-', $yearRange);
                    $startDate = "$startYear-09-01";
                    $endDate = "$endYear-06-30";

                    $query->orWhereBetween('creation_date', [$startDate, $endDate]);
                }
            });
        }

        if ($request->filled('order')) {
            $direction = $request->input('direction', 'asc');
            $orderField = $request->input('order');

            $allowedFields = ['title', 'created_at', 'author', 'creation_date'];
            if (in_array($orderField, $allowedFields)) {
                $schoolQuery->orderBy($orderField, $direction);
            }
        } else {
            $schoolQuery->latest();
        }

        $projects = $schoolQuery->get();

        return view('school_projects.index', compact('projects'));
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'Profesor') {
            abort(403, 'Acceso denegado');
        }
        $project = SchoolProject::findOrFail($id);

        if ($project->user_id) {
            Notification::create([
                'user_id' => $project->user_id,
                'type' => 'proyecto',
                'title' => 'Tu proyecto ha sido eliminado',
                'message' => 'El proyecto "' . $project->title . '" ha sido eliminado por el profesorado.',
            ]);
        }


        $project->delete();

        return redirect()->route('school.projects.index');
    }
    public function update(Request $request, $id)
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
            'teacher_id' => auth()->id(),
            'general_category' => $request->general_category,
            'link' => $request->link,
        ]);

        if ($project->teacher_id !== auth()->id()) {
            Notification::create([
                'author_id' => $project->teacher_id,
                'type' => 'proyecto',
                'title' => 'Tu proyecto ha sido actualizado',
                'message' => 'El proyecto "' . $project->title . '" ha sido actualizado por el profesorado.',
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


        return redirect()->route('school.projects.index');
    }

    public function show($id)
    {
        $schoolProject = SchoolProject::findOrFail($id);
        $schoolProject->increment('views');
        return view('school_projects.show', compact('schoolProject'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:40',
            'author' => 'required|string|max:50',
            'creation_date' => 'required|date',
            'description' => 'required|string',
            'tags' => 'nullable|string|max:50',
            'general_category' => 'nullable|string|max:40',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
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

        $project = new SchoolProject();
        $project->title = $validated['title'];
        $project->teacher_id = auth()->id();
        $project->author = $validated['author'];
        $project->creation_date = $validated['creation_date'];
        $project->description = $validated['description'];
        $project->tags = $validated['tags'] ?? null;
        $project->general_category = $validated['general_category'] ?? null;
        $project->image = $imagePath;
        $project->save();

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

        return redirect()->route('school.projects.index');
    }

}
