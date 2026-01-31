<?php

namespace App\Enums;

enum InquiryStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case FOLLOWING_UP = 'following_up';
    case CONVERTED = 'converted';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Baru',
            self::CONTACTED => 'Sudah Dihubungi',
            self::FOLLOWING_UP => 'Follow Up',
            self::CONVERTED => 'Dikonversi ke Transaksi',
            self::CLOSED => 'Ditutup',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW => 'blue',
            self::CONTACTED => 'yellow',
            self::FOLLOWING_UP => 'purple',
            self::CONVERTED => 'green',
            self::CLOSED => 'gray',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
