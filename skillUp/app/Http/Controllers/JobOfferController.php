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
        if ($request->filled('sector_category')) {
            $query->whereIn('sector_category', $request->sector_category);
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
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->order, $direction);
        } else {
            $query->latest();
        }


        if ($request->filled('state')) {
            $query->where('state', $request->state);
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
            'sector_category' => 'required|in:' . implode(',', [
                'Agricultura/Medio ambiente',
                'Arte/Cultura',
                'Automoción',
                'Ciberseguridad',
                'Community Manager',
                'Construcción',
                'Coordinación Educativa',
                'Diseño Gráfico',
                'Electricidad y fontanería',
                'Energía/Renovables',
                'Farmacia',
                'Finanzas y contabilidad',
                'Fotografía/vídeo',
                'Hostelería/turismo',
                'AI',
                'Investigación/laboratorio',
                'Legal',
                'Logística',
                'Mecánica',
                'Medicina/Enfermería',
                'Nutrición',
                'Operador Industrial',
                'Orientación',
                'Periodismo',
                'Enseñanza',
                'Psicología',
                'Publicidad',
                'Redes y Sistemas',
                'RRHH',
                'Seguridad',
                'SEO/SEM',
                'Terapias/Rehabilitación',
                'Traducción',
                'Transporte/Entrega',
                'Ventas'
            ]),
            'general_category' => 'required|in:Administración y negocio,Ciencia y salud,Comunicación,Diseño y comunicación,Educación,Industria,Otro,Tecnología y desarrollo',
            'state' => 'required|in:Abierta,Cerrada',
        ], [
            'name.required' => __('messages.errors.name.required'),
            'name.string' => __('messages.errors.name.string'),
            'name.max' => __('messages.errors.name.max'),

            'subtitle.string' => __('messages.errors.subtitle.string'),
            'subtitle.max' => __('messages.errors.subtitle.max'),

            'description.required' => __('messages.errors.description.required'),
            'description.string' => __('messages.errors.description.string'),

            'sector_category.required' => __('messages.errors.sector_offer.required'),
            'sector_category.in' => __('messages.errors.sector_offer.in'),

            'general_category.required' => __('messages.errors.sector.required'),
            'general_category.in' => __('messages.errors.sector.in'),

            'state.required' => __('messages.errors.state.required'),
            'state.in' => __('messages.errors.state.in'),

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
                'title' => __('messages.notifications.message-new-offer.title'),
                'message' => __('messages.notifications.message-new-offer.message') . ' "' . $jobOffer->name . '".',
            ]);
        }


        return back()->with('message', __('messages.messages.offer-create'));
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
                'title' => __('messages.notifications.message-offer-deleted.title'),
                'message' => __('messages.notifications.message-offer-deleted.message') . ' "' . $jobOffer->name . __('messages.notifications.message-offer-deleted.message-2') . (auth()->user()->role === 'Empresa' ? 'la empresa' : 'el profesor') . '.',
            ]);
        }

        $jobOffer->delete();


        $jobOffer->delete();

        return redirect()->route('job.offers.company.index')->with('message', __('messages.messages.offer-delete'));
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
            'sector_category' => 'required|in:' . implode(',', [
                'Agricultura/Medio ambiente',
                'Arte/Cultura',
                'Automoción',
                'Ciberseguridad',
                'Community Manager',
                'Construcción',
                'Coordinación Educativa',
                'Diseño Gráfico',
                'Electricidad y fontanería',
                'Energía/Renovables',
                'Farmacia',
                'Finanzas y contabilidad',
                'Fotografía/vídeo',
                'Hostelería/turismo',
                'AI',
                'Investigación/laboratorio',
                'Legal',
                'Logística',
                'Mecánica',
                'Medicina/Enfermería',
                'Nutrición',
                'Operador Industrial',
                'Orientación',
                'Periodismo',
                'Enseñanza',
                'Psicología',
                'Publicidad',
                'Redes y Sistemas',
                'RRHH',
                'Seguridad',
                'SEO/SEM',
                'Terapias/Rehabilitación',
                'Traducción',
                'Transporte/Entrega',
                'Ventas'
            ]),
            'general_category' => 'required|in:Administración y negocio,Ciencia y salud,Comunicación,Diseño y comunicación,Educación,Industria,Otro,Tecnología y desarrollo',
            'state' => 'required|in:abierta,cerrada',
            'logo' => 'nullable|string|max:255',
        ], [
            'name.required' => __('messages.errors.name.required'),
            'name.string' => __('messages.errors.name.string'),
            'name.max' => __('messages.errors.name_offer.max'),

            'subtitle.string' => __('messages.errors.subtitle.string'),
            'subtitle.max' => __('messages.errors.subtitle_offer.max'),

            'description.required' => __('messages.errors.description.required'),
            'description.string' => __('messages.errors.description.string'),

            'sector_category.required' => __('messages.errors.sector_offer.required'),
            'sector_category.string' => __('messages.errors.sector_offer.string'),
            'sector_category.in' => __('messages.errors.sector_offer.in'),

            'general_category.required' => __('messages.errors.sector.required'),
            'general_category.string' => __('messages.errors.sector.string'),
            'general_category.in' => __('messages.errors.sector.in'),

            'state.required' => __('messages.errors.state.required'),
            'state.in' => __('messages.errors.state.in'),

            'logo.string' => 'El logo debe ser una cadena de texto.',
            'logo.max' => 'La URL del logo no puede superar los 255 caracteres.',
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
                    'title' => __('messages.notifications.message-offer-closed.title'),
                    'message' => __('messages.notifications.message-offer-closed.message-1') . ' "' . $jobOffer->name . __('messages.notifications.message-offer-closed.message-2'),
                ]);
            }
        }


        return redirect()->route('job.offers.company.index')->with('message', __('messages.messages.offer-update'));
    }

    public function show($id)
    {
        $offer = JobOffer::findOrFail($id);
        $offer->increment('views');
        return view('job_offers.offer_details', compact('offer'));
    }


}
