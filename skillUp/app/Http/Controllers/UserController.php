<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Storage;

class UserController extends Controller
{

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validar los campos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'description' => 'nullable|string|max:1000',
            'foto_perfil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        // Actualizar datos básicos
        $user->name = $validated['name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->description = $validated['description'] ?? null;

        // Subir y guardar la nueva foto de perfil si existe
        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil) {
                Storage::delete($user->foto_perfil);
            }
            $user->foto_perfil = $request->file('foto_perfil')->store('perfiles', 'public');
        }

        // Subir y guardar el nuevo banner si existe
        if ($request->hasFile('banner')) {
            if ($user->banner) {
                Storage::delete($user->banner);
            }
            $user->banner = $request->file('banner')->store('banners', 'public');
        }

        $user->save();

        return redirect()->route('profile.index');
    }


}
