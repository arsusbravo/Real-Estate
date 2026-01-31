<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'property_id',
        'buyer_id',
        'seller_id',
        'notary_id',
        'assigned_admin_id',
        'transaction_number',
        'agreed_price',
        'dp_amount',
        'commission_percentage',
        'commission_amount',
        'status',
        'current_step',
        'inquiry_date',
        'viewing_date',
        'negotiation_started_at',
        'agreement_date',
        'dp_paid_at',
        'notary_assigned_at',
        'ajb_signed_at',
        'certificate_transferred_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'notes',
    ];

    protected $casts = [
        'status' => TransactionStatus::class,
        'agreed_price' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'inquiry_date' => 'datetime',
        'viewing_date' => 'datetime',
        'negotiation_started_at' => 'datetime',
        'agreement_date' => 'datetime',
        'dp_paid_at' => 'datetime',
        'notary_assigned_at' => 'datetime',
        'ajb_signed_at' => 'datetime',
        'certificate_transferred_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Transaction $transaction) {
            $transaction->uuid = $transaction->uuid ?? Str::uuid();
            $transaction->transaction_number = $transaction->transaction_number ?? self::generateTransactionNumber();
            $transaction->inquiry_date = $transaction->inquiry_date ?? now();
        });

        static::updating(function (Transaction $transaction) {
            // Update current step based on status
            if ($transaction->isDirty('status')) {
                $transaction->current_step = $transaction->status->step();
            }

            // Calculate commission when agreed price is set
            if ($transaction->isDirty('agreed_price') && $transaction->agreed_price) {
                $transaction->commission_amount = $transaction->agreed_price * ($transaction->commission_percentage / 100);
            }
        });
    }

    public static function generateTransactionNumber(): string
    {
        $prefix = 'TRX';
        $yearMonth = now()->format('Ym');
        $lastTransaction = self::where('transaction_number', 'like', "{$prefix}-{$yearMonth}-%")
            ->orderBy('transaction_number', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$yearMonth}-{$newNumber}";
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'agreed_price', 'dp_amount', 'notary_id', 'notes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function notary(): BelongsTo
    {
        return $this->belongsTo(NotaryPartner::class, 'notary_id');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TransactionActivity::class)->orderByDesc('created_at');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function viewings(): HasMany
    {
        return $this->hasMany(PropertyViewing::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [
            TransactionStatus::COMPLETED,
            TransactionStatus::CANCELLED,
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatus::COMPLETED);
    }

    public function scopeForBuyer($query, int $buyerId)
    {
        return $query->where('buyer_id', $buyerId);
    }

    public function scopeForSeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    // Status management
    public function canTransitionTo(TransactionStatus $newStatus): bool
    {
        return in_array($newStatus, $this->status->allowedTransitions());
    }

    public function transitionTo(TransactionStatus $newStatus, ?string $notes = null): bool
    {
        if (! $this->canTransitionTo($newStatus)) {
            return false;
        }

        $oldStatus = $this->status;
        $this->status = $newStatus;

        // Set relevant date fields
        match ($newStatus) {
            TransactionStatus::VIEWING_SCHEDULED => $this->viewing_date = $this->viewing_date ?? now(),
            TransactionStatus::NEGOTIATION => $this->negotiation_started_at = now(),
            TransactionStatus::AGREEMENT_REACHED => $this->agreement_date = now(),
            TransactionStatus::DP_RECEIVED => $this->dp_paid_at = now(),
            TransactionStatus::NOTARY_ASSIGNED => $this->notary_assigned_at = now(),
            TransactionStatus::AJB_SIGNED => $this->ajb_signed_at = now(),
            TransactionStatus::CERTIFICATE_COMPLETED => $this->certificate_transferred_at = now(),
            TransactionStatus::COMPLETED => $this->completed_at = now(),
            TransactionStatus::CANCELLED => $this->cancelled_at = now(),
            default => null,
        };

        $this->save();

        // Log activity
        $this->activities()->create([
            'user_id' => auth()->id(),
            'activity_type' => 'status_change',
            'description' => "Status berubah dari {$oldStatus->label()} ke {$newStatus->label()}",
            'old_value' => ['status' => $oldStatus->value],
            'new_value' => ['status' => $newStatus->value, 'notes' => $notes],
        ]);

        return true;
    }

    // Helpers
    public function getAgreedPriceFormattedAttribute(): ?string
    {
        return $this->agreed_price
            ? 'Rp '.number_format($this->agreed_price, 0, ',', '.')
            : null;
    }

    public function getDpAmountFormattedAttribute(): ?string
    {
        return $this->dp_amount
            ? 'Rp '.number_format($this->dp_amount, 0, ',', '.')
            : null;
    }

    public function getCommissionAmountFormattedAttribute(): ?string
    {
        return $this->commission_amount
            ? 'Rp '.number_format($this->commission_amount, 0, ',', '.')
            : null;
    }

    public function isActive(): bool
    {
        return ! in_array($this->status, [
            TransactionStatus::COMPLETED,
            TransactionStatus::CANCELLED,
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === TransactionStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === TransactionStatus::CANCELLED;
    }
}
