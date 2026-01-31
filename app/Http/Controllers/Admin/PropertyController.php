<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PropertyStatus;
use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    /**
     * Display all properties.
     */
    public function index(Request $request): Response
    {
        $properties = Property::with(['seller:id,name,email', 'media'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%")
                ->orWhereHas('seller', fn ($sq) => $sq->where('name', 'like', "%{$search}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Properties/Index', [
            'properties' => $properties,
            'filters' => $request->only(['status', 'search']),
            'statusOptions' => PropertyStatus::cases(),
            'counts' => [
                'all' => Property::count(),
                'pending' => Property::where('status', PropertyStatus::PENDING_REVIEW)->count(),
                'active' => Property::where('status', PropertyStatus::ACTIVE)->count(),
            ],
        ]);
    }

    /**
     * Display pending properties for review.
     */
    public function pending(): Response
    {
        $properties = Property::where('status', PropertyStatus::PENDING_REVIEW)
            ->with(['seller:id,name,email,phone', 'media', 'features'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Properties/Pending', [
            'properties' => $properties,
        ]);
    }

    /**
     * Display property detail.
     */
    public function show(Property $property): Response
    {
        $property->load([
            'seller',
            'features',
            'media',
            'inquiries' => fn ($q) => $q->latest()->take(10),
            'transactions' => fn ($q) => $q->with('buyer:id,name')->latest(),
        ]);

        return Inertia::render('Admin/Properties/Show', [
            'property' => $property,
        ]);
    }

    /**
     * Update property.
     */
    public function update(Request $request, Property $property): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'featured' => ['sometimes', 'boolean'],
            'verified' => ['sometimes', 'boolean'],
        ]);

        $property->update($validated);

        return back()->with('success', 'Properti berhasil diperbarui');
    }

    /**
     * Approve property.
     */
    public function approve(Property $property): RedirectResponse
    {
        if ($property->status !== PropertyStatus::PENDING_REVIEW) {
            return back()->with('error', 'Hanya properti dengan status pending yang dapat di-approve');
        }

        $property->update([
            'status' => PropertyStatus::ACTIVE,
            'verified' => true,
            'published_at' => now(),
            'rejection_reason' => null,
        ]);

        // TODO: Send notification to seller

        return back()->with('success', 'Properti berhasil di-approve dan dipublish');
    }

    /**
     * Reject property.
     */
    public function reject(Request $request, Property $property): RedirectResponse
    {
        if ($property->status !== PropertyStatus::PENDING_REVIEW) {
            return back()->with('error', 'Hanya properti dengan status pending yang dapat ditolak');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Alasan penolakan wajib diisi',
        ]);

        $property->update([
            'status' => PropertyStatus::REJECTED,
            'rejection_reason' => $validated['reason'],
        ]);

        // TODO: Send notification to seller

        return back()->with('success', 'Properti ditolak');
    }

    /**
     * Toggle featured status.
     */
    public function feature(Property $property): RedirectResponse
    {
        $property->update([
            'featured' => ! $property->featured,
        ]);

        $message = $property->featured
            ? 'Properti ditandai sebagai featured'
            : 'Properti dihapus dari featured';

        return back()->with('success', $message);
    }

    /**
     * Delete property.
     */
    public function destroy(Property $property): RedirectResponse
    {
        // Check if property has active transactions
        if ($property->transactions()->active()->exists()) {
            return back()->with('error', 'Properti dengan transaksi aktif tidak dapat dihapus');
        }

        $property->clearMediaCollection('images');
        $property->delete();

        return redirect()->route('admin.properties.index')
            ->with('success', 'Properti berhasil dihapus');
    }
}
