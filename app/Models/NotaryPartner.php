<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotaryPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'license_number',
        'office_name',
        'address',
        'phone',
        'email',
        'city',
        'specializations',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'specializations' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'notary_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    // Helpers
    public function getDisplayNameAttribute(): string
    {
        if ($this->office_name) {
            return "{$this->name} ({$this->office_name})";
        }

        return $this->name;
    }

    public function getSpecializationsListAttribute(): string
    {
        if (empty($this->specializations)) {
            return '-';
        }

        return implode(', ', $this->specializations);
    }

    public function getActiveTransactionsCountAttribute(): int
    {
        return $this->transactions()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
    }
}
