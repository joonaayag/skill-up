<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'description' => 'nullable|string|max:1000',
            'foto_perfil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);
        $user->name = $validated['name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->description = $validated['description'];

        

        if ($request->hasFile('foto_perfil')) {
            $path = $request->file('foto_perfil')->store('perfil', 'public');
            $user->foto_perfil = $path;
        }

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('banners', 'public');
            $user->banner = $path;
        }

        $user->save();

        $detail = $user->detail;

        if ($user->role === 'alumno') {
            $detail->birth_date = $request->birth_date;
            $detail->current_course = $request->current_course;
            $detail->educational_center = $request->educational_center;
        }

        if ($user->role === 'profesor') {
            $detail->specialization = $request->specialization;
            $detail->department = $request->department;
        }

        if ($user->role === 'empresa') {
            $detail->cif = $request->cif;
            $detail->address = $request->address;
            $detail->sector = $request->sector;
            $detail->website = $request->website;
        }

        $detail->save();

        return redirect()->back();
    }

}