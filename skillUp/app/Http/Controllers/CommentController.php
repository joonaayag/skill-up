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
            'content' => 'required|string|max:300',
            'parent_id' => 'nullable|exists:comments,id'
        ], [
            'content.required' => __('messages.errors.comments.required'),
            'content.string' => __('messages.errors.comments.string'),
            'content.max' => __('messages.errors.comments.max'),
        
            'parent_id.exists' => __('messages.errors.comments.exists'),
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
            'content' => 'required|string|max:300',
            'parent_id' => 'nullable|exists:comments,id'
        ], [
            'content.required' => __('messages.errors.comments.required'),
            'content.string' => __('messages.errors.comments.string'),
            'content.max' => __('messages.errors.comments.max'),
        
            'parent_id.exists' => __('messages.errors.comments.exists'),
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
            'content' => 'required|string|max:300',
        ], [
            'content.required' => __('messages.errors.comment.content-required'),
            'content.string' => __('messages.errors.comment.content-string'),
            'content.max' => __('messages.errors.comment.content-max'),
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
