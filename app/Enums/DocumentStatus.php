<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case PENDING = 'pending';
    case UPLOADED = 'uploaded';
    case UNDER_REVIEW = 'under_review';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Belum Diupload',
            self::UPLOADED => 'Sudah Diupload',
            self::UNDER_REVIEW => 'Sedang Direview',
            self::VERIFIED => 'Terverifikasi',
            self::REJECTED => 'Ditolak',
            self::EXPIRED => 'Kadaluarsa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::UPLOADED => 'blue',
            self::UNDER_REVIEW => 'yellow',
            self::VERIFIED => 'green',
            self::REJECTED => 'red',
            self::EXPIRED => 'orange',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
