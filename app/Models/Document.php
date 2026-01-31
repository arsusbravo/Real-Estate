<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'uploaded_by',
        'document_type',
        'category',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'document_type' => DocumentType::class,
        'status' => DocumentStatus::class,
        'file_size' => 'integer',
        'verified_at' => 'datetime',
        'expiry_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Document $document) {
            $document->uuid = $document->uuid ?? Str::uuid();
        });

        static::deleting(function (Document $document) {
            // Delete file when document is force deleted
            if ($document->isForceDeleting()) {
                Storage::delete($document->file_path);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->whereIn('status', [
            DocumentStatus::UPLOADED,
            DocumentStatus::UNDER_REVIEW,
        ]);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', DocumentStatus::VERIFIED);
    }

    public function scopeForTransaction($query, int $transactionId)
    {
        return $query->where('documentable_type', Transaction::class)
            ->where('documentable_id', $transactionId);
    }

    public function scopeSellerDocuments($query)
    {
        return $query->where('category', 'seller_document');
    }

    public function scopeBuyerDocuments($query)
    {
        return $query->where('category', 'buyer_document');
    }

    // Actions
    public function verify(int $verifierId, ?string $notes = null): bool
    {
        $this->update([
            'status' => DocumentStatus::VERIFIED,
            'verified_by' => $verifierId,
            'verified_at' => now(),
            'notes' => $notes,
            'rejection_reason' => null,
        ]);

        // Log activity if attached to transaction
        if ($this->documentable instanceof Transaction) {
            $this->documentable->activities()->create([
                'user_id' => $verifierId,
                'activity_type' => TransactionActivity::TYPE_DOCUMENT_VERIFIED,
                'description' => "Dokumen {$this->document_type->label()} telah diverifikasi",
                'new_value' => ['document_id' => $this->id],
            ]);
        }

        return true;
    }

    public function reject(int $verifierId, string $reason): bool
    {
        $this->update([
            'status' => DocumentStatus::REJECTED,
            'verified_by' => $verifierId,
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // Log activity if attached to transaction
        if ($this->documentable instanceof Transaction) {
            $this->documentable->activities()->create([
                'user_id' => $verifierId,
                'activity_type' => TransactionActivity::TYPE_DOCUMENT_REJECTED,
                'description' => "Dokumen {$this->document_type->label()} ditolak: {$reason}",
                'new_value' => ['document_id' => $this->id, 'reason' => $reason],
            ]);
        }

        return true;
    }

    // Helpers
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' bytes';
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('documents.download', $this->uuid);
    }

    public function isVerified(): bool
    {
        return $this->status === DocumentStatus::VERIFIED;
    }

    public function isRejected(): bool
    {
        return $this->status === DocumentStatus::REJECTED;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [
            DocumentStatus::UPLOADED,
            DocumentStatus::UNDER_REVIEW,
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
