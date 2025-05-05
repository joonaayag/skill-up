<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\SchoolProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function storeProjectComment(Request $request, Project $project)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ], [
            'content.required' => 'El contenido del comentario es obligatorio.',
            'content.string' => 'El contenido debe ser una cadena de texto.',
            'content.max' => 'El contenido no puede superar los 1000 caracteres.',
        
            'parent_id.exists' => 'El comentario padre seleccionado no existe.',
        ]);

        $comment = new Comment([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id
        ]);

        $project->comments()->save($comment);

        return back()->with('success', 'Comentario añadido correctamente.');
    }

    public function storeSchoolProjectComment(Request $request, SchoolProject $schoolProject)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ], [
            'content.required' => 'El contenido del comentario es obligatorio.',
            'content.string' => 'El contenido debe ser una cadena de texto.',
            'content.max' => 'El contenido no puede superar los 1000 caracteres.',
        
            'parent_id.exists' => 'El comentario padre seleccionado no existe.',
        ]);

        $comment = new Comment([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id
        ]);

        $schoolProject->comments()->save($comment);

        return back()->with('success', 'Comentario añadido correctamente.');
    }

    public function edit(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso para editar este comentario.');
        }

        return view('comments.edit', compact('comment'));
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso para actualizar este comentario.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ], [
            'content.required' => 'El contenido es obligatorio.',
            'content.string' => 'El contenido debe ser una cadena de texto.',
            'content.max' => 'El contenido no puede superar los 1000 caracteres.',
        ]);

        $comment->update([
            'content' => $request->content
        ]);

        if ($comment->project_id) {
            return redirect()->route('projects.show', $comment->project_id)->with('success', 'Comentario actualizado correctamente.');
        } else {
            return redirect()->route('school-projects.show', $comment->school_project_id)->with('success', 'Comentario actualizado correctamente.');
        }
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return back()->with('error', 'No tienes permiso para eliminar este comentario.');
        }

        $projectId = $comment->project_id;
        $schoolProjectId = $comment->school_project_id;

        $comment->delete();

        if ($projectId) {
            return redirect()->route('projects.show', $projectId)->with('success', 'Comentario eliminado correctamente.');
        } else {
            return redirect()->route('school-projects.show', $schoolProjectId)->with('success', 'Comentario eliminado correctamente.');
        }
    }
}
