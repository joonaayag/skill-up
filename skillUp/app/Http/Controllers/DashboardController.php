<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\Project;
use App\Models\SchoolProject;
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
            ->take(4)
            ->get();

        $schoolProjects = SchoolProject::withAvg('ratings', 'rating')
            ->orderByDesc('ratings_avg_rating')
            ->take(2)
            ->get();

        // Combined all
        $combined = collect();

        // Add 4 normal projects
        $combined = $combined->concat($projects);

        // If there are less than 4 normal projects, add more from school ones
        if ($projects->count() < 4) {
            $remaining = 4 - $projects->count();
            $extraSchools = SchoolProject::withAvg('ratings', 'rating')
                ->orderByDesc('ratings_avg_rating')
                ->skip($schoolProjects->count()) // Skip the already taken ones
                ->take($remaining)
                ->get();

            $schoolProjects = $schoolProjects->concat($extraSchools);
        }

        // Add 2 school projects
        $combined = $combined->concat($schoolProjects);

        // If there are less than 6 projects in total, add more from the top-rated projects 
        if ($combined->count() < 6) {
            $needed = 6 - $combined->count();
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

    public function profile()
    {
        $user = auth()->user();

        return view('profile.index', compact('user'));
    }

}
