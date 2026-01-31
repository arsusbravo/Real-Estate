<?php

namespace App\Enums;

enum RequirementUrgency: string
{
    case SEGERA = 'segera';
    case SATU_BULAN = '1_bulan';
    case TIGA_BULAN = '3_bulan';
    case ENAM_BULAN = '6_bulan';
    case FLEKSIBEL = 'fleksibel';

    public function label(): string
    {
        return match ($this) {
            self::SEGERA => 'Segera',
            self::SATU_BULAN => '1 Bulan',
            self::TIGA_BULAN => '3 Bulan',
            self::ENAM_BULAN => '6 Bulan',
            self::FLEKSIBEL => 'Fleksibel',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
