<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function show()
    {
        return view('dashboard');
    }

    public function profile()
    {
        $user = auth()->user();

        return view('profile.index', compact('user'));
    }

}
