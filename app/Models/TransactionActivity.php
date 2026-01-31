<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'activity_type',
        'description',
        'old_value',
        'new_value',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    // Activity types
    public const TYPE_STATUS_CHANGE = 'status_change';

    public const TYPE_NOTE_ADDED = 'note_added';

    public const TYPE_DOCUMENT_UPLOADED = 'document_uploaded';

    public const TYPE_DOCUMENT_VERIFIED = 'document_verified';

    public const TYPE_DOCUMENT_REJECTED = 'document_rejected';

    public const TYPE_VIEWING_SCHEDULED = 'viewing_scheduled';

    public const TYPE_VIEWING_COMPLETED = 'viewing_completed';

    public const TYPE_NOTARY_ASSIGNED = 'notary_assigned';

    public const TYPE_PRICE_UPDATED = 'price_updated';

    public const TYPE_PAYMENT_RECEIVED = 'payment_received';

    // Relationships
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helpers
    public function getActivityTypeLabel(): string
    {
        return match ($this->activity_type) {
            self::TYPE_STATUS_CHANGE => 'Perubahan Status',
            self::TYPE_NOTE_ADDED => 'Catatan Ditambahkan',
            self::TYPE_DOCUMENT_UPLOADED => 'Dokumen Diupload',
            self::TYPE_DOCUMENT_VERIFIED => 'Dokumen Diverifikasi',
            self::TYPE_DOCUMENT_REJECTED => 'Dokumen Ditolak',
            self::TYPE_VIEWING_SCHEDULED => 'Jadwal Viewing',
            self::TYPE_VIEWING_COMPLETED => 'Viewing Selesai',
            self::TYPE_NOTARY_ASSIGNED => 'Notaris Ditugaskan',
            self::TYPE_PRICE_UPDATED => 'Harga Diperbarui',
            self::TYPE_PAYMENT_RECEIVED => 'Pembayaran Diterima',
            default => $this->activity_type,
        };
    }

    public function getActivityIcon(): string
    {
        return match ($this->activity_type) {
            self::TYPE_STATUS_CHANGE => 'refresh-cw',
            self::TYPE_NOTE_ADDED => 'message-square',
            self::TYPE_DOCUMENT_UPLOADED => 'upload',
            self::TYPE_DOCUMENT_VERIFIED => 'check-circle',
            self::TYPE_DOCUMENT_REJECTED => 'x-circle',
            self::TYPE_VIEWING_SCHEDULED => 'calendar',
            self::TYPE_VIEWING_COMPLETED => 'check',
            self::TYPE_NOTARY_ASSIGNED => 'user-check',
            self::TYPE_PRICE_UPDATED => 'dollar-sign',
            self::TYPE_PAYMENT_RECEIVED => 'credit-card',
            default => 'activity',
        };
    }
}
