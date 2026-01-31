<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    /**
     * Display list of seller's transactions.
     */
    public function index(): Response
    {
        $transactions = auth()->user()
            ->sellerTransactions()
            ->with([
                'property.media',
                'buyer:id,name,email,phone',
                'notary:id,name,office_name',
            ])
            ->latest()
            ->paginate(10);

        return Inertia::render('Seller/Transactions/Index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Display transaction detail.
     */
    public function show(Transaction $transaction): Response
    {
        // Ensure seller owns this transaction
        if ($transaction->seller_id !== auth()->id()) {
            abort(403);
        }

        $transaction->load([
            'property.media',
            'property.features',
            'buyer:id,name,phone,whatsapp_number,email',
            'notary',
            'activities.user:id,name',
            'documents' => fn ($q) => $q->where('category', 'seller_document'),
            'viewings',
        ]);

        // Get required documents checklist
        $requiredDocuments = $this->getSellerDocumentChecklist($transaction->property->certificate_type);

        return Inertia::render('Seller/Transactions/Show', [
            'transaction' => $transaction,
            'requiredDocuments' => $requiredDocuments,
            'uploadedDocumentTypes' => $transaction->documents->pluck('document_type')->toArray(),
        ]);
    }

    private function getSellerDocumentChecklist(string $certificateType): array
    {
        $documents = [
            ['type' => 'ktp_seller', 'label' => 'KTP Penjual', 'required' => true],
            ['type' => 'kk_seller', 'label' => 'Kartu Keluarga', 'required' => true],
            ['type' => 'npwp_seller', 'label' => 'NPWP', 'required' => true],
            ['type' => 'marriage_cert_seller', 'label' => 'Akta Nikah (jika menikah)', 'required' => false],
            ['type' => 'spouse_consent_seller', 'label' => 'Surat Persetujuan Pasangan', 'required' => false],
            ['type' => $certificateType, 'label' => 'Sertifikat Asli', 'required' => true],
            ['type' => 'pbb', 'label' => 'PBB 5 Tahun Terakhir', 'required' => true],
            ['type' => 'imb', 'label' => 'IMB/PBG', 'required' => false],
            ['type' => 'ajb_previous', 'label' => 'AJB Sebelumnya', 'required' => false],
        ];

        return $documents;
    }
}
