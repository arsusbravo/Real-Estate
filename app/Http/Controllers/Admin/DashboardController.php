<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PropertyStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Key metrics
        $metrics = [
            'total_properties' => Property::count(),
            'active_properties' => Property::where('status', PropertyStatus::ACTIVE)->count(),
            'pending_properties' => Property::where('status', PropertyStatus::PENDING_REVIEW)->count(),
            'total_users' => User::count(),
            'total_transactions' => Transaction::count(),
            'active_transactions' => Transaction::active()->count(),
            'completed_transactions' => Transaction::where('status', TransactionStatus::COMPLETED)->count(),
            'new_inquiries' => Inquiry::new()->count(),
        ];

        // Revenue this month
        $revenueThisMonth = Transaction::where('status', TransactionStatus::COMPLETED)
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->sum('commission_amount');

        // Recent transactions
        $recentTransactions = Transaction::with([
            'property:id,title,slug',
            'buyer:id,name',
            'seller:id,name',
        ])
            ->latest()
            ->take(5)
            ->get();

        // Pending approvals
        $pendingProperties = Property::where('status', PropertyStatus::PENDING_REVIEW)
            ->with(['seller:id,name', 'media'])
            ->latest()
            ->take(5)
            ->get();

        // Transaction status distribution
        $transactionsByStatus = Transaction::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Recent inquiries
        $recentInquiries = Inquiry::with(['property:id,title,slug'])
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'metrics' => $metrics,
            'revenueThisMonth' => $revenueThisMonth,
            'recentTransactions' => $recentTransactions,
            'pendingProperties' => $pendingProperties,
            'transactionsByStatus' => $transactionsByStatus,
            'recentInquiries' => $recentInquiries,
        ]);
    }
}
