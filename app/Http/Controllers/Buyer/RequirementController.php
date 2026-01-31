<?php

namespace App\Http\Controllers\Buyer;

use App\Enums\CertificateType;
use App\Enums\ListingType;
use App\Enums\PropertyType;
use App\Enums\RequirementUrgency;
use App\Http\Controllers\Controller;
use App\Models\BuyerRequirement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RequirementController extends Controller
{
    /**
     * Display list of buyer's requirements.
     */
    public function index(): Response
    {
        $requirements = auth()->user()
            ->requirements()
            ->latest()
            ->get()
            ->map(function ($requirement) {
                $requirement->matching_count = $requirement->matchingProperties()->count();

                return $requirement;
            });

        return Inertia::render('Buyer/Requirements/Index', [
            'requirements' => $requirements,
        ]);
    }

    /**
     * Show form to create new requirement.
     */
    public function create(): Response
    {
        return Inertia::render('Buyer/Requirements/Create', [
            'propertyTypes' => PropertyType::cases(),
            'listingTypes' => ListingType::cases(),
            'certificateTypes' => CertificateType::cases(),
            'urgencyOptions' => RequirementUrgency::cases(),
            'cities' => $this->getJakartaCities(),
        ]);
    }

    /**
     * Store new requirement.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_types' => ['required', 'array', 'min:1'],
            'property_types.*' => ['required', 'string'],
            'listing_type' => ['required', 'string'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'min_land_area' => ['nullable', 'integer', 'min:0'],
            'max_land_area' => ['nullable', 'integer', 'min:0'],
            'min_building_area' => ['nullable', 'integer', 'min:0'],
            'max_building_area' => ['nullable', 'integer', 'min:0'],
            'min_bedrooms' => ['nullable', 'integer', 'min:0'],
            'min_bathrooms' => ['nullable', 'integer', 'min:0'],
            'preferred_locations' => ['nullable', 'array'],
            'preferred_certificate_types' => ['nullable', 'array'],
            'urgency' => ['required', 'string'],
            'additional_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'property_types.required' => 'Pilih minimal satu tipe properti',
            'listing_type.required' => 'Pilih tipe listing (beli/sewa)',
            'max_price.gte' => 'Budget maksimal harus lebih besar dari budget minimal',
        ]);

        auth()->user()->requirements()->create($validated);

        return redirect()->route('buyer.requirements.index')
            ->with('success', 'Formulir kebutuhan berhasil disimpan');
    }

    /**
     * Show form to edit requirement.
     */
    public function edit(BuyerRequirement $requirement): Response
    {
        $this->authorize('update', $requirement);

        return Inertia::render('Buyer/Requirements/Edit', [
            'requirement' => $requirement,
            'propertyTypes' => PropertyType::cases(),
            'listingTypes' => ListingType::cases(),
            'certificateTypes' => CertificateType::cases(),
            'urgencyOptions' => RequirementUrgency::cases(),
            'cities' => $this->getJakartaCities(),
            'matchingProperties' => $requirement->matchingProperties()
                ->with('media')
                ->take(6)
                ->get(),
        ]);
    }

    /**
     * Update requirement.
     */
    public function update(Request $request, BuyerRequirement $requirement): RedirectResponse
    {
        $this->authorize('update', $requirement);

        $validated = $request->validate([
            'property_types' => ['required', 'array', 'min:1'],
            'property_types.*' => ['required', 'string'],
            'listing_type' => ['required', 'string'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'min_land_area' => ['nullable', 'integer', 'min:0'],
            'max_land_area' => ['nullable', 'integer', 'min:0'],
            'min_building_area' => ['nullable', 'integer', 'min:0'],
            'max_building_area' => ['nullable', 'integer', 'min:0'],
            'min_bedrooms' => ['nullable', 'integer', 'min:0'],
            'min_bathrooms' => ['nullable', 'integer', 'min:0'],
            'preferred_locations' => ['nullable', 'array'],
            'preferred_certificate_types' => ['nullable', 'array'],
            'urgency' => ['required', 'string'],
            'additional_notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'string', 'in:active,closed'],
        ]);

        $requirement->update($validated);

        return redirect()->route('buyer.requirements.index')
            ->with('success', 'Formulir kebutuhan berhasil diperbarui');
    }

    /**
     * Delete requirement.
     */
    public function destroy(BuyerRequirement $requirement): RedirectResponse
    {
        $this->authorize('delete', $requirement);

        $requirement->delete();

        return redirect()->route('buyer.requirements.index')
            ->with('success', 'Formulir kebutuhan berhasil dihapus');
    }

    private function getJakartaCities(): array
    {
        return [
            'Jakarta Pusat',
            'Jakarta Utara',
            'Jakarta Barat',
            'Jakarta Selatan',
            'Jakarta Timur',
            'Kepulauan Seribu',
            'Tangerang',
            'Tangerang Selatan',
            'Bekasi',
            'Depok',
            'Bogor',
        ];
    }
}
