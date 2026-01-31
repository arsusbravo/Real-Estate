<?php

namespace App\Http\Controllers\Seller;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Upload document for transaction.
     */
    public function store(Request $request, Transaction $transaction): RedirectResponse
    {
        // Ensure seller owns this transaction
        if ($transaction->seller_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'document_type' => ['required', 'string'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // 10MB max
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'file.required' => 'File wajib diupload',
            'file.mimes' => 'Format file harus PDF, JPG, atau PNG',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents/transactions/'.$transaction->uuid, 'private');

        $document = $transaction->documents()->create([
            'uploaded_by' => auth()->id(),
            'document_type' => $validated['document_type'],
            'category' => 'seller_document',
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => DocumentStatus::UPLOADED,
            'notes' => $validated['notes'],
        ]);

        // Log activity
        $transaction->activities()->create([
            'user_id' => auth()->id(),
            'activity_type' => TransactionActivity::TYPE_DOCUMENT_UPLOADED,
            'description' => 'Dokumen '.DocumentType::from($validated['document_type'])->label().' telah diupload oleh penjual',
            'new_value' => ['document_id' => $document->id],
        ]);

        return back()->with('success', 'Dokumen berhasil diupload');
    }
}
