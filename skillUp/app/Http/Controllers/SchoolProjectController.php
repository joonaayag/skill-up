<?php

namespace App\Http\Controllers;

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
        $project->delete();

        return redirect()->route('school.projects.index');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'creation_date' => 'required|date',
            'description' => 'required|string',
            'tags' => 'nullable|string|max:255',
            'general_category' => 'nullable|string|max:255',
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

        return redirect()->route('school.projects.index');
    }

    public function show($id)
    {
        $schoolProject = SchoolProject::findOrFail($id);
        return view('school_projects.show', compact('schoolProject'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'creation_date' => 'required|date',
            'description' => 'required|string',
            'tags' => 'nullable|string|max:255',
            'general_category' => 'nullable|string|max:255',
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

        return redirect()->route('school.projects.index');
    }

}
