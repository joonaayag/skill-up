<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Rating;
use App\Models\SchoolProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function rateProject(Request $request, Project $project)
    {
        // Detectar si es una petición AJAX
        $isAjax = $request->expectsJson() || $request->ajax() || $request->wantsJson();

        try {
            $request->validate([
                'rating' => 'required|integer|between:1,5',
            ], [
                'rating.required' => __('messages.errors.ratings.required'),
                'rating.integer' => __('messages.errors.ratings.integer'),
                'rating.between' => __('messages.errors.ratings.between'),
            ]);

            $user = Auth::user();

            if (!$user) {
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Debes estar autenticado para calificar.'
                    ], 401);
                }
                return redirect()->back()->with('error', 'Debes estar autenticado para calificar.');
            }

            $existingRating = $project->getRatingByUser($user->id);

            if ($existingRating) {
                $existingRating->update([
                    'rating' => $request->rating,
                ]);
                $message = 'Tu calificación ha sido actualizada.';
            } else {
                $project->ratings()->create([
                    'user_id' => $user->id,
                    'rating' => $request->rating,
                ]);
                $message = '¡Gracias por tu calificación!';
            }

            // Si es AJAX, devolver JSON
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'userRating' => $request->rating,
                    'averageRating' => $project->averageRating(),
                    'totalRatings' => $project->ratings()->count()
                ]);
            }

            // Si no es AJAX, redireccionar (fallback)
            return redirect()->back()->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos.',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors());

        } catch (\Exception $e) {
            \Log::error('Error al calificar proyecto: ' . $e->getMessage());

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error inesperado. Inténtalo de nuevo.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Ocurrió un error inesperado.');
        }
    }

    public function rateSchoolProject(Request $request, SchoolProject $schoolProject)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
        ], [
            'rating.required' => __('messages.errors.ratings.required'),
            'rating.integer' => __('messages.errors.ratings.integer'),
            'rating.between' => __('messages.errors.ratings.between'),
        ]);

        $user = Auth::user();

        $existingRating = $schoolProject->getRatingByUser($user->id);

        if ($existingRating) {
            $existingRating->update([
                'rating' => $request->rating,
            ]);

            return redirect()->back();
        } else {

            $schoolProject->ratings()->create([
                'user_id' => $user->id,
                'rating' => $request->rating,
            ]);

            return redirect()->back();
        }
    }
}
