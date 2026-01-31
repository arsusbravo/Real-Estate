<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    /**
     * Store a new inquiry.
     */
    public function store(Request $request, Property $property): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'message' => ['required', 'string', 'max:1000'],
            'preferred_contact_method' => ['required', 'in:phone,whatsapp,email'],
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'message.required' => 'Pesan wajib diisi',
            'preferred_contact_method.required' => 'Pilih metode kontak yang diinginkan',
        ]);

        $inquiry = $property->inquiries()->create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Terima kasih! Pertanyaan Anda telah terkirim. Kami akan segera menghubungi Anda.');
    }
}
