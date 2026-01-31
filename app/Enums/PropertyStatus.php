<?php

namespace App\Enums;

enum PropertyStatus: string
{
    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case ACTIVE = 'active';
    case SOLD = 'sold';
    case RENTED = 'rented';
    case INACTIVE = 'inactive';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING_REVIEW => 'Menunggu Review',
            self::ACTIVE => 'Aktif',
            self::SOLD => 'Terjual',
            self::RENTED => 'Tersewa',
            self::INACTIVE => 'Tidak Aktif',
            self::REJECTED => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING_REVIEW => 'yellow',
            self::ACTIVE => 'green',
            self::SOLD => 'blue',
            self::RENTED => 'blue',
            self::INACTIVE => 'gray',
            self::REJECTED => 'red',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
