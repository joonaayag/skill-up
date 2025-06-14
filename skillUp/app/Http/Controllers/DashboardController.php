<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\Project;
use App\Models\SchoolProject;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->take(4)
            ->get();

        // Load top-rated projects  
        $projects = Project::withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->take(5)
            ->get();

        $schoolProjects = SchoolProject::withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->take(2)
            ->get();

        // Combined all
        $combined = collect();

        // Add 5 normal projects
        $combined = $combined->concat($projects);

        // If there are less than 5 normal projects, add more from school ones
        if ($projects->count() < 5) {
            $remaining = 5 - $projects->count();
            $extraSchools = SchoolProject::withAvg('ratings', 'rating')
                ->orderByDesc('ratings_avg_rating')
                ->skip($schoolProjects->count()) // Skip the already taken ones
                ->take($remaining)
                ->get();

            $schoolProjects = $schoolProjects->concat($extraSchools);
        }

        // Add 2 school projects
        $combined = $combined->concat($schoolProjects);

        // If there are less than 7 projects in total, add more from the top-rated projects 
        if ($combined->count() < 7) {
            $needed = 7 - $combined->count();
            $moreProjects = Project::withAvg('ratings', 'rating')
                ->orderByDesc('ratings_avg_rating')
                ->skip($projects->count()) // Skip the already taken ones
                ->take($needed)
                ->get();

            $combined = $combined->concat($moreProjects);
        }

        $ownProjects = auth()->user()->projects()
            ->take(2)
            ->get();

        $jobOffers = JobOffer::latest()
            ->take(4)
            ->get();


        return view('dashboard', compact('notifications', 'combined', 'ownProjects', 'jobOffers'));
    }

    public function profile($id)
    {
        $user = User::findOrFail($id);

        return view('profile.index', compact('user'));
    }

}
