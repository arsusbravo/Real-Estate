<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        // Get buyer's active transactions
        $activeTransactions = $user->buyerTransactions()
            ->with(['property.media', 'seller:id,name'])
            ->active()
            ->latest()
            ->take(5)
            ->get();

        // Get recent favorites
        $recentFavorites = $user->favorites()
            ->with('media')
            ->latest('favorites.created_at')
            ->take(4)
            ->get();

        // Get buyer requirements with matching count
        $requirements = $user->requirements()
            ->active()
            ->latest()
            ->get()
            ->map(function ($requirement) {
                $requirement->matching_count = $requirement->matchingProperties()->count();

                return $requirement;
            });

        // Recommended properties based on requirements
        $recommendedProperties = collect();
        if ($requirements->isNotEmpty()) {
            $requirement = $requirements->first();
            $recommendedProperties = $requirement->matchingProperties()
                ->with('media')
                ->take(4)
                ->get();
        }

        return Inertia::render('Buyer/Dashboard', [
            'activeTransactions' => $activeTransactions,
            'recentFavorites' => $recentFavorites,
            'requirements' => $requirements,
            'recommendedProperties' => $recommendedProperties,
            'stats' => [
                'active_transactions' => $user->buyerTransactions()->active()->count(),
                'completed_transactions' => $user->buyerTransactions()->completed()->count(),
                'favorites_count' => $user->favorites()->count(),
                'requirements_count' => $user->requirements()->active()->count(),
            ],
        ]);
    }
}
