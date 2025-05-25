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

        $rules = [
            'name' => 'required|string|max:20',
            'last_name' => 'required|string|max:40',
            'email' => 'required|email|string|max:50|unique:users,email,' . $user->id,
            'description' => 'nullable|string|max:600',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'cv' => 'nullable|file|mimes:pdf|max:2048',
        ];

        switch ($user->role) {
            case 'Alumno':
                $rules += [
                    'birth_date' => 'required|date|before_or_equal:' . date('Y-m-d'),
                    'current_course' => 'required|string|max:50',
                    'educational_center' => 'required|string|max:100',
                ];
                break;
            case 'Profesor':
                $rules += [
                    'educational_center' => 'required|string|max:100',
                    'specialization' => 'required|string|max:100',
                    'department' => 'required|string|max:100',
                ];
                break;
            case 'Empresa':
                $rules += [
                    'cif' => 'required|string|max:50',
                    'address' => 'required|string|max:255',
                    'sector' => 'required|string|max:100',
                    'website' => 'nullable|url|max:255',
                ];
                break;
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('cv')) {
            if ($user->cv && Storage::disk('public')->exists($user->cv)) {
                Storage::disk('public')->delete($user->cv);
            }
            $user->cv = $request->file('cv')->store('cvs', 'public');
        }

        if ($request->hasFile('profile')) {
            $user->profile = $request->file('profile')->store('perfil', 'public');
        }

        if ($request->hasFile('banner')) {
            $user->banner = $request->file('banner')->store('banners', 'public');
        }

        $user->name = ucfirst($validated['name']);
        $user->last_name = ucfirst($validated['last_name']);
        $user->email = $validated['email'];
        $user->description = $validated['description'];
        $user->save();

        $detail = $user->detail ?? new UserDetail(['user_id' => $user->id]);

        switch ($user->role) {
            case 'Alumno':
                $detail->birth_date = $request->birth_date;
                $detail->current_course = ucfirst($request->current_course);
                $detail->educational_center = ucfirst($request->educational_center);
                break;
            case 'Profesor':
                $detail->educational_center = ucfirst($request->educational_center);
                $detail->specialization = ucfirst($request->specialization);
                $detail->department = ucfirst($request->department);
                break;
            case 'Empresa':
                $detail->cif = strtoupper($request->cif);
                $detail->address = ucfirst($request->address);
                $detail->sector = ucfirst($request->sector);
                $detail->website = $request->website;
                break;
        }

        $detail->save();

        return redirect()->back()->with('messages', __('messages.messages.user-updated'));
    }

}