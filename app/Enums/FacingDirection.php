<?php

namespace App\Enums;

enum FacingDirection: string
{
    case UTARA = 'utara';
    case SELATAN = 'selatan';
    case TIMUR = 'timur';
    case BARAT = 'barat';
    case TIMUR_LAUT = 'timur_laut';
    case TENGGARA = 'tenggara';
    case BARAT_DAYA = 'barat_daya';
    case BARAT_LAUT = 'barat_laut';

    public function label(): string
    {
        return match ($this) {
            self::UTARA => 'Utara',
            self::SELATAN => 'Selatan',
            self::TIMUR => 'Timur',
            self::BARAT => 'Barat',
            self::TIMUR_LAUT => 'Timur Laut',
            self::TENGGARA => 'Tenggara',
            self::BARAT_DAYA => 'Barat Daya',
            self::BARAT_LAUT => 'Barat Laut',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
