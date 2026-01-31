<?php

namespace App\Enums;

enum TransactionStatus: string
{
    // Initial stages
    case INQUIRY = 'inquiry';
    case VIEWING_SCHEDULED = 'viewing_scheduled';
    case VIEWING_COMPLETED = 'viewing_completed';

    // Negotiation stages
    case NEGOTIATION = 'negotiation';
    case AGREEMENT_REACHED = 'agreement_reached';

    // Document & payment stages
    case DP_PENDING = 'dp_pending';
    case DP_RECEIVED = 'dp_received';
    case DOCUMENT_COLLECTION = 'document_collection';
    case DOCUMENT_VERIFICATION = 'document_verification';

    // Notary stages
    case NOTARY_ASSIGNED = 'notary_assigned';
    case NOTARY_REVIEW = 'notary_review';
    case AJB_PREPARATION = 'ajb_preparation';
    case AJB_SIGNING = 'ajb_signing';
    case AJB_SIGNED = 'ajb_signed';

    // Transfer stages
    case PAYMENT_PROCESSING = 'payment_processing';
    case PAYMENT_COMPLETED = 'payment_completed';
    case CERTIFICATE_TRANSFER = 'certificate_transfer';
    case CERTIFICATE_COMPLETED = 'certificate_completed';

    // Final stages
    case HANDOVER = 'handover';
    case COMPLETED = 'completed';

    // Exception states
    case ON_HOLD = 'on_hold';
    case CANCELLED = 'cancelled';
    case DISPUTED = 'disputed';

    public function label(): string
    {
        return match ($this) {
            self::INQUIRY => 'Inquiry',
            self::VIEWING_SCHEDULED => 'Jadwal Viewing',
            self::VIEWING_COMPLETED => 'Viewing Selesai',
            self::NEGOTIATION => 'Negosiasi',
            self::AGREEMENT_REACHED => 'Kesepakatan Tercapai',
            self::DP_PENDING => 'Menunggu DP',
            self::DP_RECEIVED => 'DP Diterima',
            self::DOCUMENT_COLLECTION => 'Pengumpulan Dokumen',
            self::DOCUMENT_VERIFICATION => 'Verifikasi Dokumen',
            self::NOTARY_ASSIGNED => 'Notaris Ditugaskan',
            self::NOTARY_REVIEW => 'Review Notaris',
            self::AJB_PREPARATION => 'Persiapan AJB',
            self::AJB_SIGNING => 'Penandatanganan AJB',
            self::AJB_SIGNED => 'AJB Ditandatangani',
            self::PAYMENT_PROCESSING => 'Proses Pembayaran',
            self::PAYMENT_COMPLETED => 'Pembayaran Selesai',
            self::CERTIFICATE_TRANSFER => 'Balik Nama Sertifikat',
            self::CERTIFICATE_COMPLETED => 'Sertifikat Selesai',
            self::HANDOVER => 'Serah Terima',
            self::COMPLETED => 'Selesai',
            self::ON_HOLD => 'Ditunda',
            self::CANCELLED => 'Dibatalkan',
            self::DISPUTED => 'Sengketa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::INQUIRY, self::VIEWING_SCHEDULED => 'blue',
            self::VIEWING_COMPLETED, self::NEGOTIATION => 'indigo',
            self::AGREEMENT_REACHED, self::DP_PENDING => 'purple',
            self::DP_RECEIVED, self::DOCUMENT_COLLECTION, self::DOCUMENT_VERIFICATION => 'yellow',
            self::NOTARY_ASSIGNED, self::NOTARY_REVIEW, self::AJB_PREPARATION, self::AJB_SIGNING => 'orange',
            self::AJB_SIGNED, self::PAYMENT_PROCESSING, self::PAYMENT_COMPLETED => 'teal',
            self::CERTIFICATE_TRANSFER, self::CERTIFICATE_COMPLETED, self::HANDOVER => 'cyan',
            self::COMPLETED => 'green',
            self::ON_HOLD => 'gray',
            self::CANCELLED => 'red',
            self::DISPUTED => 'red',
        };
    }

    public function step(): int
    {
        return match ($this) {
            self::INQUIRY => 1,
            self::VIEWING_SCHEDULED => 2,
            self::VIEWING_COMPLETED => 3,
            self::NEGOTIATION => 4,
            self::AGREEMENT_REACHED => 5,
            self::DP_PENDING => 6,
            self::DP_RECEIVED => 7,
            self::DOCUMENT_COLLECTION => 8,
            self::DOCUMENT_VERIFICATION => 9,
            self::NOTARY_ASSIGNED => 10,
            self::NOTARY_REVIEW => 11,
            self::AJB_PREPARATION => 12,
            self::AJB_SIGNING => 13,
            self::AJB_SIGNED => 14,
            self::PAYMENT_PROCESSING => 15,
            self::PAYMENT_COMPLETED => 16,
            self::CERTIFICATE_TRANSFER => 17,
            self::CERTIFICATE_COMPLETED => 18,
            self::HANDOVER => 19,
            self::COMPLETED => 20,
            self::ON_HOLD, self::CANCELLED, self::DISPUTED => 0,
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::INQUIRY => [self::VIEWING_SCHEDULED, self::CANCELLED],
            self::VIEWING_SCHEDULED => [self::VIEWING_COMPLETED, self::VIEWING_SCHEDULED, self::CANCELLED],
            self::VIEWING_COMPLETED => [self::NEGOTIATION, self::VIEWING_SCHEDULED, self::CANCELLED],
            self::NEGOTIATION => [self::AGREEMENT_REACHED, self::VIEWING_SCHEDULED, self::CANCELLED],
            self::AGREEMENT_REACHED => [self::DP_PENDING, self::NEGOTIATION, self::CANCELLED],
            self::DP_PENDING => [self::DP_RECEIVED, self::CANCELLED],
            self::DP_RECEIVED => [self::DOCUMENT_COLLECTION, self::ON_HOLD],
            self::DOCUMENT_COLLECTION => [self::DOCUMENT_VERIFICATION, self::ON_HOLD],
            self::DOCUMENT_VERIFICATION => [self::NOTARY_ASSIGNED, self::DOCUMENT_COLLECTION, self::ON_HOLD],
            self::NOTARY_ASSIGNED => [self::NOTARY_REVIEW, self::ON_HOLD],
            self::NOTARY_REVIEW => [self::AJB_PREPARATION, self::DOCUMENT_VERIFICATION, self::ON_HOLD],
            self::AJB_PREPARATION => [self::AJB_SIGNING, self::ON_HOLD],
            self::AJB_SIGNING => [self::AJB_SIGNED, self::ON_HOLD],
            self::AJB_SIGNED => [self::PAYMENT_PROCESSING],
            self::PAYMENT_PROCESSING => [self::PAYMENT_COMPLETED, self::DISPUTED],
            self::PAYMENT_COMPLETED => [self::CERTIFICATE_TRANSFER],
            self::CERTIFICATE_TRANSFER => [self::CERTIFICATE_COMPLETED],
            self::CERTIFICATE_COMPLETED => [self::HANDOVER],
            self::HANDOVER => [self::COMPLETED],
            self::COMPLETED => [],
            self::ON_HOLD => [self::CANCELLED],
            self::CANCELLED => [],
            self::DISPUTED => [self::CANCELLED],
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
