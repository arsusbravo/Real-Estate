<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case BUYER = 'buyer';
    case SELLER = 'seller';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::BUYER => 'Pembeli',
            self::SELLER => 'Penjual',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
