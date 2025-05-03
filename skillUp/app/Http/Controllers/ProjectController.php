<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Project;
use App\Models\SchoolProject;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        if ($request->filled('author')) {
            $query->whereHas('author', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->author . '%');
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

        if ($request->filled('name')) {
            $schoolQuery->where('title', 'like', '%' . $request->name . '%');
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

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
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
            'project_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
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

        Notification::create([
            'user_id' => auth()->id(),
            'type' => 'proyecto',
            'title' => 'Proyecto registrado',
            'message' => 'Tu proyecto "' . $project->name . '" ha sido creado correctamente.',
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

        if ($request->hasFile('project_images')) {
            foreach ($request->file('project_images') as $imageFile) {
                $path = $imageFile->store('project_images', 'public');
                $project->images()->create([
                    'path' => $path,
                ]);
            }
        }


        return redirect()->back();
    }

}
