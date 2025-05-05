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
            'name' => 'required|string|max:20',
            'last_name' => 'required|string|max:40',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'description' => 'nullable|string|max:300',
            'foto_perfil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'cv' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 20 caracteres.',
            
            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.max' => 'El apellido no puede tener más de 40 caracteres.',
            
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
        
            'description.max' => 'La descripción no puede superar los 300 caracteres.',
        
            'foto_perfil.image' => 'La foto de perfil debe ser una imagen.',
            'foto_perfil.mimes' => 'La foto de perfil debe ser un archivo JPG, JPEG o PNG.',
            'foto_perfil.max' => 'La foto de perfil no puede superar los 2MB.',
        
            'banner.image' => 'El banner debe ser una imagen.',
            'banner.mimes' => 'El banner debe ser un archivo JPG, JPEG o PNG.',
            'banner.max' => 'El banner no puede superar los 4MB.',
        
            'cv.file' => 'El currículum debe ser un archivo.',
            'cv.mimes' => 'El currículum debe estar en formato PDF.',
            'cv.max' => 'El currículum no puede superar los 2MB.',
        ]);

        if ($request->hasFile('cv')) {
            if ($request->cv && Storage::disk('public')->exists($request->cv)) {
                Storage::disk('public')->delete($request->cv);
            }

            $cvPath = $request->file('cv')->store('cvs', 'public');
            $user->cv = $cvPath;
        }

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