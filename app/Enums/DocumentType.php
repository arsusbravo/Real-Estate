<?php

namespace App\Enums;

enum DocumentType: string
{
    // Property Documents (Seller provides)
    case SHM = 'shm';
    case SHGB = 'shgb';
    case SHP = 'shp';
    case GIRIK = 'girik';
    case AJB_SEBELUMNYA = 'ajb_previous';
    case STRATA_TITLE = 'strata_title';

    // Tax Documents (Seller)
    case PBB = 'pbb';
    case STTS_PBB = 'stts_pbb';
    case SPPT_PBB = 'sppt_pbb';

    // Building Permits (Seller)
    case IMB = 'imb';
    case PBG = 'pbg';
    case SLF = 'slf';

    // Identity Documents (Seller)
    case KTP_PENJUAL = 'ktp_seller';
    case KK_PENJUAL = 'kk_seller';
    case NPWP_PENJUAL = 'npwp_seller';
    case AKTA_NIKAH_PENJUAL = 'marriage_cert_seller';
    case SURAT_CERAI_PENJUAL = 'divorce_cert_seller';
    case AKTA_KEMATIAN_PASANGAN = 'death_cert_spouse';
    case PERSETUJUAN_PASANGAN_PENJUAL = 'spouse_consent_seller';

    // Identity Documents (Buyer)
    case KTP_PEMBELI = 'ktp_buyer';
    case KK_PEMBELI = 'kk_buyer';
    case NPWP_PEMBELI = 'npwp_buyer';
    case AKTA_NIKAH_PEMBELI = 'marriage_cert_buyer';
    case PERSETUJUAN_PASANGAN_PEMBELI = 'spouse_consent_buyer';

    // Company Documents
    case AKTA_PENDIRIAN = 'company_deed';
    case SK_KEMENKUMHAM = 'sk_kemenkumham';
    case SIUP = 'siup';
    case TDP = 'tdp';
    case NIB = 'nib';

    // Transaction Documents
    case SURAT_PEMESANAN = 'booking_letter';
    case BUKTI_DP = 'dp_receipt';
    case PPJB = 'ppjb';
    case AJB = 'ajb';
    case BUKTI_PELUNASAN = 'payment_proof';
    case BPHTB = 'bphtb';
    case PPH = 'pph';

    // Post-Transaction Documents
    case SERTIFIKAT_BARU = 'new_certificate';
    case BERITA_ACARA_SERAH_TERIMA = 'handover_report';

