<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'feature_name',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // Common property features in Indonesian
    public static function availableFeatures(): array
    {
        return [
            'kolam_renang' => 'Kolam Renang',
            'taman' => 'Taman',
            'security_24jam' => 'Security 24 Jam',
            'cctv' => 'CCTV',
            'carport' => 'Carport',
            'garasi' => 'Garasi',
            'ac' => 'AC',
            'water_heater' => 'Water Heater',
            'listrik_pln' => 'Listrik PLN',
            'pam' => 'PAM/Air PDAM',
            'sumur_bor' => 'Sumur Bor',
            'internet' => 'Internet/WiFi',
            'telepon' => 'Telepon',
            'dapur' => 'Dapur',
            'ruang_tamu' => 'Ruang Tamu',
            'ruang_makan' => 'Ruang Makan',
            'ruang_keluarga' => 'Ruang Keluarga',
            'balkon' => 'Balkon',
            'teras' => 'Teras',
            'gudang' => 'Gudang',
            'kamar_pembantu' => 'Kamar Pembantu',
            'one_gate_system' => 'One Gate System',
            'cluster' => 'Cluster',
            'gym' => 'Gym/Fitness',
            'playground' => 'Playground',
            'jogging_track' => 'Jogging Track',
            'lapangan_basket' => 'Lapangan Basket',
            'lapangan_tenis' => 'Lapangan Tenis',
            'masjid' => 'Masjid',
            'sekolah' => 'Dekat Sekolah',
            'mall' => 'Dekat Mall',
            'rumah_sakit' => 'Dekat Rumah Sakit',
            'stasiun' => 'Dekat Stasiun',
            'tol' => 'Dekat Tol',
        ];
    }
}
