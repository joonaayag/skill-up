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
        return view('school_projects_index', compact('projects'));
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
            'general_category' => $request->general_category,
        ]);

        return redirect()->route('school.projects.index');
    }
}
