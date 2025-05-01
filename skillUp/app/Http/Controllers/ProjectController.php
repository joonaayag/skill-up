<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SchoolProject;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->take(9)->get();
        $schoolProjects = SchoolProject::latest()->take(9)->get();

        return view('projects.index', compact('projects', 'schoolProjects'));
    }

    public function ownProjects()
    {
        $userProjects = auth()->user()->projects()->latest()->get();

        return view('projects.own_projects', compact('userProjects'));
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return view('projects.project_details', compact('project'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'required|string|max:255',
            'sector_category' => 'required|string|max:255',
            'creation_date' => 'required|date',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'files.*' => 'nullable|file|max:4096',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('project_images', 'public')
            : null;


        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'tags' => $request->tags,
            'sector_category' => $request->sector_category,
            'creation_date' => $request->creation_date,
            'link' => $request->link,
            'image' => $imagePath,
            'author_id' => auth()->id(),
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('project_files', 'public');

                $project->files()->create([
                    'path' => $path
                ]);
            }
        }

        return redirect()->route('projects.index');
    }

}
