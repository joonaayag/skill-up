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
            'email' => 'required|email|string|max:50|unique:users,email,' . $user->id,
            'description' => 'nullable|string|max:300',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'cv' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'name.required' => __('messages.errors.name.required'),
            'name.string' => __('messages.errors.name.string'),
            'name.max' => __('messages.errors.name.max'),

            'last_name.required' => __('messages.errors.last_name.required'),
            'last_name.max' => __('messages.errors.last_name.max'),
            'last_name.string' => __('messages.errors.last_name.string'),

            'email.required' => __('messages.errors.email.required'),
            'email.email' => __('messages.errors.email.email'),
            'email.unique' => __('messages.errors.email.unique'),
            'email.max' => __('messages.errors.email.max'),
            'email.string' => __('messages.errors.email.string'),

            'description.max' => __('messages.errors.description.max'),

            'profile.image' => __('messages.errors.profile.image'),
            'profile.mimes' => __('messages.errors.profile.mimes'),
            'profile.max' => __('messages.errors.profile.max'),

            'banner.image' => __('messages.errors.banner.image'),
            'banner.mimes' => __('messages.errors.banner.mimes'),
            'banner.max' => __('messages.errors.banner.max'),

            'cv.file' => __('messages.errors.cv.file'),
            'cv.mimes' => __('messages.errors.cv.mimes'),
            'cv.max' => __('messages.errors.cv.max'),
        ]);

        if ($request->hasFile('cv')) {
            if ($request->cv && Storage::disk('public')->exists($request->cv)) {
                Storage::disk('public')->delete($request->cv);
            }

            $cvPath = $request->file('cv')->store('cvs', 'public');
            $user->cv = $cvPath;
        }

        $user->name = ucfirst($validated['name']);
        $user->last_name = ucfirst($validated['last_name']);
        $user->email = ucfirst($validated['email']);
        $user->description = ucfirst($validated['description']);


        if ($request->hasFile('profile')) {
            $path = $request->file('profile')->store('perfil', 'public');
            $user->profile = $path;
        }

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('banners', 'public');
            $user->banner = $path;
        }

        $user->save();

        $detail = $user->detail;

        if ($user->role === 'Alumno') {
            $detail->birth_date = $request->birth_date;
            $detail->current_course = ucfirst($request->current_course);
            $detail->educational_center = ucfirst($request->educational_center);
        }

        if ($user->role === 'Profesor') {
            $detail->specialization = ucfirst($request->specialization);
            $detail->department = ucfirst($request->department);
        }

        if ($user->role === 'Empresa') {
            $detail->cif = ucfirst($request->cif);
            $detail->address = ucfirst($request->address);
            $detail->sector = ucfirst($request->sector);
            $detail->website = $request->website;
        }

        $detail->save();

        return redirect()->back();
    }

}