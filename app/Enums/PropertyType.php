<?php

namespace App\Enums;

enum PropertyType: string
{
    case RUMAH = 'rumah';
    case APARTEMEN = 'apartemen';
    case TANAH = 'tanah';
    case RUKO = 'ruko';
    case GUDANG = 'gudang';
    case KANTOR = 'kantor';

    public function label(): string
    {
        return match ($this) {
            self::RUMAH => 'Rumah',
            self::APARTEMEN => 'Apartemen',
            self::TANAH => 'Tanah',
            self::RUKO => 'Ruko',
            self::GUDANG => 'Gudang',
            self::KANTOR => 'Kantor',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
