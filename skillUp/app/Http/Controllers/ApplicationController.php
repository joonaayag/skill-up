<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationStatusChanged;
use App\Models\Application;
use Illuminate\Http\Request;
use Mail;

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

        Application::create([
            'user_id' => auth()->id(),
            'offer_id' => $request->offer_id,
            'candidate_name' => $request->candidate_name,
            'position_applied' => $request->position_applied,
            'application_reason' => $request->application_reason,
            'cv' => $request->file('cv') ? $request->file('cv')->store('cvs') : null,
            'state' => 'nueva',
            'application_date' => now(),
        ]);

        return redirect()->route('job.offers.show', $request->offer_id);
    }

    public function index()
    {

        $applications = Application::whereHas('jobOffer', function ($query) {
            $query->where('company_id', auth()->id());
        })->latest()->get();

        return view('applications.index', compact('applications'));
    }

    public function destroy($id)
    {
        $application = Application::where('id', $id)
            ->whereHas('jobOffer', fn($q) => $q->where('company_id', auth()->id()))
            ->firstOrFail();

        $application->delete();

        return redirect()->route('applications.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'state' => 'required|in:nueva,en revisión,aceptado,rechazado',
        ]);

        $application = Application::where('id', $id)
            ->whereHas('jobOffer', fn($q) => $q->where('company_id', auth()->id()))
            ->firstOrFail();

        $application->update([
            'state' => $request->state,
        ]);

        $messages = [
            'nueva' => 'Tu candidatura ha sido registrada correctamente y será revisada pronto.',
            'en revisión' => 'Estamos revisando tu perfil. Te mantendremos informado.',
            'aceptado' => '¡Felicidades! Tu candidatura ha sido aceptada. Nos pondremos en contacto contigo pronto.',
            'rechazado' => 'Lamentamos informarte que esta vez no ha sido posible continuar con tu candidatura.',
        ];

        $email = $application->user->email ?? null;

        if ($email) {
            Mail::to($email)->send(new ApplicationStatusChanged($application, $messages[$request->state]));
        }

        return redirect()->route('applications.index');
    }

}