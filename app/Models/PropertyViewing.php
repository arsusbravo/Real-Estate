<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyViewing extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'buyer_id',
        'transaction_id',
        'scheduled_date',
        'scheduled_time',
        'status',
        'notes',
        'feedback',
        'rating',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime:H:i',
    ];

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_NO_SHOW = 'no_show';

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', now()->toDateString())
            ->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED])
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time');
    }

    public function scopeForBuyer($query, int $buyerId)
    {
        return $query->where('buyer_id', $buyerId);
    }

    public function scopeForProperty($query, int $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    // Actions
    public function confirm(): void
    {
        $this->update(['status' => self::STATUS_CONFIRMED]);
    }

    public function complete(?string $feedback = null, ?int $rating = null): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'feedback' => $feedback,
            'rating' => $rating,
        ]);

        // Update transaction status if linked
        if ($this->transaction) {
            $this->transaction->activities()->create([
                'user_id' => auth()->id(),
                'activity_type' => TransactionActivity::TYPE_VIEWING_COMPLETED,
                'description' => 'Viewing telah selesai dilakukan',
                'new_value' => ['feedback' => $feedback, 'rating' => $rating],
            ]);
        }
    }

    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'notes' => $reason ? ($this->notes ? "{$this->notes}\nDibatalkan: {$reason}" : "Dibatalkan: {$reason}") : $this->notes,
        ]);
    }

    public function markNoShow(): void
    {
        $this->update(['status' => self::STATUS_NO_SHOW]);
    }

    // Helpers
    public function getScheduledDateTimeAttribute(): string
    {
        return $this->scheduled_date->format('d M Y').' '.$this->scheduled_time->format('H:i');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SCHEDULED => 'Terjadwal',
            self::STATUS_CONFIRMED => 'Dikonfirmasi',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_NO_SHOW => 'Tidak Hadir',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SCHEDULED => 'blue',
            self::STATUS_CONFIRMED => 'green',
            self::STATUS_COMPLETED => 'gray',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_NO_SHOW => 'orange',
            default => 'gray',
        };
    }

    public function isUpcoming(): bool
    {
        return $this->scheduled_date->isFuture() &&
            in_array($this->status, [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED]);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
}
