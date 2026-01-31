<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, LogsActivity, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'whatsapp_number',
        'ktp_number',
        'address',
        'role',
        'avatar_path',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
        'ktp_number',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'role' => UserRole::class,
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'role'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships

    /**
     * Properties listed by this user (as seller)
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'seller_id');
    }

    /**
     * Transactions where user is the buyer
     */
    public function buyerTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    /**
     * Transactions where user is the seller
     */
    public function sellerTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    /**
     * Buyer requirements submitted by this user
     */
    public function requirements(): HasMany
    {
        return $this->hasMany(BuyerRequirement::class);
    }

    /**
     * Inquiries submitted by this user
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    /**
     * Properties favorited by this user
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'favorites')
            ->withTimestamps();
    }

    /**
     * Property viewings for this user (as buyer)
     */
    public function viewings(): HasMany
    {
        return $this->hasMany(PropertyViewing::class, 'buyer_id');
    }

    /**
     * Documents uploaded by this user
     */
    public function uploadedDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    /**
     * Documents belonging to this user (polymorphic)
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // Role checks

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isBuyer(): bool
    {
        return $this->role === UserRole::BUYER;
    }

    public function isSeller(): bool
    {
        return $this->role === UserRole::SELLER;
    }

    public function hasRole(UserRole|string $role): bool
    {
        if (is_string($role)) {
            $role = UserRole::from($role);
        }

        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    // Helpers

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';

        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path
            ? asset('storage/'.$this->avatar_path)
            : null;
    }

    public function getWhatsappLinkAttribute(): ?string
    {
        $number = $this->whatsapp_number ?? $this->phone;
        if (! $number) {
            return null;
        }

        // Format Indonesian number
        $number = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        } elseif (! str_starts_with($number, '62')) {
            $number = '62'.$number;
        }

        return "https://wa.me/{$number}";
    }

    /**
     * Check if this user has favorited a property
     */
    public function hasFavorited(Property $property): bool
    {
        return $this->favorites()->where('property_id', $property->id)->exists();
    }

    /**
     * Toggle favorite status for a property
     */
    public function toggleFavorite(Property $property): bool
    {
        if ($this->hasFavorited($property)) {
            $this->favorites()->detach($property->id);

            return false;
        }

        $this->favorites()->attach($property->id);

        return true;
    }

    /**
     * Get all transactions (as buyer or seller)
     */
    public function getAllTransactionsAttribute()
    {
        return Transaction::where('buyer_id', $this->id)
            ->orWhere('seller_id', $this->id)
            ->get();
    }

    /**
     * Get active transactions count
     */
    public function getActiveTransactionsCountAttribute(): int
    {
        return Transaction::where(function ($q) {
            $q->where('buyer_id', $this->id)
                ->orWhere('seller_id', $this->id);
        })
            ->active()
            ->count();
    }
}
