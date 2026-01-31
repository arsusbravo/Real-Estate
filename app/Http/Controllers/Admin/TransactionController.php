<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Models\NotaryPartner;
use App\Models\Property;
use App\Models\Transaction;
use App\Models\TransactionActivity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    /**
     * Display all transactions.
     */
    public function index(Request $request): Response
    {
        $transactions = Transaction::with([
            'property:id,title,slug,price',
            'buyer:id,name,email',
            'seller:id,name,email',
            'notary:id,name',
        ])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->search, fn ($q, $search) => $q->where('transaction_number', 'like', "%{$search}%")
                ->orWhereHas('property', fn ($sq) => $sq->where('title', 'like', "%{$search}%"))
                ->orWhereHas('buyer', fn ($sq) => $sq->where('name', 'like', "%{$search}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Transactions/Index', [
            'transactions' => $transactions,
            'filters' => $request->only(['status', 'search']),
            'statusOptions' => TransactionStatus::cases(),
            'counts' => [
                'all' => Transaction::count(),
                'active' => Transaction::active()->count(),
                'completed' => Transaction::where('status', TransactionStatus::COMPLETED)->count(),
            ],
        ]);
    }

    /**
     * Show form to create new transaction.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Transactions/Create', [
            'properties' => Property::active()->with('seller:id,name')->get(['id', 'title', 'price', 'seller_id']),
            'buyers' => User::where('role', 'buyer')->get(['id', 'name', 'email']),
            'notaries' => NotaryPartner::active()->get(['id', 'name', 'office_name']),
        ]);
    }

    /**
     * Store new transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'buyer_id' => ['required', 'exists:users,id'],
            'agreed_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $property = Property::findOrFail($validated['property_id']);

        $transaction = Transaction::create([
            'property_id' => $property->id,
            'buyer_id' => $validated['buyer_id'],
            'seller_id' => $property->seller_id,
            'agreed_price' => $validated['agreed_price'],
            'notes' => $validated['notes'],
            'status' => TransactionStatus::INQUIRY,
            'inquiry_date' => now(),
            'assigned_admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.transactions.show', $transaction)
            ->with('success', 'Transaksi berhasil dibuat');
    }

    /**
     * Display transaction detail.
     */
    public function show(Transaction $transaction): Response
    {
        $transaction->load([
            'property.media',
            'property.features',
            'buyer',
            'seller',
            'notary',
            'assignedAdmin:id,name',
            'activities.user:id,name',
            'documents.uploader:id,name',
            'viewings.buyer:id,name',
        ]);

        return Inertia::render('Admin/Transactions/Show', [
            'transaction' => $transaction,
            'statusOptions' => TransactionStatus::cases(),
            'notaries' => NotaryPartner::active()->get(['id', 'name', 'office_name']),
            'allowedTransitions' => $transaction->status->allowedTransitions(),
        ]);
    }

    /**
     * Update transaction.
     */
    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'agreed_price' => ['nullable', 'numeric', 'min:0'],
            'dp_amount' => ['nullable', 'numeric', 'min:0'],
            'commission_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $transaction->update($validated);

        return back()->with('success', 'Transaksi berhasil diperbarui');
    }

    /**
     * Update transaction status.
     */
    public function updateStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $newStatus = TransactionStatus::from($validated['status']);

        if (! $transaction->canTransitionTo($newStatus)) {
            return back()->with('error', 'Perubahan status tidak diizinkan');
        }

        $transaction->transitionTo($newStatus, $validated['notes']);

        return back()->with('success', 'Status transaksi berhasil diperbarui');
    }

    /**
     * Assign notary to transaction.
     */
    public function assignNotary(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'notary_id' => ['required', 'exists:notary_partners,id'],
        ]);

        $transaction->update([
            'notary_id' => $validated['notary_id'],
            'notary_assigned_at' => now(),
        ]);

        $notary = NotaryPartner::find($validated['notary_id']);

        // Log activity
        $transaction->activities()->create([
            'user_id' => auth()->id(),
            'activity_type' => TransactionActivity::TYPE_NOTARY_ASSIGNED,
            'description' => "Notaris {$notary->name} ditugaskan untuk transaksi ini",
            'new_value' => ['notary_id' => $notary->id, 'notary_name' => $notary->name],
        ]);

        return back()->with('success', 'Notaris berhasil ditugaskan');
    }

    /**
     * Add activity note.
     */
    public function addActivity(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['required', 'string', 'max:1000'],
        ]);

        $transaction->activities()->create([
            'user_id' => auth()->id(),
            'activity_type' => TransactionActivity::TYPE_NOTE_ADDED,
            'description' => $validated['description'],
        ]);

        return back()->with('success', 'Catatan berhasil ditambahkan');
    }
}
