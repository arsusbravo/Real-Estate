<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    /**
     * Display list of buyer's transactions.
     */
    public function index(): Response
    {
        $transactions = auth()->user()
            ->buyerTransactions()
            ->with([
                'property.media',
                'seller:id,name',
                'notary:id,name,office_name',
            ])
            ->latest()
            ->paginate(10);

        return Inertia::render('Buyer/Transactions/Index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Display transaction detail.
     */
    public function show(Transaction $transaction): Response
    {
        // Ensure buyer owns this transaction
        if ($transaction->buyer_id !== auth()->id()) {
            abort(403);
        }

        $transaction->load([
            'property.media',
            'property.features',
            'seller:id,name,phone,whatsapp_number,email',
            'notary',
            'activities.user:id,name',
            'documents' => fn ($q) => $q->where('category', 'buyer_document'),
            'viewings',
        ]);

        // Get required documents checklist
        $requiredDocuments = $this->getBuyerDocumentChecklist();

        return Inertia::render('Buyer/Transactions/Show', [
            'transaction' => $transaction,
            'requiredDocuments' => $requiredDocuments,
            'uploadedDocumentTypes' => $transaction->documents->pluck('document_type')->toArray(),
        ]);
    }

    private function getBuyerDocumentChecklist(): array
    {
        return [
            ['type' => 'ktp_buyer', 'label' => 'KTP Pembeli', 'required' => true],
            ['type' => 'kk_buyer', 'label' => 'Kartu Keluarga', 'required' => true],
            ['type' => 'npwp_buyer', 'label' => 'NPWP', 'required' => true],
            ['type' => 'marriage_cert_buyer', 'label' => 'Akta Nikah (jika menikah)', 'required' => false],
            ['type' => 'spouse_consent_buyer', 'label' => 'Surat Persetujuan Pasangan', 'required' => false],
        ];
    }
}
