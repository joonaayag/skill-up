<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{

public function index(Request $request)
{
    $query = JobOffer::query();

    if ($request->filled('title')) {
        $query->where('name', 'like', '%' . $request->title . '%');
    }

    if ($request->filled('author')) {
        $query->whereHas('company', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->author . '%');
        });
    }

    if ($request->filled('general_category')) {
        $query->whereIn('general_category', $request->general_category);
    }

    $order = $request->get('order');
    $direction = $request->get('direction', 'asc');

    if ($order && in_array($order, ['name', 'general_category', 'created_at'])) {
        $query->orderBy($order, $direction);
    } else {
        $query->orderBy('created_at', 'desc');
    }

    $offers = $query->get();

    return view('job_offers.index', compact('offers'));
}



    public function companyIndex(Request $request)
    {
        $query = JobOffer::where('company_id', auth()->id());

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        if ($request->filled('general_category')) {
            $query->whereIn('general_category', (array) $request->general_category);
        }

        if ($request->filled('sector_category')) {
            $query->whereIn('sector_category', (array) $request->sector_category);
        }

        if ($request->filled('order')) {
            $query->orderBy($request->order, 'asc');
        } else {
            $query->latest();
        }

        $offers = $query->get();

        return view('job_offers.company.index', compact('offers'));
    }



    public function store(Request $request)
    {
        if ((auth()->user()->role !== 'Empresa' && auth()->user()->role !== 'Profesor')) {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'name' => 'required|string|max:40',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'sector_category' => 'required|string',
            'general_category' => 'required|string',
            'state' => 'required|in:abierta,cerrada',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 40 caracteres.',

            'subtitle.string' => 'El subtítulo debe ser una cadena de texto.',
            'subtitle.max' => 'El subtítulo no puede tener más de 255 caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',

            'sector_category.required' => 'La categoría del sector es obligatoria.',
            'sector_category.string' => 'La categoría del sector debe ser una cadena de texto.',

            'general_category.required' => 'La categoría general es obligatoria.',
            'general_category.string' => 'La categoría general debe ser una cadena de texto.',

            'state.required' => 'El estado es obligatorio.',
            'state.in' => 'El estado debe ser "abierta" o "cerrada".',

        ]);

        $jobOffer = JobOffer::create([
            'name' => $request->name,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'sector_category' => $request->sector_category,
            'general_category' => $request->general_category,
            'state' => $request->state,
            'company_id' => auth()->id(),
        ]);

        $usuarios = User::where('id', '!=', auth()->id())->get();

        foreach ($usuarios as $usuario) {
            Notification::create([
                'user_id' => $usuario->id,
                'type' => 'oferta',
                'title' => '¡Nueva oferta disponible!',
                'message' => 'Se ha publicado una nueva oferta: "' . $jobOffer->name . '".',
            ]);
        }


        return back();
    }

    public function destroy($id)
    {
        $jobOffer = JobOffer::findOrFail($id);

        if ((auth()->user()->role !== 'Empresa' && auth()->user()->role !== 'Profesor') || $jobOffer->company_id !== auth()->id()) {
            abort(403, 'Acceso denegado');
        }

        $jobOffer = JobOffer::findOrFail($id);
        $applications = $jobOffer->applications()->with('user')->get();

        foreach ($applications as $application) {
            Notification::create([
                'user_id' => $application->user->id,
                'type' => 'oferta',
                'title' => 'Oferta retirada',
                'message' => 'La oferta "' . $jobOffer->name . '" a la que te postulaste ha sido eliminada por ' . (auth()->user()->role === 'Empresa' ? 'la empresa' : 'el profesor') . '.',
            ]);
        }

        $jobOffer->delete();


        $jobOffer->delete();

        return redirect()->route('job.offers.company.index');
    }


    public function update(Request $request, $id)
    {
        $jobOffer = JobOffer::findOrFail($id);

        if ((auth()->user()->role !== 'Empresa' && auth()->user()->role !== 'Profesor') || $jobOffer->company_id !== auth()->id()) {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'name' => 'required|string|max:40',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'sector_category' => 'required|string',
            'general_category' => 'required|string',
            'state' => 'required|in:abierta,cerrada',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 40 caracteres.',

            'subtitle.string' => 'El subtítulo debe ser una cadena de texto.',
            'subtitle.max' => 'El subtítulo no puede tener más de 255 caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser una cadena de texto.',

            'sector_category.required' => 'La categoría del sector es obligatoria.',
            'sector_category.string' => 'La categoría del sector debe ser una cadena de texto.',

            'general_category.required' => 'La categoría general es obligatoria.',
            'general_category.string' => 'La categoría general debe ser una cadena de texto.',

            'state.required' => 'El estado es obligatorio.',
            'state.in' => 'El estado debe ser "abierta" o "cerrada".',

        ]);

        $jobOffer->update([
            'name' => $request->name,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'sector_category' => $request->sector_category,
            'general_category' => $request->general_category,
            'state' => $request->state,
        ]);

        $jobOffer->update($request->all());

        if ($jobOffer->status === 'cerrada') {
            $applications = $jobOffer->applications()->with('user')->get();

            foreach ($applications as $application) {
                Notification::create([
                    'user_id' => $application->user->id,
                    'type' => 'oferta',
                    'title' => 'Oferta cerrada',
                    'message' => 'La oferta "' . $jobOffer->name . '" ha sido cerrada por la empresa.',
                ]);
            }
        }


        return redirect()->route('job.offers.company.index');
    }

    public function show($id)
    {
        $offer = JobOffer::findOrFail($id);
        $offer->increment('views');
        return view('job_offers.offer_details', compact('offer'));
    }


}
