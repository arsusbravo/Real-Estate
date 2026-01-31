<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FavoriteController extends Controller
{
    /**
     * Display list of favorited properties.
     */
    public function index(): Response
    {
        $favorites = auth()->user()
            ->favorites()
            ->with(['media', 'seller:id,name'])
            ->active()
            ->latest('favorites.created_at')
            ->paginate(12);

        return Inertia::render('Buyer/Favorites/Index', [
            'favorites' => $favorites,
        ]);
    }

    /**
     * Add property to favorites.
     */
    public function store(Property $property): RedirectResponse
    {
        auth()->user()->favorites()->syncWithoutDetaching([$property->id]);

        return back()->with('success', 'Properti ditambahkan ke favorit');
    }

    /**
     * Remove property from favorites.
     */
    public function destroy(Property $property): RedirectResponse
    {
        auth()->user()->favorites()->detach($property->id);

        return back()->with('success', 'Properti dihapus dari favorit');
    }
}
