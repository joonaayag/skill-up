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

    // Si es una petición AJAX, devolver respuesta JSON
    if ($request->ajax() || $request->wantsJson()) {
        // Cargar las relaciones necesarias del comentario
        $comment->load('user');
        
        $isReply = !empty($request->parent_id);
        
        if ($isReply) {
            // Es una respuesta, renderizar solo el HTML de la respuesta
            $replyHtml = view('comments.single_reply', [
                'reply' => $comment,
                'type' => 'project'
            ])->render();

            return response()->json([
                'success' => true,
                'message' => __('messages.messages.comment-create'),
                'reply_html' => $replyHtml,
                'parent_id' => $request->parent_id,
                'is_reply' => true,
                'comments_count' => $project->comments->count()
            ]);
        } else {
            // Es un comentario principal, renderizar el comentario completo
            $commentHtml = view('comments.single_comment', [
                'comment' => $comment,
                'type' => 'project',
                'commentable' => $project
            ])->render();

            return response()->json([
                'success' => true,
                'message' => __('messages.messages.comment-create'),
                'comment_html' => $commentHtml,
                'is_reply' => false,
                'comments_count' => $project->comments->count()
            ]);
        }
    }

    return back()->with('message', __('messages.messages.comment-create'));
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

    // Si es una petición AJAX, devolver respuesta JSON
    if ($request->ajax() || $request->wantsJson()) {
        // Cargar las relaciones necesarias del comentario
        $comment->load('user');
        
        $isReply = !empty($request->parent_id);
        
        if ($isReply) {
            // Es una respuesta, renderizar solo el HTML de la respuesta
            $replyHtml = view('comments.single_reply', [
                'reply' => $comment,
                'type' => 'school-project'
            ])->render();

            return response()->json([
                'success' => true,
                'message' => __('messages.messages.comment-create'),
                'reply_html' => $replyHtml,
                'parent_id' => $request->parent_id,
                'is_reply' => true,
                'comments_count' => $schoolProject->comments->count()
            ]);
        } else {
            // Es un comentario principal, renderizar el comentario completo
            $commentHtml = view('comments.single_comment', [
                'comment' => $comment,
                'type' => 'school-project',
                'commentable' => $schoolProject
            ])->render();

            return response()->json([
                'success' => true,
                'message' => __('messages.messages.comment-create'),
                'comment_html' => $commentHtml,
                'is_reply' => false,
                'comments_count' => $schoolProject->comments->count()
            ]);
        }
    }

    return back()->with('message', __('messages.messages.comment-create'));
}

    public function edit(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes permiso para editar este comentario.');
        }

        return view('comments.edit', compact('comment'))->with('message', __('messages.messages.comment-edit'));
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
            return redirect()->route('projects.show', $comment->project_id)->with('message', __('messages.messages.comment-update'));
        } else {
            return redirect()->route('school-projects.show', $comment->school_project_id)->with('message', __('messages.messages.comment-update'));
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
            return redirect()->route('projects.show', $projectId)->with('message', __('messages.messages.comment-delete'));
        } else {
            return redirect()->route('school-projects.show', $schoolProjectId)->with('message', __('messages.messages.comment-delete'));
        }
    }
}
