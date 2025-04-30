<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = auth()->user()->favorites()->get();

        return view('favorites.index', compact('favorites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:proyecto,oferta',
            'reference_id' => 'required|integer',
        ]);

        Favorite::firstOrCreate([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'reference_id' => $request->reference_id,
        ]);

        return back();
    }

    public function destroy($id)
    {
        $favorite = Favorite::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $favorite->delete();

        return back()->with('success', 'Eliminado de favoritos');
    }


}
