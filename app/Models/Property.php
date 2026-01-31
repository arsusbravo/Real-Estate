<?php

namespace App\Models;

use App\Enums\CertificateType;
use App\Enums\FacingDirection;
use App\Enums\FurnishingType;
use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Property extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'seller_id',
        'title',
        'slug',
        'description',
        'property_type',
        'listing_type',
        'price',
        'price_negotiable',
        'land_area',
        'building_area',
        'bedrooms',
        'bathrooms',
        'floors',
        'parking_spaces',
        'furnishing',
        'facing_direction',
        'province',
        'city',
        'district',
        'subdistrict',
        'address',
        'postal_code',
        'latitude',
        'longitude',
        'certificate_type',
        'certificate_number',
        'certificate_expiry',
        'status',
        'featured',
        'verified',
        'rejection_reason',
        'published_at',
        'sold_at',
    ];

    protected $casts = [
        'property_type' => PropertyType::class,
        'listing_type' => ListingType::class,
        'furnishing' => FurnishingType::class,
        'facing_direction' => FacingDirection::class,
        'certificate_type' => CertificateType::class,
        'status' => PropertyStatus::class,
        'price' => 'decimal:2',
        'price_negotiable' => 'boolean',
        'featured' => 'boolean',
        'verified' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'certificate_expiry' => 'date',
        'published_at' => 'datetime',
        'sold_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Property $property) {
            $property->uuid = $property->uuid ?? Str::uuid();
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk('public');

        $this->addMediaCollection('documents')
            ->useDisk('private');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(800)
            ->sharpen(10)
            ->nonQueued();
    }

    // Relationships
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function features(): HasMany
    {
        return $this->hasMany(PropertyFeature::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function viewings(): HasMany
    {
        return $this->hasMany(PropertyViewing::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', PropertyStatus::ACTIVE);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeForSale($query)
    {
        return $query->where('listing_type', ListingType::DIJUAL);
    }

    public function scopeForRent($query)
    {
        return $query->where('listing_type', ListingType::DISEWAKAN);
    }

    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopePriceBetween($query, $min, $max)
    {
        return $query->when($min, fn ($q) => $q->where('price', '>=', $min))
            ->when($max, fn ($q) => $q->where('price', '<=', $max));
    }

    // Helpers
    public function getPriceFormattedAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->subdistrict,
            $this->district,
            $this->city,
            $this->province,
        ]);

        return implode(', ', $parts);
    }

    public function getPrimaryImageAttribute()
    {
        return $this->getFirstMedia('images');
    }

    public function isActive(): bool
    {
        return $this->status === PropertyStatus::ACTIVE;
    }

    public function isPendingReview(): bool
    {
        return $this->status === PropertyStatus::PENDING_REVIEW;
    }
}
