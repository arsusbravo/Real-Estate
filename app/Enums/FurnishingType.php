<?php

namespace App\Enums;

enum FurnishingType: string
{
    case UNFURNISHED = 'unfurnished';
    case SEMI_FURNISHED = 'semi_furnished';
    case FULLY_FURNISHED = 'fully_furnished';

    public function label(): string
    {
        return match ($this) {
            self::UNFURNISHED => 'Kosongan',
            self::SEMI_FURNISHED => 'Semi Furnished',
            self::FULLY_FURNISHED => 'Full Furnished',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
