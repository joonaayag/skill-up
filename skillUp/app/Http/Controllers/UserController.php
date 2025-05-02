<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() != $id) {
            return redirect()->back()->with('error', 'No tienes permiso para editar este perfil.');
        }

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
        $user->description = $validated['description'] ?? null;

        Storage::disk('public')->makeDirectory('banners');
        Storage::disk('public')->makeDirectory('perfiles');

        if ($request->hasFile('foto_perfil') && $request->file('foto_perfil')->isValid()) {
            // Eliminar la foto antigua si existe
            if ($user->foto_perfil) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            $user->foto_perfil = $request->file('foto_perfil')->store('perfiles', 'public');
        }

        if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
            // Eliminar el banner antiguo si existe
            if ($user->banner) {
                Storage::disk('public')->delete($user->banner);
            }
            $user->banner = $request->file('banner')->store('banners', 'public');
        }

        $user->save();

        return redirect()->route('profile.index');
    }
}