<?php

namespace App\Http\Controllers;

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

        return view('dashboard', compact('notifications'));
    }

    public function profile()
    {
        $user = auth()->user();

        return view('profile.index', compact('user'));
    }

}
