<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PropertyStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Transaction reports.
     */
    public function transactions(Request $request): Response
    {
        $startDate = $request->start_date ?? now()->startOfMonth();
        $endDate = $request->end_date ?? now()->endOfMonth();

        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->with(['property:id,title,price', 'buyer:id,name', 'seller:id,name'])
            ->latest()
            ->get();

        // Summary stats
        $summary = [
            'total_transactions' => $transactions->count(),
            'completed_transactions' => $transactions->where('status', TransactionStatus::COMPLETED)->count(),
            'cancelled_transactions' => $transactions->where('status', TransactionStatus::CANCELLED)->count(),
            'total_value' => $transactions->sum('agreed_price'),
            'total_commission' => $transactions->where('status', TransactionStatus::COMPLETED)->sum('commission_amount'),
        ];

        // Monthly trend
        $monthlyTrend = Transaction::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count'),
            DB::raw('sum(agreed_price) as total_value')
        )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return Inertia::render('Admin/Reports/Transactions', [
            'transactions' => $transactions,
            'summary' => $summary,
            'monthlyTrend' => $monthlyTrend,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Property reports.
     */
    public function properties(Request $request): Response
    {
        // Property stats by status
        $byStatus = Property::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Property stats by type
        $byType = Property::select('property_type', DB::raw('count(*) as count'))
            ->groupBy('property_type')
            ->get()
            ->pluck('count', 'property_type');

        // Property stats by city
        $byCity = Property::select('city', DB::raw('count(*) as count'))
            ->groupBy('city')
            ->orderByDesc('count')
            ->get();

        // Top viewed properties
        $topViewed = Property::active()
            ->with('media')
            ->orderByDesc('views_count')
            ->take(10)
            ->get();

        // Most inquired properties
        $mostInquired = Property::active()
            ->with('media')
            ->orderByDesc('inquiries_count')
            ->take(10)
            ->get();

        return Inertia::render('Admin/Reports/Properties', [
            'byStatus' => $byStatus,
            'byType' => $byType,
            'byCity' => $byCity,
            'topViewed' => $topViewed,
            'mostInquired' => $mostInquired,
            'totals' => [
                'all' => Property::count(),
                'active' => Property::where('status', PropertyStatus::ACTIVE)->count(),
                'total_views' => Property::sum('views_count'),
                'total_inquiries' => Property::sum('inquiries_count'),
            ],
        ]);
    }

    /**
     * Revenue reports.
     */
    public function revenue(Request $request): Response
    {
        $year = $request->year ?? now()->year;

        // Monthly revenue
        $monthlyRevenue = Transaction::select(
            DB::raw('DATE_FORMAT(completed_at, "%m") as month'),
            DB::raw('sum(commission_amount) as revenue'),
            DB::raw('count(*) as transactions')
        )
            ->where('status', TransactionStatus::COMPLETED)
            ->whereYear('completed_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Total revenue this year
        $totalRevenue = Transaction::where('status', TransactionStatus::COMPLETED)
            ->whereYear('completed_at', $year)
            ->sum('commission_amount');

        // Total transaction value
        $totalValue = Transaction::where('status', TransactionStatus::COMPLETED)
            ->whereYear('completed_at', $year)
            ->sum('agreed_price');

        // Compare with last year
        $lastYearRevenue = Transaction::where('status', TransactionStatus::COMPLETED)
            ->whereYear('completed_at', $year - 1)
            ->sum('commission_amount');

        return Inertia::render('Admin/Reports/Revenue', [
            'monthlyRevenue' => $monthlyRevenue,
            'totalRevenue' => $totalRevenue,
            'totalValue' => $totalValue,
            'lastYearRevenue' => $lastYearRevenue,
            'year' => $year,
            'availableYears' => range(now()->year, now()->year - 5),
        ]);
    }

    /**
     * Export report.
     */
    public function export(Request $request, string $type): StreamedResponse
    {
        // Simple CSV export
        $filename = "{$type}_report_".now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'transactions') {
                fputcsv($handle, ['No. Transaksi', 'Properti', 'Pembeli', 'Penjual', 'Harga', 'Komisi', 'Status', 'Tanggal']);

                Transaction::with(['property:id,title', 'buyer:id,name', 'seller:id,name'])
                    ->chunk(100, function ($transactions) use ($handle) {
                        foreach ($transactions as $t) {
                            fputcsv($handle, [
                                $t->transaction_number,
                                $t->property->title ?? '-',
                                $t->buyer->name ?? '-',
                                $t->seller->name ?? '-',
                                $t->agreed_price ?? '-',
                                $t->commission_amount ?? '-',
                                $t->status->label(),
                                $t->created_at->format('Y-m-d'),
                            ]);
                        }
                    });
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
