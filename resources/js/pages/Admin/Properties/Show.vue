<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Property, type Inquiry, type Transaction } from '@/types';
import {
    Building2,
    MapPin,
    Ruler,
    BedDouble,
    Bath,
    Car,
    Star,
    CheckCircle,
    XCircle,
    Eye,
    Calendar,
    User,
    Phone,
    Mail,
    FileText,
    ArrowLeft,
    Layers,
    Home,
    Shield,
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';

type Props = {
    property: Property;
    inquiries: Inquiry[];
    transactions: Transaction[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Properti', href: '/admin/properti' },
    { title: props.property.title },
];

const showRejectDialog = ref(false);
const rejectReason = ref('');

function formatCurrency(value: number): string {
    if (value >= 1_000_000_000) {
        return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    }
    if (value >= 1_000_000) {
        return `Rp ${(value / 1_000_000).toFixed(0)} Jt`;
    }
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

const statusLabel: Record<string, string> = {
    draft: 'Draf',
    pending_review: 'Menunggu Review',
    active: 'Aktif',
    sold: 'Terjual',
    rented: 'Tersewa',
    inactive: 'Nonaktif',
    rejected: 'Ditolak',
};

const statusColor: Record<string, string> = {
    draft: 'secondary',
    pending_review: 'outline',
    active: 'default',
    sold: 'destructive',
    rented: 'destructive',
    inactive: 'secondary',
    rejected: 'destructive',
};

const propertyTypeLabel: Record<string, string> = {
    rumah: 'Rumah',
    apartemen: 'Apartemen',
    tanah: 'Tanah',
    ruko: 'Ruko',
    gudang: 'Gudang',
    kantor: 'Kantor',
};

const certificateTypeLabel: Record<string, string> = {
    shm: 'SHM',
    shgb: 'SHGB',
    shp: 'SHP',
    girik: 'Girik',
    strata_title: 'Strata Title',
};

const listingTypeLabel: Record<string, string> = {
    dijual: 'Dijual',
    disewakan: 'Disewakan',
};

const furnishingLabel: Record<string, string> = {
    unfurnished: 'Tanpa Perabot',
    semi_furnished: 'Semi Furnished',
    fully_furnished: 'Full Furnished',
};

const transactionStatusLabels: Record<string, string> = {
    inquiry: 'Inquiry',
    viewing_scheduled: 'Jadwal Viewing',
    negotiation: 'Negosiasi',
    agreement_reached: 'Kesepakatan',
    dp_pending: 'Menunggu DP',
    dp_received: 'DP Diterima',
    document_collection: 'Kumpul Dokumen',
    document_verification: 'Verifikasi Dokumen',
    notary_assigned: 'Notaris Ditugaskan',
    completed: 'Selesai',
    cancelled: 'Dibatalkan',
};

function approveProperty() {
    router.put(`/admin/properti/${props.property.id}/approve`);
}

function rejectProperty() {
    if (!rejectReason.value.trim()) return;
    router.put(`/admin/properti/${props.property.id}/reject`, {
        rejection_reason: rejectReason.value,
    });
}

function toggleFeatured() {
    router.put(`/admin/properti/${props.property.id}/feature`);
}
</script>

<template>
    <Head :title="property.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Button variant="outline" size="icon" as-child>
                        <Link href="/admin/properti">
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold">{{ property.title }}</h1>
                        <div class="flex items-center gap-2 mt-1">
                            <Badge :variant="(statusColor[property.status] as any) ?? 'secondary'">
                                {{ statusLabel[property.status] ?? property.status }}
                            </Badge>
                            <Badge v-if="property.featured" variant="outline" class="text-yellow-600 border-yellow-400">
                                <Star class="mr-1 h-3 w-3 fill-yellow-400" />
                                Featured
                            </Badge>
                            <Badge v-if="property.verified" variant="outline" class="text-green-600 border-green-400">
                                <Shield class="mr-1 h-3 w-3" />
                                Terverifikasi
                            </Badge>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        :variant="property.featured ? 'outline' : 'default'"
                        size="sm"
                        @click="toggleFeatured"
                    >
                        <Star class="mr-2 h-4 w-4" :class="property.featured ? 'fill-yellow-400 text-yellow-400' : ''" />
                        {{ property.featured ? 'Hapus Featured' : 'Jadikan Featured' }}
                    </Button>
                </div>
            </div>

            <!-- Approve/Reject Actions for Pending -->
            <Card v-if="property.status === 'pending_review'" class="border-orange-200 dark:border-orange-800">
                <CardHeader>
                    <CardTitle class="text-orange-600 dark:text-orange-400">Properti Menunggu Persetujuan</CardTitle>
                    <CardDescription>Silakan review properti ini dan tentukan tindakan.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="!showRejectDialog" class="flex gap-3">
                        <Button @click="approveProperty" class="bg-green-600 hover:bg-green-700">
                            <CheckCircle class="mr-2 h-4 w-4" />
                            Setujui Properti
                        </Button>
                        <Button variant="destructive" @click="showRejectDialog = true">
                            <XCircle class="mr-2 h-4 w-4" />
                            Tolak Properti
                        </Button>
                    </div>
                    <div v-else class="space-y-3">
                        <label class="text-sm font-medium">Alasan Penolakan</label>
                        <textarea
                            v-model="rejectReason"
                            rows="3"
                            class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                            placeholder="Jelaskan alasan penolakan..."
                        />
                        <div class="flex gap-2">
                            <Button variant="destructive" @click="rejectProperty" :disabled="!rejectReason.trim()">
                                Konfirmasi Tolak
                            </Button>
                            <Button variant="outline" @click="showRejectDialog = false; rejectReason = ''">
                                Batal
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Rejection Info -->
            <Card v-if="property.status === 'rejected' && property.rejection_reason" class="border-red-200 dark:border-red-800">
                <CardHeader>
                    <CardTitle class="text-red-600 dark:text-red-400">Properti Ditolak</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm">{{ property.rejection_reason }}</p>
                </CardContent>
            </Card>

            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Main Info -->
                <div class="space-y-4 lg:col-span-2">
                    <!-- Images -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Foto Properti</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="property.media && property.media.length > 0" class="grid grid-cols-2 gap-3 md:grid-cols-3">
                                <div
                                    v-for="media in property.media"
                                    :key="media.id"
                                    class="bg-muted aspect-video overflow-hidden rounded-lg"
                                >
                                    <img
                                        :src="media.original_url"
                                        :alt="media.file_name"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                            </div>
                            <div v-else class="text-muted-foreground py-8 text-center text-sm">
                                <Building2 class="mx-auto mb-2 h-8 w-8 opacity-50" />
                                Belum ada foto
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Detail Properti -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Detail Properti</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                                <div>
                                    <p class="text-muted-foreground text-xs">Tipe Properti</p>
                                    <p class="text-sm font-medium">{{ propertyTypeLabel[property.property_type] ?? property.property_type }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Tipe Listing</p>
                                    <p class="text-sm font-medium">{{ listingTypeLabel[property.listing_type] ?? property.listing_type }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Harga</p>
                                    <p class="text-sm font-bold">{{ formatCurrency(property.price) }}</p>
                                    <p v-if="property.price_negotiable" class="text-xs text-green-600">Bisa nego</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Luas Tanah</p>
                                    <p class="text-sm font-medium flex items-center gap-1">
                                        <Ruler class="h-3 w-3" /> {{ property.land_area }} m&sup2;
                                    </p>
                                </div>
                                <div v-if="property.building_area">
                                    <p class="text-muted-foreground text-xs">Luas Bangunan</p>
                                    <p class="text-sm font-medium flex items-center gap-1">
                                        <Home class="h-3 w-3" /> {{ property.building_area }} m&sup2;
                                    </p>
                                </div>
                                <div v-if="property.bedrooms">
                                    <p class="text-muted-foreground text-xs">Kamar Tidur</p>
                                    <p class="text-sm font-medium flex items-center gap-1">
                                        <BedDouble class="h-3 w-3" /> {{ property.bedrooms }}
                                    </p>
                                </div>
                                <div v-if="property.bathrooms">
                                    <p class="text-muted-foreground text-xs">Kamar Mandi</p>
                                    <p class="text-sm font-medium flex items-center gap-1">
                                        <Bath class="h-3 w-3" /> {{ property.bathrooms }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Jumlah Lantai</p>
                                    <p class="text-sm font-medium flex items-center gap-1">
                                        <Layers class="h-3 w-3" /> {{ property.floors }}
                                    </p>
                                </div>
                                <div v-if="property.parking_spaces">
                                    <p class="text-muted-foreground text-xs">Parkir</p>
                                    <p class="text-sm font-medium flex items-center gap-1">
                                        <Car class="h-3 w-3" /> {{ property.parking_spaces }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Furnishing</p>
                                    <p class="text-sm font-medium">{{ furnishingLabel[property.furnishing] ?? property.furnishing }}</p>
                                </div>
                                <div v-if="property.facing_direction">
                                    <p class="text-muted-foreground text-xs">Hadap</p>
                                    <p class="text-sm font-medium">{{ property.facing_direction }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Deskripsi -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Deskripsi</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm whitespace-pre-line">{{ property.description }}</p>
                        </CardContent>
                    </Card>

                    <!-- Lokasi -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Lokasi</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2">
                                <div class="flex items-start gap-2">
                                    <MapPin class="text-muted-foreground mt-0.5 h-4 w-4 flex-shrink-0" />
                                    <div>
                                        <p class="text-sm font-medium">{{ property.address }}</p>
                                        <p class="text-muted-foreground text-sm">
                                            {{ property.subdistrict ? property.subdistrict + ', ' : '' }}{{ property.district }}, {{ property.city }}, {{ property.province }}
                                            {{ property.postal_code ? `(${property.postal_code})` : '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Sertifikat -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Sertifikat</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                                <div>
                                    <p class="text-muted-foreground text-xs">Tipe Sertifikat</p>
                                    <p class="text-sm font-medium">{{ certificateTypeLabel[property.certificate_type] ?? property.certificate_type }}</p>
                                </div>
                                <div v-if="property.certificate_number">
                                    <p class="text-muted-foreground text-xs">Nomor Sertifikat</p>
                                    <p class="text-sm font-medium">{{ property.certificate_number }}</p>
                                </div>
                                <div v-if="property.certificate_expiry">
                                    <p class="text-muted-foreground text-xs">Masa Berlaku</p>
                                    <p class="text-sm font-medium">{{ formatDate(property.certificate_expiry) }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Fitur -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Fitur</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="property.features && property.features.length > 0" class="flex flex-wrap gap-2">
                                <Badge v-for="feature in property.features" :key="feature.id" variant="secondary">
                                    {{ feature.feature_name }}
                                </Badge>
                            </div>
                            <p v-else class="text-muted-foreground text-sm">Belum ada fitur ditambahkan</p>
                        </CardContent>
                    </Card>

                    <!-- Inquiry untuk properti ini -->
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <CardTitle>Inquiry ({{ inquiries.length }})</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div v-if="inquiries.length === 0" class="text-muted-foreground py-6 text-center text-sm">
                                Belum ada inquiry untuk properti ini
                            </div>
                            <div v-else class="space-y-3">
                                <div
                                    v-for="inquiry in inquiries"
                                    :key="inquiry.id"
                                    class="flex items-center justify-between rounded-lg border p-3"
                                >
                                    <div>
                                        <p class="text-sm font-medium">{{ inquiry.name }}</p>
                                        <p class="text-muted-foreground text-xs">{{ inquiry.email }} &middot; {{ formatDate(inquiry.created_at) }}</p>
                                        <p class="text-muted-foreground mt-1 line-clamp-1 text-xs">{{ inquiry.message }}</p>
                                    </div>
                                    <Badge
                                        :variant="inquiry.status === 'new' ? 'default' : 'secondary'"
                                    >
                                        {{ inquiry.status === 'new' ? 'Baru' : inquiry.status === 'contacted' ? 'Dihubungi' : inquiry.status === 'converted' ? 'Konversi' : 'Ditutup' }}
                                    </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Transaksi -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Transaksi ({{ transactions.length }})</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="transactions.length === 0" class="text-muted-foreground py-6 text-center text-sm">
                                Belum ada transaksi untuk properti ini
                            </div>
                            <div v-else class="space-y-3">
                                <div
                                    v-for="transaction in transactions"
                                    :key="transaction.id"
                                    class="flex items-center justify-between rounded-lg border p-3"
                                >
                                    <div>
                                        <Link
                                            :href="`/admin/transaksi/${transaction.uuid}`"
                                            class="text-sm font-medium hover:underline"
                                        >
                                            {{ transaction.transaction_number }}
                                        </Link>
                                        <p class="text-muted-foreground text-xs">
                                            Pembeli: {{ transaction.buyer?.name ?? '-' }}
                                        </p>
                                        <p class="text-xs font-medium" v-if="transaction.agreed_price">
                                            {{ formatCurrency(transaction.agreed_price) }}
                                        </p>
                                    </div>
                                    <Badge variant="secondary">
                                        {{ transactionStatusLabels[transaction.status] ?? transaction.status }}
                                    </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4">
                    <!-- Info Penjual -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Penjual</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="property.seller" class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="bg-muted flex h-10 w-10 items-center justify-center rounded-full">
                                        <User class="text-muted-foreground h-5 w-5" />
                                    </div>
                                    <div>
                                        <Link
                                            :href="`/admin/pengguna/${property.seller.id}`"
                                            class="text-sm font-medium hover:underline"
                                        >
                                            {{ property.seller.name }}
                                        </Link>
                                        <p class="text-muted-foreground text-xs">Penjual</p>
                                    </div>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center gap-2">
                                        <Mail class="text-muted-foreground h-3 w-3" />
                                        <span>{{ property.seller.email }}</span>
                                    </div>
                                    <div v-if="property.seller.phone" class="flex items-center gap-2">
                                        <Phone class="text-muted-foreground h-3 w-3" />
                                        <span>{{ property.seller.phone }}</span>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-muted-foreground text-sm">Data penjual tidak tersedia</p>
                        </CardContent>
                    </Card>

                    <!-- Info Tanggal -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Tanggal</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3 text-sm">
                                <div>
                                    <p class="text-muted-foreground text-xs">Dibuat</p>
                                    <p class="font-medium">{{ formatDate(property.created_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Diperbarui</p>
                                    <p class="font-medium">{{ formatDate(property.updated_at) }}</p>
                                </div>
                                <div v-if="property.published_at">
                                    <p class="text-muted-foreground text-xs">Dipublish</p>
                                    <p class="font-medium">{{ formatDate(property.published_at) }}</p>
                                </div>
                                <div v-if="property.sold_at">
                                    <p class="text-muted-foreground text-xs">Terjual</p>
                                    <p class="font-medium">{{ formatDate(property.sold_at) }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Statistik -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Statistik</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">Inquiry</span>
                                    <span class="font-medium">{{ property.inquiries_count ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">Favorit</span>
                                    <span class="font-medium">{{ property.favorites_count ?? 0 }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">Viewing</span>
                                    <span class="font-medium">{{ property.viewings_count ?? 0 }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
