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
                'title' => __('messages.notifications.message-project-deleted-teacher.title'),
                'message' =>  __('messages.notifications.message-project-deleted-teacher.message-1') . ' "' . $project->title . __('messages.notifications.message-project-deleted-teacher.message-2'),
            ]);
        }


        $project->delete();

        return redirect()->route('school.projects.index')->with('message', 'messages.messages.sp-delete');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:40',
            'author' => 'required|string|max:50',
            'creation_date' => 'required|date',
            'description' => 'required|string',
            'tags' => 'required|in:TFG,TFM,Tesis,Individual,Grupal,Tecnología,Ciencias,Artes,Ingeniería',
            'general_category' => 'required|in:Administración y negocio,Ciencia y salud,Comunicación,Diseño y comunicación,Educación,Industria,Otro,Tecnología y desarrollo',
            'link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'files.*' => 'nullable|file|max:4096',
        ], [
            'title.required' => __('messages.errors.title.required'),
            'title.string' => __('messages.errors.title.string'),
            'title.max' => __('messages.errors.title.max'),

            'author.required' => __('messages.errors.author.required'),
            'author.string' => __('messages.errors.author.string'),
            'author.max' => __('messages.errors.author.max'),

            'creation_date.required' => __('messages.errors.creation_date.required'),
            'creation_date.date' => __('messages.errors.creation_date.date'),

            'description.required' => __('messages.errors.description.required'),
            'description.string' => __('messages.errors.description.string'),

            'tags.in' => __('messages.errors.tags.in'),
            'tags.required' => __('messages.errors.tags.required'),

            'general_category.in' => __('messages.errors.sector.in'),
            'general_category.required' => __('messages.errors.sector.required'),
            
            'images.*.image' => __('messages.errors.image.image'),
            'images.*.mimes' => __('messages.errors.image.mimes'),
            'images.*.max' => __('messages.errors.image.max'),

            'files.*.file' => __('messages.errors.file.file'),
            'files.*.max' => __('messages.errors.file.max'),
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
                'user_id' => $project->teacher_id,
                'type' => 'proyecto',
                'title' => __('messages.notifications.message-project-updated-teacher.title'),
                'message' => __('messages.notifications.message-project-updated-teacher.message-1') . $project->title . __('messages.notifications.message-project-updated-teacher.message-2'),
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


        return redirect()->route('school.projects.index')->with('message', 'messages.messages.sp-update');
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
            'tags' => 'required|in:TFG,TFM,Tesis,Individual,Grupal,Tecnología,Ciencias,Artes,Ingeniería',
            'general_category' => 'required|in:Administración y negocio,Ciencia y salud,Comunicación,Diseño y comunicación,Educación,Industria,Otro,Tecnología y desarrollo',
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'files.*' => 'nullable|file|max:4096',
        ], [
            'title.required' => __('messages.errors.title.required'),
            'title.string' => __('messages.errors.title.string'),
            'title.max' => __('messages.errors.title.max'),

            'author.required' => __('messages.errors.author.required'),
            'author.string' => __('messages.errors.author.string'),
            'author.max' => __('messages.errors.author.max'),

            'creation_date.required' => __('messages.errors.creation_date.required'),
            'creation_date.date' => __('messages.errors.creation_date.date'),

            'description.required' => __('messages.errors.description.required'),
            'description.string' => __('messages.errors.description.string'),

            'tags.in' => __('messages.errors.tags.in'),

            'general_category.in' => __('messages.errors.sector.in'),
            'general_category.required' => __('messages.errors.sector.required'),
            
            'images.*.image' => __('messages.errors.image.image'),
            'images.*.mimes' => __('messages.errors.image.mimes'),
            'images.*.max' => __('messages.errors.image.max'),

            'files.*.file' => __('messages.errors.file.file'),
            'files.*.max' => __('messages.errors.file.max'),
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
            'title' => __('messages.notifications.message-project-published.title'),
            'message' => __('messages.notifications.message-project-published.message-1') . $project->title . __('messages.notifications.message-project-published.message-2'),
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $imageFile) {
                $path = $imageFile->store('project_images', 'public');
                $project->images()->create([
                    'path' => $path,
                ]);
            }
        }

        return redirect()->route('school.projects.index')->with('message', 'messages.messages.project-create');
    }

}
