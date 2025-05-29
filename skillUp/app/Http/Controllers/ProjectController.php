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

        if ($request->filled('author')) {
            $query->whereHas('author', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->author . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('general_category', $request->category);
        }

        if ($request->filled('order')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->order, $direction);
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
            $direction = $request->input('direction', 'asc');
            $schoolQuery->orderBy($request->order, $direction);
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

        $imagePath = null;

if ($request->hasFile('image')) {
    $file = $request->file('image');
        if ($file->isValid()) {
        $path = $file->store('project_images', 's3');
        logger('Subido a S3 (public): ' . $path);
        $imagePath = $path;
    }
    else {
            logger('Archivo inválido');
        }
} else {
    logger('No hay archivo en request');
}


        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags,
            'sector_category' => $request->sector_category,
            'creation_date' => $request->creation_date,
            'link' => $request->link,
            'image' => $imagePath, // solo se guarda la ruta
            'author_id' => auth()->id(),
        ]);


        Notification::firstOrCreate([
            'user_id' => auth()->id(),
            'type' => 'proyecto',
            'title' => __('messages.notifications.message-project-registered.title'),
            'message' => __('messages.notifications.message-project-registered.message-1') . ' "' . $project->title . __('messages.notifications.message-project-registered.message-2'),
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
                $path = $imageFile->store('project_images', 's3');
                $project->images()->create([
                    'path' => $path,
                ]);
            }
        }
        return redirect()->back()->with('message', __('messages.messages.project-create'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:40',
            'description' => 'required|string|max:600',
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
            if ($project->image && Storage::disk('s3')->exists($project->image)) {
                Storage::disk('s3')->delete($project->image);
            }

            $imagePath = $request->file('image')->store('project_images', 's3');
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
                $path = $file->store('project_images', 's3');
                $project->images()->create([
                    'path' => $path,
                ]);
            }
        }

        return redirect()->back()->with('message', __('messages.messages.project-updated'));
    }


    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        if ($project->image) {
            $path = $project->image;
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        }

        foreach ($project->images as $image) {
            $path = $image->path;
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
            $image->delete();
        }

        $project->delete();

        return redirect()->back()->with('message', __('messages.messages.project-deleted'));
    }


}
