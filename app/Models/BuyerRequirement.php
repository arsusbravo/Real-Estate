<?php

namespace App\Models;

use App\Enums\ListingType;
use App\Enums\RequirementUrgency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BuyerRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_types',
        'listing_type',
        'min_price',
        'max_price',
        'min_land_area',
        'max_land_area',
        'min_building_area',
        'max_building_area',
        'min_bedrooms',
        'min_bathrooms',
        'preferred_locations',
        'preferred_certificate_types',
        'urgency',
        'additional_notes',
        'status',
    ];

    protected $casts = [
        'property_types' => 'array',
        'listing_type' => ListingType::class,
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'preferred_locations' => 'array',
        'preferred_certificate_types' => 'array',
        'urgency' => RequirementUrgency::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (BuyerRequirement $requirement) {
            $requirement->uuid = $requirement->uuid ?? Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helpers
    public function getBudgetRangeAttribute(): string
    {
        $min = $this->min_price ? 'Rp '.number_format($this->min_price, 0, ',', '.') : '-';
        $max = $this->max_price ? 'Rp '.number_format($this->max_price, 0, ',', '.') : '-';

        return "{$min} - {$max}";
    }

    public function getPropertyTypesListAttribute(): string
    {
        if (empty($this->property_types)) {
            return '-';
        }

        return implode(', ', array_map(fn ($type) => ucfirst($type), $this->property_types));
    }

    public function getPreferredLocationsListAttribute(): string
    {
        if (empty($this->preferred_locations)) {
            return '-';
        }

        return implode(', ', $this->preferred_locations);
    }

    public function matchingProperties()
    {
        return Property::query()
            ->active()
            ->when($this->property_types, fn ($q) => $q->whereIn('property_type', $this->property_types))
            ->when($this->listing_type, fn ($q) => $q->where('listing_type', $this->listing_type))
            ->when($this->min_price, fn ($q) => $q->where('price', '>=', $this->min_price))
            ->when($this->max_price, fn ($q) => $q->where('price', '<=', $this->max_price))
            ->when($this->min_bedrooms, fn ($q) => $q->where('bedrooms', '>=', $this->min_bedrooms))
            ->when($this->min_bathrooms, fn ($q) => $q->where('bathrooms', '>=', $this->min_bathrooms))
            ->when($this->preferred_locations, fn ($q) => $q->whereIn('city', $this->preferred_locations))
            ->when($this->preferred_certificate_types, fn ($q) => $q->whereIn('certificate_type', $this->preferred_certificate_types));
    }
}
