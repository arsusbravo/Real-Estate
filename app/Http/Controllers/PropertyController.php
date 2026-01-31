<?php

namespace App\Http\Controllers;

use App\Enums\CertificateType;
use App\Enums\FurnishingType;
use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    /**
     * Display public property listing.
     */
    public function index(Request $request): Response
    {
        $properties = Property::query()
            ->active()
            ->with(['seller:id,name', 'media'])
            ->when($request->search, fn ($q, $search) => $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            }))
            ->when($request->property_type, fn ($q, $type) => $q->where('property_type', $type))
            ->when($request->listing_type, fn ($q, $listing) => $q->where('listing_type', $listing))
            ->when($request->city, fn ($q, $city) => $q->where('city', $city))
            ->when($request->min_price, fn ($q, $min) => $q->where('price', '>=', $min))
            ->when($request->max_price, fn ($q, $max) => $q->where('price', '<=', $max))
            ->when($request->min_land_area, fn ($q, $min) => $q->where('land_area', '>=', $min))
            ->when($request->max_land_area, fn ($q, $max) => $q->where('land_area', '<=', $max))
            ->when($request->min_building_area, fn ($q, $min) => $q->where('building_area', '>=', $min))
            ->when($request->max_building_area, fn ($q, $max) => $q->where('building_area', '<=', $max))
            ->when($request->bedrooms, fn ($q, $bedrooms) => $q->where('bedrooms', '>=', $bedrooms))
            ->when($request->bathrooms, fn ($q, $bathrooms) => $q->where('bathrooms', '>=', $bathrooms))
            ->when($request->certificate_type, fn ($q, $cert) => $q->where('certificate_type', $cert))
            ->when($request->furnishing, fn ($q, $furnishing) => $q->where('furnishing', $furnishing))
            ->when($request->sort, function ($q, $sort) {
                return match ($sort) {
                    'price_asc' => $q->orderBy('price', 'asc'),
                    'price_desc' => $q->orderBy('price', 'desc'),
                    'oldest' => $q->orderBy('created_at', 'asc'),
                    'land_area' => $q->orderBy('land_area', 'desc'),
                    default => $q->orderBy('created_at', 'desc'),
                };
            }, fn ($q) => $q->orderBy('created_at', 'desc'))
            ->paginate(12)
            ->withQueryString();

        $filterKeys = [
            'search', 'property_type', 'listing_type', 'city',
            'min_price', 'max_price', 'min_land_area', 'max_land_area',
            'min_building_area', 'max_building_area',
            'bedrooms', 'bathrooms', 'certificate_type', 'furnishing', 'sort',
        ];

        return Inertia::render('Public/Properties/Index', [
            'properties' => $properties,
            'filters' => $request->only($filterKeys),
            'propertyTypes' => collect(PropertyType::cases())->map(fn ($t) => ['value' => $t->value, 'name' => $t->label()]),
            'listingTypes' => collect(ListingType::cases())->map(fn ($t) => ['value' => $t->value, 'name' => $t->label()]),
            'certificateTypes' => collect(CertificateType::cases())->map(fn ($t) => ['value' => $t->value, 'name' => $t->label()]),
            'furnishingTypes' => collect(FurnishingType::cases())->map(fn ($t) => ['value' => $t->value, 'name' => $t->label()]),
            'cities' => collect($this->getJakartaCities())->map(fn ($c) => ['value' => $c, 'name' => $c]),
        ]);
    }

    /**
     * Display property detail.
     */
    public function show(Property $property): Response
    {
        // Only show active or sold properties publicly
        if (! in_array($property->status, [PropertyStatus::ACTIVE, PropertyStatus::SOLD, PropertyStatus::RENTED])) {
            abort(404);
        }

        // Increment view count
        $property->increment('views_count');

        $property->load([
            'seller:id,name,phone,whatsapp_number,avatar_path',
            'features',
            'media',
        ]);

        // Get related properties
        $relatedProperties = Property::query()
            ->active()
            ->where('id', '!=', $property->id)
            ->where(function ($q) use ($property) {
                $q->where('city', $property->city)
                    ->orWhere('property_type', $property->property_type);
            })
            ->with('media')
            ->limit(4)
            ->get();

        return Inertia::render('Public/Properties/Show', [
            'property' => $property,
            'relatedProperties' => $relatedProperties,
            'isFavorited' => auth()->check() ? auth()->user()->hasFavorited($property) : false,
        ]);
    }

    /**
     * Get Jakarta area cities/districts.
     */
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
