<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = auth()->user()->favorites;

        $filtered = $favorites->filter(function ($fav) use ($request) {
            $item = $fav->item();

            if (!$item)
                return false;

            if ($request->filled('type') && $fav->type !== $request->type) {
                return false;
            }

            if ($request->filled('name') && !str_contains(strtolower($item->name), strtolower($request->name))) {
                return false;
            }

            if ($request->filled('description') && !str_contains(strtolower($item->description), strtolower($request->description))) {
                return false;
            }

            if ($request->filled('author')) {
                $author = $fav->type === 'proyecto' ? optional($item->author)->name : optional($item->company)->name;
                if (!str_contains(strtolower($author), strtolower($request->author))) {
                    return false;
                }
            }

            return true;
        });

        if ($request->filled('order')) {
            $filtered = $filtered->sortBy(function ($fav) use ($request) {
                $item = $fav->item();
                return $item->{$request->order} ?? null;
            });
        }

        return view('favorites.index', [
            'favorites' => $filtered,
            'filters' => $request->all(),
        ]);
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
