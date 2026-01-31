<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotaryPartner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotaryController extends Controller
{
    /**
     * Display all notary partners.
     */
    public function index(Request $request): Response
    {
        $query = NotaryPartner::withCount([
            'transactions',
            'transactions as active_transactions_count' => fn ($q) => $q->active(),
        ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('office_name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->input('active') !== null) {
            $query->where('is_active', $request->boolean('active'));
        }

        $notaries = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Notaries/Index', [
            'notaries' => $notaries,
            'filters' => [
                'search' => $request->input('search', ''),
                'active' => $request->input('active'),
            ],
        ]);
    }

    /**
     * Show form to create new notary partner.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Notaries/Form', [
            'notary' => null,
        ]);
    }

    /**
     * Store new notary partner.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'office_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'city' => ['required', 'string'],
            'specializations' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama notaris wajib diisi',
            'city.required' => 'Kota wajib dipilih',
        ]);

        NotaryPartner::create($validated);

        return redirect()->route('admin.notaries.index')
            ->with('success', 'Partner notaris berhasil ditambahkan');
    }

    /**
     * Show form to edit notary partner.
     */
    public function edit(NotaryPartner $notary): Response
    {
        return Inertia::render('Admin/Notaries/Form', [
            'notary' => $notary,
        ]);
    }

    /**
     * Update notary partner.
     */
    public function update(Request $request, NotaryPartner $notary): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'office_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'city' => ['required', 'string'],
            'specializations' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $notary->update($validated);

        return redirect()->route('admin.notaries.index')
            ->with('success', 'Partner notaris berhasil diperbarui');
    }

    /**
     * Delete notary partner.
     */
    public function destroy(NotaryPartner $notary): RedirectResponse
    {
        // Check if notary has active transactions
        if ($notary->transactions()->active()->exists()) {
            return back()->with('error', 'Notaris dengan transaksi aktif tidak dapat dihapus');
        }

        $notary->delete();

        return redirect()->route('admin.notaries.index')
            ->with('success', 'Partner notaris berhasil dihapus');
    }
}
