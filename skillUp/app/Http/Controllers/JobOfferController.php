<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{

    public function index(Request $request)
    {
        $query = JobOffer::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
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

        $offers = $query->latest()->get();

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
        if (auth()->user()->role !== 'empresa') {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'sector_category' => 'required|string',
            'general_category' => 'required|string',
            'state' => 'required|in:abierta,cerrada',
            'logo' => 'nullable|string|max:255',
        ]);

        JobOffer::create([
            'name' => $request->name,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'sector_category' => $request->sector_category,
            'general_category' => $request->general_category,
            'state' => $request->state,
            'company_id' => auth()->id(),
            'logo' => $request->logo,
        ]);

        return redirect()->route('job.offers.company.index');
    }

    public function destroy($id)
    {
        $jobOffer = JobOffer::findOrFail($id);

        if (auth()->user()->role !== 'empresa' || $jobOffer->company_id !== auth()->id()) {
            abort(403, 'Acceso denegado');
        }

        $jobOffer->delete();

        return redirect()->route('job.offers.company.index');
    }


    public function update(Request $request, $id)
    {
        $jobOffer = JobOffer::findOrFail($id);

        if (auth()->user()->role !== 'empresa' || $jobOffer->company_id !== auth()->id()) {
            abort(403, 'Acceso denegado');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'sector_category' => 'required|string',
            'general_category' => 'required|string',
            'state' => 'required|in:abierta,cerrada',
            'logo' => 'nullable|string|max:255',
        ]);

        $jobOffer->update([
            'name' => $request->name,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'sector_category' => $request->sector_category,
            'general_category' => $request->general_category,
            'state' => $request->state,
            'logo' => $request->logo,
        ]);

        return redirect()->route('job.offers.company.index');
    }

    public function show($id)
    {
        $offer = JobOffer::findOrFail($id);
        return view('job_offers.offer_details', compact('offer'));
    }


}
