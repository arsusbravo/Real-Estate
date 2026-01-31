<?php

namespace Database\Seeders;

use App\Enums\CertificateType;
use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Enums\FurnishingType;
use App\Enums\InquiryStatus;
use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Enums\RequirementUrgency;
use App\Enums\TransactionStatus;
use App\Enums\UserRole;
use App\Models\BuyerRequirement;
use App\Models\Document;
use App\Models\Favorite;
use App\Models\Inquiry;
use App\Models\NotaryPartner;
use App\Models\Property;
use App\Models\PropertyFeature;
use App\Models\PropertyViewing;
use App\Models\Transaction;
use App\Models\TransactionActivity;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Users ────────────────────────────────────────────────────────
        $admin = User::factory()->create([
            'name' => 'Admin PropertiKu',
            'email' => 'info@arsus.nl',
            'password' => bcrypt('arsus123'),
            'role' => UserRole::ADMIN,
            'email_verified_at' => now(),
            'phone' => '081234567890',
            'whatsapp_number' => '081234567890',
        ]);

        $buyers = collect();
        $buyerData = [
            ['Budi Santoso', 'budi@example.com', '081234567891'],
            ['Dewi Lestari', 'dewi@example.com', '081234567893'],
            ['Rizky Pratama', 'rizky@example.com', '081234567895'],
            ['Anisa Putri', 'anisa@example.com', '081234567896'],
            ['Fajar Nugroho', 'fajar@example.com', '081234567897'],
        ];
        foreach ($buyerData as [$name, $email, $phone]) {
            $buyers->push(User::factory()->create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('password'),
                'role' => UserRole::BUYER,
                'email_verified_at' => now(),
                'phone' => $phone,
                'whatsapp_number' => $phone,
            ]));
        }

        $sellers = collect();
        $sellerData = [
            ['Siti Rahayu', 'siti@example.com', '081234567892'],
            ['Ahmad Hidayat', 'ahmad@example.com', '081234567894'],
            ['Linda Wati', 'linda@example.com', '081234567898'],
        ];
        foreach ($sellerData as [$name, $email, $phone]) {
            $sellers->push(User::factory()->create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('password'),
                'role' => UserRole::SELLER,
                'email_verified_at' => now(),
                'phone' => $phone,
                'whatsapp_number' => $phone,
            ]));
        }

        // ─── Properties ───────────────────────────────────────────────────
        $propertiesData = [
            [
                'seller_id' => $sellers[0]->id,
                'title' => 'Rumah Mewah di Menteng',
                'description' => 'Rumah mewah 2 lantai dengan desain modern di kawasan elite Menteng, Jakarta Pusat. Dekat dengan pusat bisnis dan fasilitas umum.',
                'property_type' => PropertyType::RUMAH, 'listing_type' => ListingType::DIJUAL,
                'price' => 15_000_000_000, 'price_negotiable' => true,
                'land_area' => 450, 'building_area' => 350, 'bedrooms' => 5, 'bathrooms' => 4, 'floors' => 2, 'parking_spaces' => 3,
                'furnishing' => FurnishingType::FULLY_FURNISHED,
                'province' => 'DKI Jakarta', 'city' => 'Jakarta Pusat', 'district' => 'Menteng', 'subdistrict' => 'Menteng',
                'address' => 'Jl. Menteng Raya No. 10', 'postal_code' => '10310',
                'certificate_type' => CertificateType::SHM, 'status' => PropertyStatus::ACTIVE,
                'featured' => true, 'verified' => true, 'published_at' => now()->subDays(30), 'views_count' => 245,
                'features' => ['Kolam Renang', 'Taman Luas', 'Smart Home System', 'CCTV', 'Gazebo'],
            ],
            [
                'seller_id' => $sellers[0]->id,
                'title' => 'Apartemen Premium Sudirman',
                'description' => 'Unit apartemen premium di kawasan SCBD Sudirman. View kota yang spektakuler, fasilitas lengkap.',
                'property_type' => PropertyType::APARTEMEN, 'listing_type' => ListingType::DIJUAL,
                'price' => 3_500_000_000, 'price_negotiable' => false,
                'land_area' => 0, 'building_area' => 120, 'bedrooms' => 3, 'bathrooms' => 2, 'floors' => 1, 'parking_spaces' => 2,
                'furnishing' => FurnishingType::FULLY_FURNISHED,
                'province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => 'Setiabudi', 'subdistrict' => 'Karet Kuningan',
                'address' => 'SCBD Lot 18, Sudirman', 'postal_code' => '12920',
                'certificate_type' => CertificateType::STRATA_TITLE, 'status' => PropertyStatus::ACTIVE,
                'featured' => true, 'verified' => true, 'published_at' => now()->subDays(25), 'views_count' => 189,
                'features' => ['Gym', 'Swimming Pool', 'Sky Lounge', 'Concierge 24 Jam'],
            ],
            [
                'seller_id' => $sellers[1]->id,
                'title' => 'Ruko Strategis di Kelapa Gading',
                'description' => 'Ruko 3 lantai di lokasi strategis Kelapa Gading, cocok untuk usaha atau kantor.',
                'property_type' => PropertyType::RUKO, 'listing_type' => ListingType::DIJUAL,
                'price' => 5_800_000_000, 'price_negotiable' => true,
                'land_area' => 80, 'building_area' => 200, 'bedrooms' => 0, 'bathrooms' => 3, 'floors' => 3, 'parking_spaces' => 1,
                'furnishing' => FurnishingType::UNFURNISHED,
                'province' => 'DKI Jakarta', 'city' => 'Jakarta Utara', 'district' => 'Kelapa Gading', 'subdistrict' => 'Kelapa Gading Barat',
                'address' => 'Jl. Boulevard Raya No. 25', 'postal_code' => '14240',
                'certificate_type' => CertificateType::SHGB, 'status' => PropertyStatus::ACTIVE,
                'featured' => false, 'verified' => true, 'published_at' => now()->subDays(20), 'views_count' => 98,
                'features' => ['Lokasi Strategis', 'Akses Jalan Utama', 'Dekat Mal'],
            ],
            [
                'seller_id' => $sellers[1]->id,
                'title' => 'Tanah Kavling PIK 2',
                'description' => 'Tanah kavling di area pengembangan PIK 2, potensi investasi tinggi.',
                'property_type' => PropertyType::TANAH, 'listing_type' => ListingType::DIJUAL,
                'price' => 8_000_000_000, 'price_negotiable' => true,
                'land_area' => 500, 'building_area' => null, 'bedrooms' => null, 'bathrooms' => null, 'floors' => 0, 'parking_spaces' => null,
                'furnishing' => FurnishingType::UNFURNISHED,
                'province' => 'DKI Jakarta', 'city' => 'Jakarta Utara', 'district' => 'Penjaringan', 'subdistrict' => 'Kamal Muara',
                'address' => 'PIK 2 Cluster Gold', 'postal_code' => '14470',
                'certificate_type' => CertificateType::SHGB, 'status' => PropertyStatus::ACTIVE,
                'featured' => false, 'verified' => true, 'published_at' => now()->subDays(15), 'views_count' => 156,
                'features' => ['Area Berkembang', 'Dekat Pantai', 'Akses Tol'],
            ],
            [
                'seller_id' => $sellers[0]->id,
                'title' => 'Rumah Cluster Bintaro Jaya',
                'description' => 'Rumah cluster modern di Bintaro Jaya Sektor 9. Lingkungan aman, dekat sekolah internasional.',
                'property_type' => PropertyType::RUMAH, 'listing_type' => ListingType::DIJUAL,
                'price' => 2_800_000_000, 'price_negotiable' => true,
                'land_area' => 150, 'building_area' => 120, 'bedrooms' => 3, 'bathrooms' => 2, 'floors' => 2, 'parking_spaces' => 2,
                'furnishing' => FurnishingType::SEMI_FURNISHED,
                'province' => 'DKI Jakarta', 'city' => 'Tangerang Selatan', 'district' => 'Pondok Aren', 'subdistrict' => 'Bintaro',
                'address' => 'Bintaro Jaya Sektor 9 Blok J No. 15', 'postal_code' => '15227',
                'certificate_type' => CertificateType::SHM, 'status' => PropertyStatus::ACTIVE,
                'featured' => true, 'verified' => true, 'published_at' => now()->subDays(10), 'views_count' => 312,
                'features' => ['Cluster', 'One Gate System', 'Taman Bermain', 'Jogging Track'],
            ],
            [
                'seller_id' => $sellers[1]->id,
                'title' => 'Rumah Minimalis Cibubur',
                'description' => 'Rumah minimalis siap huni di perumahan Cibubur. Akses mudah ke tol dan TransJakarta.',
                'property_type' => PropertyType::RUMAH, 'listing_type' => ListingType::DIJUAL,
                'price' => 1_200_000_000, 'price_negotiable' => true,
                'land_area' => 90, 'building_area' => 70, 'bedrooms' => 2, 'bathrooms' => 1, 'floors' => 1, 'parking_spaces' => 1,
                'furnishing' => FurnishingType::UNFURNISHED,
                'province' => 'DKI Jakarta', 'city' => 'Bekasi', 'district' => 'Jatiasih', 'subdistrict' => 'Jatiluhur',
                'address' => 'Perum Cibubur Indah Blok C No. 8', 'postal_code' => '17423',
                'certificate_type' => CertificateType::SHM, 'status' => PropertyStatus::PENDING_REVIEW,
                'featured' => false, 'verified' => false, 'published_at' => null, 'views_count' => 0,
                'features' => ['Carport', 'Taman Depan'],
            ],
            [
                'seller_id' => $sellers[0]->id,
                'title' => 'Apartemen Sewa di Kemang',
                'description' => 'Apartemen nyaman untuk disewakan di Kemang Village. Ideal untuk ekspatriat.',
                'property_type' => PropertyType::APARTEMEN, 'listing_type' => ListingType::DISEWAKAN,
                'price' => 120_000_000, 'price_negotiable' => false,
                'land_area' => 0, 'building_area' => 85, 'bedrooms' => 2, 'bathrooms' => 1, 'floors' => 1, 'parking_spaces' => 1,
                'furnishing' => FurnishingType::FULLY_FURNISHED,
                'province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => 'Mampang Prapatan', 'subdistrict' => 'Kemang',
                'address' => 'Kemang Village Tower Infinity Lt. 20', 'postal_code' => '12730',
                'certificate_type' => CertificateType::STRATA_TITLE, 'status' => PropertyStatus::ACTIVE,
                'featured' => true, 'verified' => true, 'published_at' => now()->subDays(5), 'views_count' => 78,
                'features' => ['Furnished Lengkap', 'View Pool', 'Akses Mall'],
            ],
            [
                'seller_id' => $sellers[2]->id,
                'title' => 'Gudang Industrial Marunda',
                'description' => 'Gudang industri besar di kawasan Marunda, cocok untuk logistik dan distribusi.',
                'property_type' => PropertyType::GUDANG, 'listing_type' => ListingType::DIJUAL,
                'price' => 12_000_000_000, 'price_negotiable' => true,
                'land_area' => 2000, 'building_area' => 1500, 'bedrooms' => null, 'bathrooms' => 2, 'floors' => 1, 'parking_spaces' => 10,
                'furnishing' => FurnishingType::UNFURNISHED,
                'province' => 'DKI Jakarta', 'city' => 'Jakarta Utara', 'district' => 'Cilincing', 'subdistrict' => 'Marunda',
                'address' => 'Kawasan Industri Marunda Blok B No. 5', 'postal_code' => '14150',
                'certificate_type' => CertificateType::SHGB, 'status' => PropertyStatus::ACTIVE,
                'featured' => false, 'verified' => true, 'published_at' => now()->subDays(18), 'views_count' => 42,
                'features' => ['Loading Dock', 'Container Access', 'Security 24 Jam', 'Genset'],
            ],
            [
                'seller_id' => $sellers[2]->id,
                'title' => 'Kantor di Kuningan',
                'description' => 'Ruang kantor strategis di kawasan bisnis Kuningan, siap pakai.',
                'property_type' => PropertyType::KANTOR, 'listing_type' => ListingType::DISEWAKAN,
                'price' => 250_000_000, 'price_negotiable' => true,
                'land_area' => 0, 'building_area' => 200, 'bedrooms' => null, 'bathrooms' => 2, 'floors' => 1, 'parking_spaces' => 3,
                'furnishing' => FurnishingType::SEMI_FURNISHED,
                'province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => 'Kuningan', 'subdistrict' => 'Karet Kuningan',
                'address' => 'Menara Kuningan Lt. 15', 'postal_code' => '12940',
                'certificate_type' => CertificateType::STRATA_TITLE, 'status' => PropertyStatus::ACTIVE,
                'featured' => false, 'verified' => true, 'published_at' => now()->subDays(8), 'views_count' => 65,
                'features' => ['Parkir Basement', 'Resepsionis', 'Meeting Room', 'Pantry'],
            ],
            [
                'seller_id' => $sellers[2]->id,
                'title' => 'Rumah Asri di Depok',
                'description' => 'Rumah asri dengan halaman luas di kawasan Depok. Cocok untuk keluarga muda.',
                'property_type' => PropertyType::RUMAH, 'listing_type' => ListingType::DIJUAL,
                'price' => 950_000_000, 'price_negotiable' => true,
                'land_area' => 120, 'building_area' => 80, 'bedrooms' => 3, 'bathrooms' => 2, 'floors' => 1, 'parking_spaces' => 1,
                'furnishing' => FurnishingType::UNFURNISHED,
                'province' => 'Jawa Barat', 'city' => 'Depok', 'district' => 'Sawangan', 'subdistrict' => 'Sawangan Baru',
                'address' => 'Jl. Raya Sawangan No. 88', 'postal_code' => '16511',
                'certificate_type' => CertificateType::SHM, 'status' => PropertyStatus::ACTIVE,
                'featured' => false, 'verified' => true, 'published_at' => now()->subDays(3), 'views_count' => 29,
                'features' => ['Halaman Luas', 'Dekat Stasiun', 'Akses Angkot'],
            ],
            [
                'seller_id' => $sellers[0]->id,
                'title' => 'Rumah Terjual di Pondok Indah',
                'description' => 'Rumah mewah di Pondok Indah yang sudah terjual.',
                'property_type' => PropertyType::RUMAH, 'listing_type' => ListingType::DIJUAL,
                'price' => 25_000_000_000, 'price_negotiable' => false,
                'land_area' => 600, 'building_area' => 500, 'bedrooms' => 6, 'bathrooms' => 5, 'floors' => 2, 'parking_spaces' => 4,
                'furnishing' => FurnishingType::FULLY_FURNISHED,
                'province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => 'Kebayoran Lama', 'subdistrict' => 'Pondok Indah',
                'address' => 'Jl. Metro Pondok Indah No. 5', 'postal_code' => '12310',
                'certificate_type' => CertificateType::SHM, 'status' => PropertyStatus::SOLD,
                'featured' => false, 'verified' => true, 'published_at' => now()->subMonths(3), 'views_count' => 520,
                'features' => ['Kolam Renang', 'Lift Pribadi', 'Wine Cellar', 'Home Theater', 'Taman Tropis'],
            ],
            [
                'seller_id' => $sellers[1]->id,
                'title' => 'Rumah di Tangerang',
                'description' => 'Rumah nyaman di perumahan BSD City, dekat AEON Mall.',
                'property_type' => PropertyType::RUMAH, 'listing_type' => ListingType::DIJUAL,
                'price' => 1_800_000_000, 'price_negotiable' => true,
                'land_area' => 130, 'building_area' => 100, 'bedrooms' => 3, 'bathrooms' => 2, 'floors' => 2, 'parking_spaces' => 2,
                'furnishing' => FurnishingType::SEMI_FURNISHED,
                'province' => 'Banten', 'city' => 'Tangerang Selatan', 'district' => 'Serpong', 'subdistrict' => 'BSD',
                'address' => 'BSD City Cluster Nusa Loka Blok A No. 12', 'postal_code' => '15321',
                'certificate_type' => CertificateType::SHM, 'status' => PropertyStatus::ACTIVE,
                'featured' => false, 'verified' => true, 'published_at' => now()->subDays(7), 'views_count' => 134,
                'features' => ['Cluster', 'Dekat Mall', 'Sekolah Internasional', 'Taman'],
            ],
            [
                'seller_id' => $sellers[2]->id,
                'title' => 'Ruko di Bekasi',
                'description' => 'Ruko baru 2 lantai di pinggir jalan utama Bekasi, strategis untuk usaha.',
                'property_type' => PropertyType::RUKO, 'listing_type' => ListingType::DIJUAL,
                'price' => 2_200_000_000, 'price_negotiable' => true,
                'land_area' => 72, 'building_area' => 140, 'bedrooms' => 0, 'bathrooms' => 2, 'floors' => 2, 'parking_spaces' => 1,
                'furnishing' => FurnishingType::UNFURNISHED,
                'province' => 'Jawa Barat', 'city' => 'Bekasi', 'district' => 'Bekasi Selatan', 'subdistrict' => 'Jaka Setia',
                'address' => 'Jl. Ahmad Yani No. 55, Bekasi', 'postal_code' => '17147',
                'certificate_type' => CertificateType::SHM, 'status' => PropertyStatus::ACTIVE,
                'featured' => false, 'verified' => true, 'published_at' => now()->subDays(12), 'views_count' => 87,
                'features' => ['Pinggir Jalan Utama', 'Parkir Depan', 'Listrik 3 Phase'],
            ],
            [
                'seller_id' => $sellers[0]->id,
                'title' => 'Apartemen Studio Thamrin',
                'description' => 'Studio apartment di pusat kota, cocok untuk pekerja kantoran.',
                'property_type' => PropertyType::APARTEMEN, 'listing_type' => ListingType::DISEWAKAN,
                'price' => 55_000_000, 'price_negotiable' => false,
                'land_area' => 0, 'building_area' => 28, 'bedrooms' => 1, 'bathrooms' => 1, 'floors' => 1, 'parking_spaces' => 0,
                'furnishing' => FurnishingType::FULLY_FURNISHED,
                'province' => 'DKI Jakarta', 'city' => 'Jakarta Pusat', 'district' => 'Thamrin', 'subdistrict' => 'Gondangdia',
                'address' => 'Thamrin Residence Tower A Lt. 30', 'postal_code' => '10350',
                'certificate_type' => CertificateType::STRATA_TITLE, 'status' => PropertyStatus::ACTIVE,
                'featured' => false, 'verified' => true, 'published_at' => now()->subDays(2), 'views_count' => 45,
                'features' => ['Furnished Lengkap', 'Dekat MRT', 'City View'],
            ],
            [
                'seller_id' => $sellers[1]->id,
                'title' => 'Rumah Baru di Bogor',
                'description' => 'Rumah baru 2 lantai di kawasan Sentul City, udara sejuk pegunungan.',
                'property_type' => PropertyType::RUMAH, 'listing_type' => ListingType::DIJUAL,
                'price' => 1_500_000_000, 'price_negotiable' => true,
                'land_area' => 200, 'building_area' => 150, 'bedrooms' => 4, 'bathrooms' => 3, 'floors' => 2, 'parking_spaces' => 2,
                'furnishing' => FurnishingType::UNFURNISHED,
                'province' => 'Jawa Barat', 'city' => 'Bogor', 'district' => 'Babakan Madang', 'subdistrict' => 'Sentul',
                'address' => 'Sentul City Cluster Bukit Golf Blok D No. 9', 'postal_code' => '16810',
                'certificate_type' => CertificateType::SHM, 'status' => PropertyStatus::DRAFT,
                'featured' => false, 'verified' => false, 'published_at' => null, 'views_count' => 0,
                'features' => ['View Gunung', 'Udara Sejuk', 'Dekat Tol Jagorawi'],
            ],
        ];

        $properties = collect();
        foreach ($propertiesData as $pData) {
            $features = $pData['features'] ?? [];
            unset($pData['features']);
            $property = Property::create($pData);
            foreach ($features as $featureName) {
                PropertyFeature::create([
                    'property_id' => $property->id,
                    'feature_name' => $featureName,
                ]);
            }
            $properties->push($property);
        }

        $activeProperties = $properties->where('status', PropertyStatus::ACTIVE);

        // ─── Notary Partners ──────────────────────────────────────────────
        $notaries = collect();
        $notaryData = [
            [
                'name' => 'Notaris Hendra Wijaya, S.H., M.Kn.',
                'license_number' => 'SK.M.01.HT.03.01-2024-0001',
                'office_name' => 'Kantor Notaris Hendra Wijaya',
                'address' => 'Jl. Sudirman No. 100, Jakarta Pusat',
                'phone' => '021-5551234', 'email' => 'hendra.notaris@example.com',
                'city' => 'Jakarta Pusat',
                'specializations' => ['Jual Beli Properti', 'AJB', 'Balik Nama'],
                'is_active' => true,
                'notes' => 'Notaris rekanan utama untuk area Jakarta Pusat dan Selatan.',
            ],
            [
                'name' => 'Notaris Maria Susanto, S.H., M.Kn.',
                'license_number' => 'SK.M.01.HT.03.01-2024-0002',
                'office_name' => 'Kantor Notaris Maria Susanto',
                'address' => 'Jl. Kelapa Gading Boulevard No. 50, Jakarta Utara',
                'phone' => '021-4551234', 'email' => 'maria.notaris@example.com',
                'city' => 'Jakarta Utara',
                'specializations' => ['Jual Beli Properti', 'PPJB', 'Sewa Menyewa'],
                'is_active' => true,
                'notes' => 'Notaris rekanan untuk area Jakarta Utara dan Timur.',
            ],
            [
                'name' => 'Notaris Bambang Sutrisno, S.H., M.Kn.',
                'license_number' => 'SK.M.01.HT.03.01-2024-0003',
                'office_name' => 'Kantor Notaris Bambang Sutrisno',
                'address' => 'Jl. BSD Raya No. 10, Tangerang Selatan',
                'phone' => '021-7551234', 'email' => 'bambang.notaris@example.com',
                'city' => 'Tangerang Selatan',
                'specializations' => ['Jual Beli Properti', 'AJB', 'PPJB'],
                'is_active' => true,
                'notes' => 'Notaris rekanan untuk area Tangerang dan sekitarnya.',
            ],
            [
                'name' => 'Notaris Ratna Dewi, S.H.',
                'license_number' => 'SK.M.01.HT.03.01-2023-0010',
                'office_name' => 'Kantor Notaris Ratna Dewi',
                'address' => 'Jl. Juanda No. 30, Bekasi',
                'phone' => '021-8801234', 'email' => 'ratna.notaris@example.com',
                'city' => 'Bekasi',
                'specializations' => ['Jual Beli Properti', 'Sewa Menyewa'],
                'is_active' => false,
                'notes' => 'Kontrak kerjasama berakhir.',
            ],
        ];
        foreach ($notaryData as $nd) {
            $notaries->push(NotaryPartner::create($nd));
        }

        // ─── Inquiries ────────────────────────────────────────────────────
        $inquiryData = [
            ['property' => 0, 'user' => $buyers[0], 'name' => 'Budi Santoso', 'email' => 'budi@example.com', 'phone' => '081234567891', 'message' => 'Saya tertarik dengan properti ini. Apakah bisa dijadwalkan viewing minggu depan?', 'method' => 'whatsapp', 'status' => InquiryStatus::CONTACTED],
            ['property' => 1, 'user' => $buyers[1], 'name' => 'Dewi Lestari', 'email' => 'dewi@example.com', 'phone' => '081234567893', 'message' => 'Mohon info lebih lanjut mengenai properti ini. Terima kasih.', 'method' => 'email', 'status' => InquiryStatus::NEW],
            ['property' => 6, 'user' => null, 'name' => 'Tamu Baru', 'email' => 'tamu@example.com', 'phone' => '081299998888', 'message' => 'Berapa harga sewa per tahun? Apakah bisa nego?', 'method' => 'phone', 'status' => InquiryStatus::NEW],
            ['property' => 0, 'user' => $buyers[2], 'name' => 'Rizky Pratama', 'email' => 'rizky@example.com', 'phone' => '081234567895', 'message' => 'Apakah masih tersedia? Saya ingin melihat langsung propertinya.', 'method' => 'whatsapp', 'status' => InquiryStatus::FOLLOWING_UP],
            ['property' => 4, 'user' => $buyers[3], 'name' => 'Anisa Putri', 'email' => 'anisa@example.com', 'phone' => '081234567896', 'message' => 'Saya tertarik. Apakah bisa cicilan bertahap ke developer?', 'method' => 'whatsapp', 'status' => InquiryStatus::NEW],
            ['property' => 2, 'user' => $buyers[4], 'name' => 'Fajar Nugroho', 'email' => 'fajar@example.com', 'phone' => '081234567897', 'message' => 'Ruko ini apakah cocok untuk restoran? Berapa daya listriknya?', 'method' => 'phone', 'status' => InquiryStatus::CONTACTED],
            ['property' => 11, 'user' => $buyers[0], 'name' => 'Budi Santoso', 'email' => 'budi@example.com', 'phone' => '081234567891', 'message' => 'Saya tertarik rumah di BSD. Bisa jadwalkan viewing?', 'method' => 'whatsapp', 'status' => InquiryStatus::CONVERTED],
            ['property' => 3, 'user' => null, 'name' => 'Investor Tanah', 'email' => 'investor@example.com', 'phone' => '081200001111', 'message' => 'Berapa harga per meter? Apakah bisa negosiasi untuk pembelian cepat?', 'method' => 'email', 'status' => InquiryStatus::CLOSED],
            ['property' => 9, 'user' => $buyers[3], 'name' => 'Anisa Putri', 'email' => 'anisa@example.com', 'phone' => '081234567896', 'message' => 'Rumah ini lokasi persis di mana ya? Dekat stasiun mana?', 'method' => 'whatsapp', 'status' => InquiryStatus::NEW],
            ['property' => 7, 'user' => null, 'name' => 'PT Logistik Jaya', 'email' => 'procurement@logistikjaya.com', 'phone' => '021-88776655', 'message' => 'Kami membutuhkan gudang untuk operasional. Apakah bisa sewa jangka panjang?', 'method' => 'email', 'status' => InquiryStatus::FOLLOWING_UP],
        ];

        foreach ($inquiryData as $i => $iq) {
            Inquiry::create([
                'property_id' => $activeProperties->values()->get($iq['property'])?->id ?? $activeProperties->first()->id,
                'user_id' => $iq['user']?->id,
                'name' => $iq['name'],
                'email' => $iq['email'],
                'phone' => $iq['phone'],
                'message' => $iq['message'],
                'preferred_contact_method' => $iq['method'],
                'status' => $iq['status'],
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        // ─── Buyer Requirements ───────────────────────────────────────────
        BuyerRequirement::create([
            'user_id' => $buyers[0]->id,
            'property_types' => [PropertyType::RUMAH->value, PropertyType::APARTEMEN->value],
            'listing_type' => ListingType::DIJUAL,
            'min_price' => 1_000_000_000, 'max_price' => 5_000_000_000,
            'min_land_area' => 100, 'max_land_area' => 500,
            'min_building_area' => 80, 'max_building_area' => 300,
            'min_bedrooms' => 3, 'min_bathrooms' => 2,
            'preferred_locations' => ['Jakarta Selatan', 'Tangerang Selatan'],
            'preferred_certificate_types' => [CertificateType::SHM->value],
            'urgency' => RequirementUrgency::TIGA_BULAN,
            'additional_notes' => 'Preferensi dekat sekolah internasional dan akses tol.',
            'status' => 'active',
        ]);

        BuyerRequirement::create([
            'user_id' => $buyers[1]->id,
            'property_types' => [PropertyType::APARTEMEN->value],
            'listing_type' => ListingType::DIJUAL,
            'min_price' => 2_000_000_000, 'max_price' => 5_000_000_000,
            'min_building_area' => 80,
            'min_bedrooms' => 2, 'min_bathrooms' => 1,
            'preferred_locations' => ['Jakarta Selatan', 'Jakarta Pusat'],
            'urgency' => RequirementUrgency::SEGERA,
            'additional_notes' => 'Harus sudah furnished. Prioritas view bagus.',
            'status' => 'active',
        ]);

        BuyerRequirement::create([
            'user_id' => $buyers[2]->id,
            'property_types' => [PropertyType::RUMAH->value],
            'listing_type' => ListingType::DIJUAL,
            'min_price' => 500_000_000, 'max_price' => 1_500_000_000,
            'min_land_area' => 60, 'min_building_area' => 50,
            'min_bedrooms' => 2,
            'preferred_locations' => ['Depok', 'Bekasi', 'Bogor'],
            'urgency' => RequirementUrgency::ENAM_BULAN,
            'additional_notes' => 'Budget terbatas, yang penting lokasi dekat transportasi umum.',
            'status' => 'active',
        ]);

        BuyerRequirement::create([
            'user_id' => $buyers[4]->id,
            'property_types' => [PropertyType::RUKO->value],
            'listing_type' => ListingType::DIJUAL,
            'min_price' => 2_000_000_000, 'max_price' => 7_000_000_000,
            'min_building_area' => 100,
            'preferred_locations' => ['Jakarta Utara', 'Jakarta Barat', 'Tangerang'],
            'urgency' => RequirementUrgency::SATU_BULAN,
            'additional_notes' => 'Untuk usaha F&B, butuh area parkir yang luas.',
            'status' => 'active',
        ]);

        // ─── Favorites ────────────────────────────────────────────────────
        $favoritesPairs = [
            [$buyers[0]->id, $properties[0]->id],
            [$buyers[0]->id, $properties[4]->id],
            [$buyers[0]->id, $properties[11]->id],
            [$buyers[1]->id, $properties[1]->id],
            [$buyers[1]->id, $properties[6]->id],
            [$buyers[2]->id, $properties[5]->id],
            [$buyers[2]->id, $properties[9]->id],
            [$buyers[3]->id, $properties[4]->id],
            [$buyers[3]->id, $properties[0]->id],
            [$buyers[4]->id, $properties[2]->id],
            [$buyers[4]->id, $properties[12]->id],
        ];
        foreach ($favoritesPairs as [$userId, $propId]) {
            Favorite::create(['user_id' => $userId, 'property_id' => $propId]);
        }

        // ─── Transactions ─────────────────────────────────────────────────

        // Transaction 1: COMPLETED (Pondok Indah property - sold)
        $tx1 = Transaction::create([
            'property_id' => $properties[10]->id, // Rumah Terjual Pondok Indah
            'buyer_id' => $buyers[1]->id,
            'seller_id' => $sellers[0]->id,
            'notary_id' => $notaries[0]->id,
            'assigned_admin_id' => $admin->id,
            'status' => TransactionStatus::COMPLETED,
            'agreed_price' => 24_000_000_000,
            'dp_amount' => 2_400_000_000,
            'commission_percentage' => 2.5,
            'commission_amount' => 600_000_000,
            'viewing_date' => now()->subMonths(4),
            'agreement_date' => now()->subMonths(3)->subWeeks(2),
            'dp_paid_date' => now()->subMonths(3),
            'notary_assigned_date' => now()->subMonths(2)->subWeeks(2),
            'ajb_date' => now()->subMonths(2),
            'completion_date' => now()->subMonths(1)->subWeeks(2),
            'completed_at' => now()->subMonths(1)->subWeeks(2),
            'notes' => 'Transaksi berjalan lancar. Pembeli sangat puas.',
        ]);

        $this->createTransactionActivities($tx1, $admin, [
            ['status_change', 'Transaksi dibuat dari inquiry', TransactionStatus::INQUIRY, now()->subMonths(5)],
            ['viewing_scheduled', 'Viewing dijadwalkan', TransactionStatus::VIEWING_SCHEDULED, now()->subMonths(4)->subDays(3)],
            ['status_change', 'Viewing selesai, pembeli tertarik', TransactionStatus::VIEWING_COMPLETED, now()->subMonths(4)],
            ['status_change', 'Masuk tahap negosiasi harga', TransactionStatus::NEGOTIATION, now()->subMonths(3)->subWeeks(3)],
            ['status_change', 'Kesepakatan harga Rp 24 M', TransactionStatus::AGREEMENT_REACHED, now()->subMonths(3)->subWeeks(2)],
            ['status_change', 'DP 10% dibayar', TransactionStatus::DP_RECEIVED, now()->subMonths(3)],
            ['status_change', 'Pengumpulan dokumen dimulai', TransactionStatus::DOCUMENT_COLLECTION, now()->subMonths(2)->subWeeks(3)],
            ['status_change', 'Notaris Hendra Wijaya ditugaskan', TransactionStatus::NOTARY_ASSIGNED, now()->subMonths(2)->subWeeks(2)],
            ['status_change', 'AJB ditandatangani', TransactionStatus::AJB_SIGNED, now()->subMonths(2)],
            ['status_change', 'Pembayaran lunas', TransactionStatus::PAYMENT_COMPLETED, now()->subMonths(1)->subWeeks(3)],
            ['status_change', 'Transaksi selesai, sertifikat diserahkan', TransactionStatus::COMPLETED, now()->subMonths(1)->subWeeks(2)],
        ]);

        // Transaction 2: In progress - DOCUMENT_COLLECTION
        $tx2 = Transaction::create([
            'property_id' => $properties[4]->id, // Rumah Cluster Bintaro
            'buyer_id' => $buyers[0]->id,
            'seller_id' => $sellers[0]->id,
            'assigned_admin_id' => $admin->id,
            'status' => TransactionStatus::DOCUMENT_COLLECTION,
            'agreed_price' => 2_650_000_000,
            'dp_amount' => 265_000_000,
            'commission_percentage' => 2.5,
            'commission_amount' => 66_250_000,
            'viewing_date' => now()->subWeeks(4),
            'agreement_date' => now()->subWeeks(2),
            'dp_paid_date' => now()->subWeeks(1),
            'notes' => 'Pembeli sedang mengumpulkan dokumen KTP dan KK.',
        ]);

        $this->createTransactionActivities($tx2, $admin, [
            ['status_change', 'Transaksi dibuat', TransactionStatus::INQUIRY, now()->subWeeks(5)],
            ['viewing_scheduled', 'Viewing dijadwalkan', TransactionStatus::VIEWING_SCHEDULED, now()->subWeeks(4)->subDays(2)],
            ['status_change', 'Viewing selesai', TransactionStatus::VIEWING_COMPLETED, now()->subWeeks(4)],
            ['status_change', 'Negosiasi harga dimulai', TransactionStatus::NEGOTIATION, now()->subWeeks(3)],
            ['status_change', 'Kesepakatan harga Rp 2.65 M', TransactionStatus::AGREEMENT_REACHED, now()->subWeeks(2)],
            ['status_change', 'DP 10% diterima', TransactionStatus::DP_RECEIVED, now()->subWeeks(1)],
            ['status_change', 'Mulai pengumpulan dokumen', TransactionStatus::DOCUMENT_COLLECTION, now()->subDays(3)],
        ]);

        // Transaction 3: Early stage - NEGOTIATION
        $tx3 = Transaction::create([
            'property_id' => $properties[11]->id, // Rumah di Tangerang
            'buyer_id' => $buyers[0]->id,
            'seller_id' => $sellers[1]->id,
            'assigned_admin_id' => $admin->id,
            'status' => TransactionStatus::NEGOTIATION,
            'viewing_date' => now()->subWeeks(2),
            'notes' => 'Pembeli menawar Rp 1.6 M, penjual meminta Rp 1.75 M.',
        ]);

        $this->createTransactionActivities($tx3, $admin, [
            ['status_change', 'Transaksi dibuat dari inquiry', TransactionStatus::INQUIRY, now()->subWeeks(3)],
            ['viewing_scheduled', 'Viewing dijadwalkan', TransactionStatus::VIEWING_SCHEDULED, now()->subWeeks(2)->subDays(2)],
            ['status_change', 'Viewing selesai, pembeli tertarik', TransactionStatus::VIEWING_COMPLETED, now()->subWeeks(2)],
            ['status_change', 'Masuk tahap negosiasi', TransactionStatus::NEGOTIATION, now()->subWeeks(1)],
        ]);

        // Transaction 4: CANCELLED
        $tx4 = Transaction::create([
            'property_id' => $properties[1]->id, // Apartemen Premium Sudirman
            'buyer_id' => $buyers[3]->id,
            'seller_id' => $sellers[0]->id,
            'assigned_admin_id' => $admin->id,
            'status' => TransactionStatus::CANCELLED,
            'viewing_date' => now()->subMonths(2),
            'cancellation_reason' => 'Pembeli membatalkan karena berubah pikiran mengenai lokasi.',
            'cancelled_at' => now()->subMonths(1),
            'notes' => 'Dibatalkan oleh pembeli pada tahap negosiasi.',
        ]);

        $this->createTransactionActivities($tx4, $admin, [
            ['status_change', 'Transaksi dibuat', TransactionStatus::INQUIRY, now()->subMonths(2)->subWeeks(1)],
            ['status_change', 'Viewing selesai', TransactionStatus::VIEWING_COMPLETED, now()->subMonths(2)],
            ['status_change', 'Negosiasi dimulai', TransactionStatus::NEGOTIATION, now()->subMonths(1)->subWeeks(2)],
            ['status_change', 'Dibatalkan oleh pembeli', TransactionStatus::CANCELLED, now()->subMonths(1)],
        ]);

        // Transaction 5: NOTARY_ASSIGNED stage
        $tx5 = Transaction::create([
            'property_id' => $properties[2]->id, // Ruko Kelapa Gading
            'buyer_id' => $buyers[4]->id,
            'seller_id' => $sellers[1]->id,
            'notary_id' => $notaries[1]->id,
            'assigned_admin_id' => $admin->id,
            'status' => TransactionStatus::NOTARY_ASSIGNED,
            'agreed_price' => 5_500_000_000,
            'dp_amount' => 550_000_000,
            'commission_percentage' => 2.5,
            'commission_amount' => 137_500_000,
            'viewing_date' => now()->subMonths(1)->subWeeks(2),
            'agreement_date' => now()->subMonths(1),
            'dp_paid_date' => now()->subWeeks(3),
            'notary_assigned_date' => now()->subDays(2),
            'notes' => 'Notaris Maria sudah ditugaskan, menunggu jadwal AJB.',
        ]);

        $this->createTransactionActivities($tx5, $admin, [
            ['status_change', 'Transaksi dibuat', TransactionStatus::INQUIRY, now()->subMonths(2)],
            ['status_change', 'Viewing selesai', TransactionStatus::VIEWING_COMPLETED, now()->subMonths(1)->subWeeks(2)],
            ['status_change', 'Kesepakatan harga Rp 5.5 M', TransactionStatus::AGREEMENT_REACHED, now()->subMonths(1)],
            ['status_change', 'DP diterima', TransactionStatus::DP_RECEIVED, now()->subWeeks(3)],
            ['status_change', 'Dokumen selesai diverifikasi', TransactionStatus::DOCUMENT_VERIFICATION, now()->subWeeks(1)],
            ['status_change', 'Notaris Maria Susanto ditugaskan', TransactionStatus::NOTARY_ASSIGNED, now()->subDays(2)],
        ]);

        // Transaction 6: VIEWING_SCHEDULED (early)
        $tx6 = Transaction::create([
            'property_id' => $properties[9]->id, // Rumah Asri Depok
            'buyer_id' => $buyers[2]->id,
            'seller_id' => $sellers[2]->id,
            'assigned_admin_id' => $admin->id,
            'status' => TransactionStatus::VIEWING_SCHEDULED,
            'notes' => 'Viewing dijadwalkan hari Sabtu depan.',
        ]);

        $this->createTransactionActivities($tx6, $admin, [
            ['status_change', 'Transaksi dibuat dari inquiry', TransactionStatus::INQUIRY, now()->subDays(5)],
            ['viewing_scheduled', 'Viewing dijadwalkan untuk Sabtu depan', TransactionStatus::VIEWING_SCHEDULED, now()->subDays(2)],
        ]);

        // ─── Property Viewings ────────────────────────────────────────────
        PropertyViewing::create([
            'property_id' => $properties[10]->id,
            'buyer_id' => $buyers[1]->id,
            'transaction_id' => $tx1->id,
            'scheduled_date' => now()->subMonths(4),
            'scheduled_time' => '10:00',
            'status' => 'completed',
            'feedback' => 'Rumah sangat bagus, sesuai ekspektasi. Lokasi strategis.',
            'rating' => 5,
        ]);

        PropertyViewing::create([
            'property_id' => $properties[4]->id,
            'buyer_id' => $buyers[0]->id,
            'transaction_id' => $tx2->id,
            'scheduled_date' => now()->subWeeks(4),
            'scheduled_time' => '14:00',
            'status' => 'completed',
            'feedback' => 'Cluster-nya bagus, aman. Tapi perlu renovasi sedikit.',
            'rating' => 4,
        ]);

        PropertyViewing::create([
            'property_id' => $properties[11]->id,
            'buyer_id' => $buyers[0]->id,
            'transaction_id' => $tx3->id,
            'scheduled_date' => now()->subWeeks(2),
            'scheduled_time' => '10:00',
            'status' => 'completed',
            'feedback' => 'Rumah bagus, lokasi dekat mall. Sedang negosiasi harga.',
            'rating' => 4,
        ]);

        PropertyViewing::create([
            'property_id' => $properties[1]->id,
            'buyer_id' => $buyers[3]->id,
            'transaction_id' => $tx4->id,
            'scheduled_date' => now()->subMonths(2),
            'scheduled_time' => '11:00',
            'status' => 'completed',
            'feedback' => 'Apartemen bagus tapi pembeli merasa lokasi kurang cocok.',
            'rating' => 3,
        ]);

        PropertyViewing::create([
            'property_id' => $properties[2]->id,
            'buyer_id' => $buyers[4]->id,
            'transaction_id' => $tx5->id,
            'scheduled_date' => now()->subMonths(1)->subWeeks(2),
            'scheduled_time' => '09:00',
            'status' => 'completed',
            'feedback' => 'Ruko sangat strategis, cocok untuk usaha F&B.',
            'rating' => 5,
        ]);

        PropertyViewing::create([
            'property_id' => $properties[9]->id,
            'buyer_id' => $buyers[2]->id,
            'transaction_id' => $tx6->id,
            'scheduled_date' => now()->addDays(3),
            'scheduled_time' => '10:00',
            'status' => 'scheduled',
        ]);

        // Extra viewings without transactions
        PropertyViewing::create([
            'property_id' => $properties[0]->id,
            'buyer_id' => $buyers[2]->id,
            'scheduled_date' => now()->subWeeks(3),
            'scheduled_time' => '14:00',
            'status' => 'completed',
            'feedback' => 'Rumah sangat mewah tapi di luar budget saya.',
            'rating' => 5,
        ]);

        PropertyViewing::create([
            'property_id' => $properties[0]->id,
            'buyer_id' => $buyers[3]->id,
            'scheduled_date' => now()->addDays(5),
            'scheduled_time' => '11:00',
            'status' => 'confirmed',
        ]);

        PropertyViewing::create([
            'property_id' => $properties[6]->id,
            'buyer_id' => $buyers[1]->id,
            'scheduled_date' => now()->subWeeks(1),
            'scheduled_time' => '15:00',
            'status' => 'no_show',
        ]);

        // ─── Documents ────────────────────────────────────────────────────

        // Documents for completed transaction (tx1)
        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx1->id,
            'uploaded_by' => $sellers[0]->id,
            'document_type' => DocumentType::SHM,
            'category' => 'seller_document',
            'file_path' => 'documents/tx1/shm-pondok-indah.pdf',
            'file_name' => 'SHM-Pondok-Indah.pdf',
            'file_size' => 2048000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now()->subMonths(2)->subWeeks(1),
        ]);

        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx1->id,
            'uploaded_by' => $sellers[0]->id,
            'document_type' => DocumentType::PBB,
            'category' => 'seller_document',
            'file_path' => 'documents/tx1/pbb-2025.pdf',
            'file_name' => 'PBB-2025.pdf',
            'file_size' => 512000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now()->subMonths(2)->subWeeks(1),
        ]);

        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx1->id,
            'uploaded_by' => $buyers[1]->id,
            'document_type' => DocumentType::KTP_PEMBELI,
            'category' => 'buyer_document',
            'file_path' => 'documents/tx1/ktp-dewi.pdf',
            'file_name' => 'KTP-Dewi-Lestari.pdf',
            'file_size' => 1024000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now()->subMonths(2)->subWeeks(1),
        ]);

        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx1->id,
            'uploaded_by' => $admin->id,
            'document_type' => DocumentType::AJB,
            'category' => 'transaction_document',
            'file_path' => 'documents/tx1/ajb-signed.pdf',
            'file_name' => 'AJB-Signed.pdf',
            'file_size' => 3072000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now()->subMonths(1)->subWeeks(2),
        ]);

        // Documents for in-progress transaction (tx2)
        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx2->id,
            'uploaded_by' => $buyers[0]->id,
            'document_type' => DocumentType::KTP_PEMBELI,
            'category' => 'buyer_document',
            'file_path' => 'documents/tx2/ktp-budi.pdf',
            'file_name' => 'KTP-Budi-Santoso.pdf',
            'file_size' => 980000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now()->subDays(2),
        ]);

        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx2->id,
            'uploaded_by' => $buyers[0]->id,
            'document_type' => DocumentType::KK_PEMBELI,
            'category' => 'buyer_document',
            'file_path' => 'documents/tx2/kk-budi.pdf',
            'file_name' => 'KK-Budi-Santoso.pdf',
            'file_size' => 1100000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::UNDER_REVIEW,
        ]);

        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx2->id,
            'uploaded_by' => $sellers[0]->id,
            'document_type' => DocumentType::SHM,
            'category' => 'seller_document',
            'file_path' => 'documents/tx2/shm-bintaro.pdf',
            'file_name' => 'SHM-Bintaro.pdf',
            'file_size' => 2200000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::PENDING,
        ]);

        // Documents for tx5 (notary stage)
        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx5->id,
            'uploaded_by' => $sellers[1]->id,
            'document_type' => DocumentType::SHGB,
            'category' => 'seller_document',
            'file_path' => 'documents/tx5/shgb-ruko.pdf',
            'file_name' => 'SHGB-Ruko-Kelapa-Gading.pdf',
            'file_size' => 1800000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now()->subWeeks(1),
        ]);

        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx5->id,
            'uploaded_by' => $buyers[4]->id,
            'document_type' => DocumentType::KTP_PEMBELI,
            'category' => 'buyer_document',
            'file_path' => 'documents/tx5/ktp-fajar.pdf',
            'file_name' => 'KTP-Fajar-Nugroho.pdf',
            'file_size' => 950000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now()->subWeeks(1),
        ]);

        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx5->id,
            'uploaded_by' => $buyers[4]->id,
            'document_type' => DocumentType::NPWP_PEMBELI,
            'category' => 'buyer_document',
            'file_path' => 'documents/tx5/npwp-fajar.pdf',
            'file_name' => 'NPWP-Fajar-Nugroho.pdf',
            'file_size' => 450000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::REJECTED,
            'verified_by' => $admin->id,
            'verified_at' => now()->subDays(3),
            'rejection_reason' => 'Dokumen tidak jelas/buram, mohon upload ulang dengan kualitas lebih baik.',
        ]);

        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx5->id,
            'uploaded_by' => $sellers[1]->id,
            'document_type' => DocumentType::PBB,
            'category' => 'seller_document',
            'file_path' => 'documents/tx5/pbb-ruko.pdf',
            'file_name' => 'PBB-Ruko-2025.pdf',
            'file_size' => 620000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::VERIFIED,
            'verified_by' => $admin->id,
            'verified_at' => now()->subWeeks(1),
        ]);

        Document::create([
            'documentable_type' => Transaction::class,
            'documentable_id' => $tx5->id,
            'uploaded_by' => $sellers[1]->id,
            'document_type' => DocumentType::IMB,
            'category' => 'seller_document',
            'file_path' => 'documents/tx5/imb-ruko.pdf',
            'file_name' => 'IMB-Ruko-Kelapa-Gading.pdf',
            'file_size' => 1500000,
            'mime_type' => 'application/pdf',
            'status' => DocumentStatus::UNDER_REVIEW,
        ]);
    }

    private function createTransactionActivities(Transaction $transaction, User $admin, array $activities): void
    {
        foreach ($activities as [$type, $description, $status, $date]) {
            TransactionActivity::create([
                'transaction_id' => $transaction->id,
                'user_id' => $admin->id,
                'activity_type' => $type,
                'description' => $description,
                'new_value' => $status->value,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
