<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InquiryStatus;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InquiryController extends Controller
{
    /**
     * Display all inquiries.
     */
    public function index(Request $request): Response
    {
        $inquiries = Inquiry::with([
            'property:id,title,slug',
            'property.media',
            'user:id,name,email',
        ])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('property', fn ($sq) => $sq->where('title', 'like', "%{$search}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Inquiries/Index', [
            'inquiries' => $inquiries,
            'filters' => $request->only(['status', 'search']),
            'statusOptions' => InquiryStatus::cases(),
            'counts' => [
                'all' => Inquiry::count(),
                'new' => Inquiry::where('status', InquiryStatus::NEW)->count(),
            ],
        ]);
    }

    /**
     * Display inquiry detail.
     */
    public function show(Inquiry $inquiry): Response
    {
        $inquiry->load(['property.media', 'property.seller:id,name', 'user', 'transaction']);

        return Inertia::render('Admin/Inquiries/Show', [
            'inquiry' => $inquiry,
            'buyers' => User::where('role', 'buyer')->get(['id', 'name', 'email']),
        ]);
    }

    /**
     * Update inquiry status.
     */
    public function updateStatus(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $inquiry->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $inquiry->admin_notes,
        ]);

        return back()->with('success', 'Status inquiry berhasil diperbarui');
    }

    /**
     * Convert inquiry to transaction.
     */
    public function convertToTransaction(Request $request, Inquiry $inquiry): RedirectResponse
    {
        if ($inquiry->isConverted()) {
            return back()->with('error', 'Inquiry sudah dikonversi ke transaksi');
        }

        $validated = $request->validate([
            'buyer_id' => ['required', 'exists:users,id'],
        ]);

        // Use existing user or the one specified
        $buyerId = $inquiry->user_id ?? $validated['buyer_id'];

        $transaction = $inquiry->convertToTransaction($buyerId);

        return redirect()->route('admin.transactions.show', $transaction)
            ->with('success', 'Inquiry berhasil dikonversi ke transaksi');
    }
}
