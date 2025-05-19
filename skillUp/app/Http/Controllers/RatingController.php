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
            'rating.required' => __('messages.errors.ratings.required'),
            'rating.integer' => __('messages.errors.ratings.integer'),
            'rating.between' => __('messages.errors.ratings.between'),
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
