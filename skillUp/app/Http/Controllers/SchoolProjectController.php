<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\SchoolProject;
use Illuminate\Http\Request;

class SchoolProjectController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'profesor') {
            abort(403, 'Acceso denegado');
        }
        $projects = SchoolProject::all();
        return view('school_projects.index', compact('projects'));
    }
    public function destroy($id)
    {
        if (auth()->user()->role !== 'profesor') {
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
        ]);

        $project = SchoolProject::findOrFail($id);

        $project->update([
            'title' => $request->title,
            'author' => $request->author,
            'creation_date' => $request->creation_date,
            'description' => $request->description,
            'tags' => $request->tags,
            'user_id' => auth()->id(),
            'general_category' => $request->general_category,
        ]);

        if ($project->user_id !== auth()->id()) {
            Notification::create([
                'user_id' => $project->user_id,
                'type' => 'proyecto',
                'title' => 'Tu proyecto ha sido actualizado',
                'message' => 'El proyecto "' . $project->title . '" ha sido actualizado por el profesorado.',
            ]);
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
        ]);

        $project = new SchoolProject();
        $project->title = $validated['title'];
        $project->user_id = auth()->id();
        $project->author = $validated['author'];
        $project->creation_date = $validated['creation_date'];
        $project->description = $validated['description'];
        $project->tags = $validated['tags'] ?? null;
        $project->general_category = $validated['general_category'] ?? null;
        $project->save();

        Notification::create([
            'user_id' => auth()->id(),
            'type' => 'proyecto',
            'title' => 'Proyecto escolar publicado',
            'message' => 'Tu proyecto "' . $project->title . '" ha sido registrado correctamente.',
        ]);


        return redirect()->route('school.projects.index');
    }

}
