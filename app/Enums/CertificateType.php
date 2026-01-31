<?php

namespace App\Enums;

enum CertificateType: string
{
    case SHM = 'shm';
    case SHGB = 'shgb';
    case SHP = 'shp';
    case GIRIK = 'girik';
    case AJB = 'ajb';
    case STRATA_TITLE = 'strata_title';
    case PPJB = 'ppjb';

    public function label(): string
    {
        return match ($this) {
            self::SHM => 'SHM (Sertifikat Hak Milik)',
            self::SHGB => 'SHGB (Sertifikat Hak Guna Bangunan)',
            self::SHP => 'SHP (Sertifikat Hak Pakai)',
            self::GIRIK => 'Girik/Letter C',
            self::AJB => 'AJB (Akta Jual Beli)',
            self::STRATA_TITLE => 'Strata Title',
            self::PPJB => 'PPJB (Perjanjian Pengikatan Jual Beli)',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::SHM => 'SHM',
            self::SHGB => 'SHGB',
            self::SHP => 'SHP',
            self::GIRIK => 'Girik',
            self::AJB => 'AJB',
            self::STRATA_TITLE => 'Strata Title',
            self::PPJB => 'PPJB',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
