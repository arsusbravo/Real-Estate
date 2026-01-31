<?php

namespace App\Http\Controllers\Seller;

use App\Enums\PropertyStatus;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        // Get seller's property stats
        $propertyStats = [
            'total' => $user->properties()->count(),
            'active' => $user->properties()->where('status', PropertyStatus::ACTIVE)->count(),
            'pending' => $user->properties()->where('status', PropertyStatus::PENDING_REVIEW)->count(),
            'sold' => $user->properties()->where('status', PropertyStatus::SOLD)->count(),
        ];

        // Get recent properties
        $recentProperties = $user->properties()
            ->with('media')
            ->latest()
            ->take(5)
            ->get();

        // Get recent inquiries
        $recentInquiries = $user->properties()
            ->with('inquiries')
            ->get()
            ->pluck('inquiries')
            ->flatten()
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        // Get active transactions
        $activeTransactions = $user->sellerTransactions()
            ->with(['property.media', 'buyer:id,name'])
            ->active()
            ->latest()
            ->take(5)
            ->get();

        // Calculate total views
        $totalViews = $user->properties()->sum('views_count');
        $totalInquiries = $user->properties()->sum('inquiries_count');

        return Inertia::render('Seller/Dashboard', [
            'propertyStats' => $propertyStats,
            'recentProperties' => $recentProperties,
            'recentInquiries' => $recentInquiries,
            'activeTransactions' => $activeTransactions,
            'stats' => [
                'total_views' => $totalViews,
                'total_inquiries' => $totalInquiries,
                'active_transactions' => $user->sellerTransactions()->active()->count(),
                'completed_transactions' => $user->sellerTransactions()->completed()->count(),
            ],
        ]);
    }
}
