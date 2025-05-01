<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    public function show()
    {
        return view('dashboard');
    }

    public function profile(){
        return view('profile.index');
    }
}
