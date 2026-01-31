<?php

namespace App\Http\Controllers\Seller;

use App\Enums\CertificateType;
use App\Enums\FacingDirection;
use App\Enums\FurnishingType;
use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyFeature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    /**
     * Display list of seller's properties.
     */
    public function index(Request $request): Response
    {
        $properties = auth()->user()
            ->properties()
            ->with('media')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Seller/Properties/Index', [
            'properties' => $properties,
            'filters' => $request->only(['status', 'search']),
            'statusOptions' => PropertyStatus::cases(),
        ]);
    }

    /**
     * Show form to create new property.
     */
    public function create(): Response
    {
        return Inertia::render('Seller/Properties/Create', [
            'propertyTypes' => PropertyType::cases(),
            'listingTypes' => ListingType::cases(),
            'certificateTypes' => CertificateType::cases(),
            'furnishingTypes' => FurnishingType::cases(),
            'facingDirections' => FacingDirection::cases(),
            'availableFeatures' => PropertyFeature::availableFeatures(),
            'cities' => $this->getJakartaCities(),
        ]);
    }

    /**
     * Store new property.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:100', 'max:5000'],
            'property_type' => ['required', 'string'],
            'listing_type' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1000000'],
            'price_negotiable' => ['boolean'],
            'land_area' => ['nullable', 'integer', 'min:1'],
            'building_area' => ['nullable', 'integer', 'min:1'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'floors' => ['nullable', 'integer', 'min:1', 'max:100'],
            'parking_spaces' => ['nullable', 'integer', 'min:0'],
            'furnishing' => ['nullable', 'string'],
            'facing_direction' => ['nullable', 'string'],
            'city' => ['required', 'string'],
            'district' => ['nullable', 'string'],
            'subdistrict' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:500'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'certificate_type' => ['required', 'string'],
            'certificate_expiry' => ['nullable', 'date'],
            'features' => ['nullable', 'array'],
        ], [
            'title.required' => 'Judul properti wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'description.min' => 'Deskripsi minimal 100 karakter',
            'price.required' => 'Harga wajib diisi',
            'price.min' => 'Harga minimal Rp 1.000.000',
            'city.required' => 'Kota wajib dipilih',
            'address.required' => 'Alamat wajib diisi',
            'certificate_type.required' => 'Jenis sertifikat wajib dipilih',
        ]);

        $features = $validated['features'] ?? [];
        unset($validated['features']);

        $property = auth()->user()->properties()->create([
            ...$validated,
            'status' => PropertyStatus::DRAFT,
        ]);

        // Add features
        foreach ($features as $feature) {
            $property->features()->create(['feature_name' => $feature]);
        }

        return redirect()->route('seller.properties.edit', $property)
            ->with('success', 'Properti berhasil dibuat. Silakan upload foto properti.');
    }

    /**
     * Show form to edit property.
     */
    public function edit(Property $property): Response
    {
        $this->authorize('update', $property);

        $property->load(['features', 'media']);

        return Inertia::render('Seller/Properties/Edit', [
            'property' => $property,
            'propertyTypes' => PropertyType::cases(),
            'listingTypes' => ListingType::cases(),
            'certificateTypes' => CertificateType::cases(),
            'furnishingTypes' => FurnishingType::cases(),
            'facingDirections' => FacingDirection::cases(),
            'availableFeatures' => PropertyFeature::availableFeatures(),
            'cities' => $this->getJakartaCities(),
        ]);
    }

    /**
     * Update property.
     */
    public function update(Request $request, Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:100', 'max:5000'],
            'property_type' => ['required', 'string'],
            'listing_type' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1000000'],
            'price_negotiable' => ['boolean'],
            'land_area' => ['nullable', 'integer', 'min:1'],
            'building_area' => ['nullable', 'integer', 'min:1'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'floors' => ['nullable', 'integer', 'min:1', 'max:100'],
            'parking_spaces' => ['nullable', 'integer', 'min:0'],
            'furnishing' => ['nullable', 'string'],
            'facing_direction' => ['nullable', 'string'],
            'city' => ['required', 'string'],
            'district' => ['nullable', 'string'],
            'subdistrict' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:500'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'certificate_type' => ['required', 'string'],
            'certificate_expiry' => ['nullable', 'date'],
            'features' => ['nullable', 'array'],
        ]);

        $features = $validated['features'] ?? [];
        unset($validated['features']);

        $property->update($validated);

        // Sync features
        $property->features()->delete();
        foreach ($features as $feature) {
            $property->features()->create(['feature_name' => $feature]);
        }

        return back()->with('success', 'Properti berhasil diperbarui');
    }

    /**
     * Submit property for review.
     */
    public function submit(Property $property): RedirectResponse
    {
        $this->authorize('update', $property);

        // Validate property has minimum requirements
        if ($property->getMedia('images')->count() < 3) {
            return back()->with('error', 'Upload minimal 3 foto properti sebelum submit untuk review');
        }

        $property->update([
            'status' => PropertyStatus::PENDING_REVIEW,
        ]);

        return back()->with('success', 'Properti berhasil disubmit untuk review. Tim kami akan segera memeriksa listing Anda.');
    }

    /**
     * Delete property.
     */
    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        // Only draft or rejected properties can be deleted
        if (! in_array($property->status, [PropertyStatus::DRAFT, PropertyStatus::REJECTED])) {
            return back()->with('error', 'Properti aktif tidak dapat dihapus');
        }

        // Delete media
        $property->clearMediaCollection('images');

        $property->delete();

        return redirect()->route('seller.properties.index')
            ->with('success', 'Properti berhasil dihapus');
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
