<?php

namespace App\Enums;

enum ListingType: string
{
    case DIJUAL = 'dijual';
    case DISEWAKAN = 'disewakan';

    public function label(): string
    {
        return match ($this) {
            self::DIJUAL => 'Dijual',
            self::DISEWAKAN => 'Disewakan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
