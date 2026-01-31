<?php

namespace App\Http\Controllers\Buyer;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Transaction;
use App\Models\TransactionActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Upload document for transaction.
     */
    public function store(Request $request, Transaction $transaction): RedirectResponse
    {
        // Ensure buyer owns this transaction
        if ($transaction->buyer_id !== auth()->id()) {
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
            'category' => 'buyer_document',
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
            'description' => 'Dokumen '.DocumentType::from($validated['document_type'])->label().' telah diupload',
            'new_value' => ['document_id' => $document->id],
        ]);

        return back()->with('success', 'Dokumen berhasil diupload');
    }

    /**
     * Delete document.
     */
    public function destroy(Document $document): RedirectResponse
    {
        // Ensure user owns this document
        if ($document->uploaded_by !== auth()->id()) {
            abort(403);
        }

        // Can only delete pending documents
        if ($document->status === DocumentStatus::VERIFIED) {
            return back()->with('error', 'Dokumen yang sudah diverifikasi tidak dapat dihapus');
        }

        // Delete file from storage
        Storage::disk('private')->delete($document->file_path);

        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus');
    }
}