    // Additional Documents
    case SURAT_KUASA = 'power_of_attorney';
    case FOTO_PROPERTI = 'property_photos';
    case DENAH_BANGUNAN = 'floor_plan';
    case SITEPLAN = 'site_plan';
    case SURAT_KETERANGAN_TIDAK_SENGKETA = 'no_dispute_letter';
    case SURAT_KETERANGAN_LUNAS_IURAN = 'paid_dues_letter';
    case IPL_HISTORY = 'ipl_history';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::SHM => 'Sertifikat Hak Milik (SHM)',
            self::SHGB => 'Sertifikat Hak Guna Bangunan (SHGB)',
            self::SHP => 'Sertifikat Hak Pakai (SHP)',
            self::GIRIK => 'Girik/Letter C',
            self::AJB_SEBELUMNYA => 'AJB Sebelumnya',
            self::STRATA_TITLE => 'Sertifikat Strata Title',
            self::PBB => 'PBB (5 Tahun Terakhir)',
            self::STTS_PBB => 'STTS PBB',
            self::SPPT_PBB => 'SPPT PBB',
            self::IMB => 'Izin Mendirikan Bangunan (IMB)',
            self::PBG => 'Persetujuan Bangunan Gedung (PBG)',
            self::SLF => 'Sertifikat Laik Fungsi (SLF)',
            self::KTP_PENJUAL => 'KTP Penjual',
            self::KK_PENJUAL => 'Kartu Keluarga Penjual',
            self::NPWP_PENJUAL => 'NPWP Penjual',
            self::AKTA_NIKAH_PENJUAL => 'Akta Nikah Penjual',
            self::SURAT_CERAI_PENJUAL => 'Akta Cerai Penjual',
            self::AKTA_KEMATIAN_PASANGAN => 'Akta Kematian Pasangan',
            self::PERSETUJUAN_PASANGAN_PENJUAL => 'Surat Persetujuan Pasangan Penjual',
            self::KTP_PEMBELI => 'KTP Pembeli',
            self::KK_PEMBELI => 'Kartu Keluarga Pembeli',
            self::NPWP_PEMBELI => 'NPWP Pembeli',
            self::AKTA_NIKAH_PEMBELI => 'Akta Nikah Pembeli',
            self::PERSETUJUAN_PASANGAN_PEMBELI => 'Surat Persetujuan Pasangan Pembeli',
            self::AKTA_PENDIRIAN => 'Akta Pendirian PT',
            self::SK_KEMENKUMHAM => 'SK Kemenkumham',
            self::SIUP => 'Surat Izin Usaha Perdagangan (SIUP)',
            self::TDP => 'Tanda Daftar Perusahaan (TDP)',
            self::NIB => 'Nomor Induk Berusaha (NIB)',
            self::SURAT_PEMESANAN => 'Surat Pemesanan',
            self::BUKTI_DP => 'Bukti Pembayaran DP',
            self::PPJB => 'Perjanjian Pengikatan Jual Beli (PPJB)',
            self::AJB => 'Akta Jual Beli (AJB)',
            self::BUKTI_PELUNASAN => 'Bukti Pelunasan',
            self::BPHTB => 'BPHTB (Bea Perolehan Hak atas Tanah dan Bangunan)',
            self::PPH => 'PPh Final (2.5%)',
            self::SERTIFIKAT_BARU => 'Sertifikat Baru (Atas Nama Pembeli)',
            self::BERITA_ACARA_SERAH_TERIMA => 'Berita Acara Serah Terima (BAST)',
            self::SURAT_KUASA => 'Surat Kuasa',
            self::FOTO_PROPERTI => 'Foto Properti',
            self::DENAH_BANGUNAN => 'Denah Bangunan',
            self::SITEPLAN => 'Site Plan',
            self::SURAT_KETERANGAN_TIDAK_SENGKETA => 'Surat Keterangan Tidak Sengketa',
            self::SURAT_KETERANGAN_LUNAS_IURAN => 'Surat Keterangan Lunas Iuran',
            self::IPL_HISTORY => 'Riwayat IPL',
            self::OTHER => 'Dokumen Lainnya',
        };
    }

    public function category(): string
    {
        return match ($this) {
            self::SHM, self::SHGB, self::SHP, self::GIRIK, self::AJB_SEBELUMNYA, self::STRATA_TITLE => 'property',
            self::PBB, self::STTS_PBB, self::SPPT_PBB, self::BPHTB, self::PPH => 'tax',
            self::IMB, self::PBG, self::SLF => 'permit',
            self::KTP_PENJUAL, self::KK_PENJUAL, self::NPWP_PENJUAL, self::AKTA_NIKAH_PENJUAL, self::SURAT_CERAI_PENJUAL, self::AKTA_KEMATIAN_PASANGAN, self::PERSETUJUAN_PASANGAN_PENJUAL => 'seller_identity',
            self::KTP_PEMBELI, self::KK_PEMBELI, self::NPWP_PEMBELI, self::AKTA_NIKAH_PEMBELI, self::PERSETUJUAN_PASANGAN_PEMBELI => 'buyer_identity',
            self::AKTA_PENDIRIAN, self::SK_KEMENKUMHAM, self::SIUP, self::TDP, self::NIB => 'company',
            self::SURAT_PEMESANAN, self::BUKTI_DP, self::PPJB, self::AJB, self::BUKTI_PELUNASAN, self::SERTIFIKAT_BARU, self::BERITA_ACARA_SERAH_TERIMA => 'transaction',
            self::SURAT_KUASA, self::FOTO_PROPERTI, self::DENAH_BANGUNAN, self::SITEPLAN, self::SURAT_KETERANGAN_TIDAK_SENGKETA, self::SURAT_KETERANGAN_LUNAS_IURAN, self::IPL_HISTORY, self::OTHER => 'other',
        };
    }

    public static function sellerDocuments(): array
    {
        return [
            self::SHM, self::SHGB, self::SHP, self::GIRIK, self::AJB_SEBELUMNYA, self::STRATA_TITLE,
            self::PBB, self::STTS_PBB, self::SPPT_PBB,
            self::IMB, self::PBG, self::SLF,
            self::KTP_PENJUAL, self::KK_PENJUAL, self::NPWP_PENJUAL,
            self::AKTA_NIKAH_PENJUAL, self::SURAT_CERAI_PENJUAL, self::AKTA_KEMATIAN_PASANGAN,
            self::PERSETUJUAN_PASANGAN_PENJUAL,
        ];
    }

    public static function buyerDocuments(): array
    {
        return [
            self::KTP_PEMBELI, self::KK_PEMBELI, self::NPWP_PEMBELI,
            self::AKTA_NIKAH_PEMBELI, self::PERSETUJUAN_PASANGAN_PEMBELI,
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
