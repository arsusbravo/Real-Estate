<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    /**
     * Display settings page.
     */
    public function index(): Response
    {
        // In a real app, these would come from a settings table
        $settings = [
            'commission_percentage' => config('app.commission_percentage', 2.5),
            'min_property_images' => 3,
            'max_property_images' => 20,
            'contact_email' => config('mail.from.address'),
            'contact_phone' => env('CONTACT_PHONE', ''),
            'contact_whatsapp' => env('CONTACT_WHATSAPP', ''),
        ];

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'contact_email' => ['required', 'email'],
            'contact_phone' => ['nullable', 'string'],
            'contact_whatsapp' => ['nullable', 'string'],
        ]);

        // In a real app, you would save these to a settings table
        // For now, this is just a placeholder

        return back()->with('success', 'Pengaturan berhasil disimpan');
    }
}
