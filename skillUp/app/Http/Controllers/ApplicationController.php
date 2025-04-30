<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'offer_id' => 'required|exists:job_offers,id',
            'candidate_name' => 'required|string|max:255',
            'position_applied' => 'required|string|max:255',
            'application_reason' => 'required|string',
            'cv' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $application = Application::create([
            'user_id' => auth()->id(),
            'offer_id' => $request->offer_id,
            'candidate_name' => $request->candidate_name,
            'position_applied' => $request->position_applied,
            'application_reason' => $request->application_reason,
            'cv' => $request->file('cv') ? $request->file('cv')->store('cvs') : null,
            'state' => 'nueva',
            'application_date' => now(),
        ]);

        return redirect()->route('job.offers.show', $request->offer_id)
            ->with('success', 'Tu aplicación ha sido enviada correctamente.');
    }
}