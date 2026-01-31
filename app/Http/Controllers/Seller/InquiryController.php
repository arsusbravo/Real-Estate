<?php

namespace App\Http\Controllers\Seller;

use App\Enums\InquiryStatus;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Inertia\Inertia;
use Inertia\Response;

class InquiryController extends Controller
{
    /**
     * Display list of inquiries for seller's properties.
     */
    public function index(): Response
    {
        $propertyIds = auth()->user()->properties()->pluck('id');

        $inquiries = Inquiry::whereIn('property_id', $propertyIds)
            ->with(['property.media', 'user:id,name,email'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Seller/Inquiries/Index', [
            'inquiries' => $inquiries,
            'newCount' => Inquiry::whereIn('property_id', $propertyIds)
                ->where('status', InquiryStatus::NEW)
                ->count(),
        ]);
    }

    /**
     * Display inquiry detail.
     */
    public function show(Inquiry $inquiry): Response
    {
        // Ensure seller owns the property
        if ($inquiry->property->seller_id !== auth()->id()) {
            abort(403);
        }

        $inquiry->load(['property.media', 'user', 'transaction']);

        // Mark as contacted if still new
        if ($inquiry->status === InquiryStatus::NEW) {
            $inquiry->update(['status' => InquiryStatus::CONTACTED]);
        }

        return Inertia::render('Seller/Inquiries/Show', [
            'inquiry' => $inquiry,
        ]);
    }
}
