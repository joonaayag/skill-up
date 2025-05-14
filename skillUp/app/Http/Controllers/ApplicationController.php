<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationStatusChanged;
use App\Models\Application;
use App\Models\Notification;
use Illuminate\Http\Request;
use Mail;
use Storage;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'offer_id' => 'required|exists:job_offers,id',
            'candidate_name' => 'required|string|max:50',
            'position_applied' => 'required|string|max:40',
            'application_reason' => 'required|string',
            'cv' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'offer_id.required' => 'La oferta de trabajo es obligatoria.',
            'offer_id.exists' => 'La oferta seleccionada no existe.',
        
            'candidate_name.required' => 'El nombre del candidato es obligatorio.',
            'candidate_name.string' => 'El nombre debe ser una cadena de texto.',
            'candidate_name.max' => 'El nombre no puede tener más de 50 caracteres.',
        
            'position_applied.required' => 'El puesto al que se postula es obligatorio.',
            'position_applied.string' => 'El puesto debe ser una cadena de texto.',
            'position_applied.max' => 'El puesto no puede tener más de 40 caracteres.',
        
            'application_reason.required' => 'Debe indicar el motivo de la candidatura.',
            'application_reason.string' => 'El motivo debe ser una cadena de texto.',
        
            'cv.file' => 'El archivo del currículum debe ser un archivo válido.',
            'cv.mimes' => 'El currículum debe estar en formato PDF.',
            'cv.max' => 'El currículum no puede superar los 2MB.',
        ]);

        if ($request->hasFile('cv')) {
            if ($request->cv && Storage::disk('public')->exists($request->cv)) {
                Storage::disk('public')->delete($request->cv);
            }

            $cvPath = $request->file('cv')->store('cvs', 'public');
        }

        $application = Application::create([
            'user_id' => auth()->id(),
            'offer_id' => $request->offer_id,
            'candidate_name' => $request->candidate_name,
            'position_applied' => $request->position_applied,
            'application_reason' => $request->application_reason,
            'cv' => $request->hasFile('cv') ? $cvPath : null,
            'state' => 'nueva',
            'application_date' => now(),
        ]);

        $company = $application->jobOffer->company;

        if ($company) {
            Notification::create([
                'user_id' => $company->id,
                'type' => 'candidatura',
                'title' => 'Nueva candidatura recibida',
                'message' => $application->user->name . $application->user->last_name . ' se ha postulado a tu oferta "' . $application->jobOffer->name . '".',
            ]);
        }


        return redirect()->route('job.offers.show', $request->offer_id);
    }

    public function index(Request $request)
    {
        $query = Application::whereHas('jobOffer', function ($query) {
            $query->where('company_id', auth()->id());
        });

        if ($request->filled('candidate_name')) {
            $query->where('candidate_name', 'like', '%' . $request->candidate_name . '%');
        }

        if ($request->filled('position_applied')) {
            $query->where('position_applied', 'like', '%' . $request->position_applied . '%');
        }

        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        $applications = $query->latest()->get();

        return view('applications.index', compact('applications'));
    }


    public function destroy($id)
    {
        $application = Application::where('id', $id)
            ->whereHas('jobOffer', fn($q) => $q->where('company_id', auth()->id()))
            ->firstOrFail();

        Notification::create([
            'user_id' => $application->user->id,
            'type' => 'candidatura',
            'title' => 'Tu candidatura ha sido retirada',
            'message' => 'Tu candidatura para la oferta "' . $application->jobOffer->name . '" ha sido retirada por ' . (auth()->user()->role === 'Empresa' ? 'la empresa' : 'el profesor') . '.',
        ]);

        $application->delete();

        return redirect()->route('applications.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'state' => 'required|in:nueva,en revisión,aceptado,rechazado',
        ], [
            'state.required' => 'El estado es obligatorio.',
            'state.in' => 'El estado debe ser uno de los siguientes: nueva, en revisión, aceptado o rechazado.',
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

        Notification::create([
            'user_id' => $application->user->id,
            'type' => 'candidatura',
            'title' => 'Tu candidatura para la oferta ' . $application->jobOffer->name . ' ha sido actualizada.',
            'message' => ' Estado actualizado: ' . ucfirst($request->state) . ', ' . $messages[$request->state],
        ]);

        return redirect()->route('applications.index');
    }

}