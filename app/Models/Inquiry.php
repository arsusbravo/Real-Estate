<?php

namespace App\Models;

use App\Enums\InquiryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'name',
        'email',
        'phone',
        'whatsapp',
        'message',
        'preferred_contact_method',
        'status',
        'converted_to_transaction_id',
        'admin_notes',
    ];

    protected $casts = [
        'status' => InquiryStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Inquiry $inquiry) {
            $inquiry->uuid = $inquiry->uuid ?? Str::uuid();
        });

        static::created(function (Inquiry $inquiry) {
            // Increment property inquiries count
            $inquiry->property?->increment('inquiries_count');
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'converted_to_transaction_id');
    }

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', InquiryStatus::NEW);
    }

    public function scopeForProperty($query, int $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    // Actions
    public function markAsContacted(): void
    {
        $this->update(['status' => InquiryStatus::CONTACTED]);
    }

    public function convertToTransaction(int $buyerId): Transaction
    {
        $property = $this->property;

        $transaction = Transaction::create([
            'property_id' => $property->id,
            'buyer_id' => $buyerId,
            'seller_id' => $property->seller_id,
            'status' => 'inquiry',
            'inquiry_date' => now(),
        ]);

        $this->update([
            'status' => InquiryStatus::CONVERTED,
            'converted_to_transaction_id' => $transaction->id,
        ]);

        return $transaction;
    }

    // Helpers
    public function getPreferredContactAttribute(): string
    {
        return match ($this->preferred_contact_method) {
            'phone' => $this->phone ?? $this->whatsapp ?? $this->email,
            'whatsapp' => $this->whatsapp ?? $this->phone ?? $this->email,
            'email' => $this->email,
            default => $this->whatsapp ?? $this->phone ?? $this->email,
        };
    }

    public function getWhatsappLinkAttribute(): ?string
    {
        $number = $this->whatsapp ?? $this->phone;
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

    public function isNew(): bool
    {
        return $this->status === InquiryStatus::NEW;
    }

    public function isConverted(): bool
    {
        return $this->status === InquiryStatus::CONVERTED;
    }
}
