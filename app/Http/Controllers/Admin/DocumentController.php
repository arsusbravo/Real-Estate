<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentStatus;
use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    /**
     * Display pending documents.
     */
    public function pending(): Response
    {
        $documents = Document::pending()
            ->with([
                'documentable',
                'uploader:id,name',
            ])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Documents/Pending', [
            'documents' => $documents,
        ]);
    }

    /**
     * Verify document.
     */
    public function verify(Request $request, Document $document): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $document->verify(auth()->id(), $validated['notes'] ?? null);

        return back()->with('success', 'Dokumen berhasil diverifikasi');
    }

    /**
     * Reject document.
     */
    public function reject(Request $request, Document $document): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Alasan penolakan wajib diisi',
        ]);

        $document->reject(auth()->id(), $validated['reason']);

        return back()->with('success', 'Dokumen ditolak');
    }
}
