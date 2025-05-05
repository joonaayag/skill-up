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
        $request->validate([
            'rating' => 'required|integer|between:1,5',
        ], [
            'rating.required' => 'La valoración es obligatoria.',
            'rating.integer' => 'La valoración debe ser un número entero.',
            'rating.between' => 'La valoración debe estar entre 1 y 5 estrellas.',
        ]);

        $user = Auth::user();
        
        $existingRating = $project->getRatingByUser($user->id);
        
        if ($existingRating) {
            $existingRating->update([
                'rating' => $request->rating,

            ]);
            
            return redirect()->back();
        } else {
            $project->ratings()->create([
                'user_id' => $user->id,
                'rating' => $request->rating,
            ]);
            
            return redirect()->back();
        }
    }

    public function rateSchoolProject(Request $request, SchoolProject $schoolProject)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
        ], [
            'rating.required' => 'La valoración es obligatoria.',
            'rating.integer' => 'La valoración debe ser un número entero.',
            'rating.between' => 'La valoración debe estar entre 1 y 5 estrellas.',
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
