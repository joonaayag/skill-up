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
            'candidate_name' => 'required|string|max:20',
            'position_applied' => 'required|string|max:40',
            'application_reason' => 'required|string|max:255',
            'cv' => 'nullable|file|mimes:pdf|max:2048',
        ], [
            'offer_id.required' => __('messages.errors.offer.required'),
            'offer_id.exists' => __('messages.errors.offer.exists'),
        
            'candidate_name.required' => __('messages.errors.candidate-name.required'),
            'candidate_name.string' => __('messages.errors.candidate-name.string'),
            'candidate_name.max' => __('messages.errors.candidate-name.max'),
        
            'position_applied.required' => __('messages.errors.position-applied.required'),
            'position_applied.string' => __('messages.errors.position-applied.string'),
            'position_applied.max' => __('messages.errors.position-applied.max'),
        
            'application_reason.required' => __('messages.errors.application-reason-required'),
            'application_reason.string' => __('messages.errors.application-reason-string'),
            'application_reason.max' => __('messages.errors.application-reason-max'),
        
            'cv.file' => __('messages.errors.offers.cv-file'),
            'cv.mimes' => __('messages.errors.offers.cv-mimes'),
            'cv.max' => __('messages.errors.offers.cv-max'),
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
                'title' => __('messages.notifications.message-app-received.title'),
                'message' => $application->user->name . $application->user->last_name . __('messages.notifications.message-app-received.message') . $application->jobOffer->name . '".',
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
            'title' => __('messages.notifications.message-app-deleted.title'),
            'message' => __('messages.notifications.message-app-deleted.message-1') . $application->jobOffer->name . __('messages.notifications.message-app-deleted.message-2') . (auth()->user()->role === 'Empresa' ? 'la empresa' : 'el profesor') . '.',
        ]);

        $application->delete();

        return redirect()->route('applications.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'state' => 'required|in:nueva,en revisión,aceptado,rechazado',
        ], [
            'state.required' => __('messages.errors.offers.state-required'),
            'state.in' => __('messages.errors.offers.state-in'),
        ]);

        $application = Application::where('id', $id)
            ->whereHas('jobOffer', fn($q) => $q->where('company_id', auth()->id()))
            ->firstOrFail();

        $application->update([
            'state' => $request->state,
        ]);

        $messages = [
            'nueva' => __('messages.email.new'),
            'en revisión' => __('messages.email.review'),
            'aceptado' => __('messages.email.accepted'),
            'rechazado' => __('messages.email.rejected'),
        ];

        $email = $application->user->email ?? null;

        if ($email) {
            Mail::to($email)->send(new ApplicationStatusChanged($application, $messages[$request->state]));
        }

        Notification::create([
            'user_id' => $application->user->id,
            'type' => 'candidatura',
            'title' => __('messages.email.text-title-1') . $application->jobOffer->name . __('messages.email.text-title-2'),
            'message' => __('messages.email.message') . __('messages.email.messages.' . $request->state) . ', ' . $messages[$request->state],
        ]);

        return redirect()->route('applications.index');
    }

}