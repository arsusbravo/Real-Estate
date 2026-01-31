<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Property, type Transaction, type BuyerRequirement } from '@/types';
import {
    FileCheck,
    CheckCircle,
    Heart,
    ClipboardList,
    Building2,
    MapPin,
    BedDouble,
    Bath,
    ArrowRight,
    Sparkles,
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Props = {
    activeTransactions: Transaction[];
    recentFavorites: Property[];
    requirements: BuyerRequirement[];
    recommendedProperties: Property[];
    stats: {
        active_transactions: number;
        completed_transactions: number;
        favorites_count: number;
        requirements_count: number;
    };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Buyer Dashboard', href: '/buyer/dashboard' },
];

function formatCurrency(value: number): string {
    if (value >= 1_000_000_000) return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    if (value >= 1_000_000) return `Rp ${(value / 1_000_000).toFixed(0)} Jt`;
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

const statusLabels: Record<string, string> = {
    inquiry: 'Inquiry',
    viewing_scheduled: 'Jadwal Viewing',
    viewing_completed: 'Viewing Selesai',
    negotiation: 'Negosiasi',
    agreement_reached: 'Kesepakatan',
    dp_pending: 'Menunggu DP',
    dp_received: 'DP Diterima',
    document_collection: 'Kumpul Dokumen',
    document_verification: 'Verifikasi Dokumen',
    notary_assigned: 'Notaris Ditugaskan',
    notary_review: 'Review Notaris',
    ajb_preparation: 'Persiapan AJB',
    ajb_signing: 'Penandatanganan AJB',
    ajb_signed: 'AJB Ditandatangani',
    payment_processing: 'Proses Pembayaran',
    payment_completed: 'Pembayaran Selesai',
    certificate_transfer: 'Balik Nama',
    certificate_completed: 'Sertifikat Selesai',
    handover: 'Serah Terima',
    completed: 'Selesai',
    on_hold: 'Ditunda',
    cancelled: 'Dibatalkan',
    disputed: 'Sengketa',
};

const statusColors: Record<string, string> = {
    inquiry: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    viewing_scheduled: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    negotiation: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
    agreement_reached: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    dp_pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    dp_received: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    document_collection: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    disputed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
};

const propertyTypeLabel: Record<string, string> = {
    rumah: 'Rumah',
    apartemen: 'Apartemen',
    tanah: 'Tanah',
    ruko: 'Ruko',
    gudang: 'Gudang',
    kantor: 'Kantor',
};

const urgencyLabels: Record<string, string> = {
    segera: 'Segera',
    '1_bulan': '1 Bulan',
    '3_bulan': '3 Bulan',
    '6_bulan': '6 Bulan',
    fleksibel: 'Fleksibel',
};
</script>

<template>
    <Head title="Buyer Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Transaksi Aktif</CardTitle>
                        <FileCheck class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.active_transactions }}</div>
                        <p class="text-muted-foreground text-xs">Sedang dalam proses</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Transaksi Selesai</CardTitle>
                        <CheckCircle class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.completed_transactions }}</div>
                        <p class="text-muted-foreground text-xs">Total selesai</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Favorit</CardTitle>
                        <Heart class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.favorites_count }}</div>
                        <p class="text-muted-foreground text-xs">Properti disimpan</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Kebutuhan</CardTitle>
                        <ClipboardList class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.requirements_count }}</div>
                        <p class="text-muted-foreground text-xs">Kebutuhan terdaftar</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Content Grid -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Active Transactions -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Transaksi Aktif</CardTitle>
                                <CardDescription>Transaksi yang sedang berjalan</CardDescription>
                            </div>
                            <Button variant="outline" size="sm" as-child>
                                <Link href="/buyer/transaksi">Lihat Semua</Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="activeTransactions.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                            Belum ada transaksi aktif
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="transaction in activeTransactions"
                                :key="transaction.id"
                                class="flex items-center justify-between"
                            >
                                <div class="min-w-0 flex-1">
                                    <Link
                                        :href="`/buyer/transaksi/${transaction.uuid}`"
                                        class="text-sm font-medium hover:underline"
                                    >
                                        {{ transaction.transaction_number }}
                                    </Link>
                                    <p class="text-muted-foreground truncate text-xs">
                                        {{ transaction.property?.title ?? '-' }}
                                    </p>
                                    <p class="text-muted-foreground text-xs">
                                        {{ transaction.created_at ? formatDate(transaction.created_at) : '-' }}
                                    </p>
                                </div>
                                <span
                                    class="ml-2 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                    :class="statusColors[transaction.status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'"
                                >
                                    {{ statusLabels[transaction.status] ?? transaction.status }}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Requirements Summary -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Kebutuhan Properti</CardTitle>
                                <CardDescription>Ringkasan kebutuhan Anda</CardDescription>
                            </div>
                            <Button variant="outline" size="sm" as-child>
                                <Link href="/buyer/kebutuhan">Lihat Semua</Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="requirements.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                            <ClipboardList class="mx-auto mb-2 h-8 w-8 opacity-50" />
                            <p>Belum ada kebutuhan properti</p>
                            <Button variant="outline" size="sm" class="mt-3" as-child>
                                <Link href="/buyer/kebutuhan/buat">Buat Kebutuhan</Link>
                            </Button>
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="req in requirements"
                                :key="req.id"
                                class="rounded-lg border p-3"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex flex-wrap gap-1 mb-1">
                                            <Badge v-for="pt in req.property_types" :key="pt" variant="secondary" class="text-xs">
                                                {{ propertyTypeLabel[pt] ?? pt }}
                                            </Badge>
                                        </div>
                                        <p class="text-sm">
                                            {{ formatCurrency(req.min_price) }} - {{ formatCurrency(req.max_price) }}
                                        </p>
                                        <p class="text-muted-foreground text-xs">
                                            {{ req.preferred_locations.join(', ') }}
                                        </p>
                                    </div>
                                    <Badge variant="outline" class="text-xs">
                                        {{ urgencyLabels[req.urgency] ?? req.urgency }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Favorites -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Favorit Terbaru</CardTitle>
                            <CardDescription>Properti yang Anda simpan</CardDescription>
                        </div>
                        <Button variant="outline" size="sm" as-child>
                            <Link href="/buyer/favorit">
                                Lihat Semua
                                <ArrowRight class="ml-1 h-4 w-4" />
                            </Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="recentFavorites.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                        <Heart class="mx-auto mb-2 h-8 w-8 opacity-50" />
                        <p>Belum ada properti favorit</p>
                    </div>
                    <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <Link
                            v-for="property in recentFavorites"
                            :key="property.id"
                            :href="`/properti/${property.slug}`"
                            class="group block overflow-hidden rounded-lg border transition hover:shadow-md"
                        >
                            <div class="bg-muted flex h-36 items-center justify-center overflow-hidden">
                                <img
                                    v-if="property.media && property.media.length > 0"
                                    :src="property.media[0].original_url"
                                    :alt="property.title"
                                    class="h-full w-full object-cover transition group-hover:scale-105"
                                />
                                <Building2 v-else class="text-muted-foreground h-8 w-8" />
                            </div>
                            <div class="p-3">
                                <p class="truncate text-sm font-semibold group-hover:underline">{{ property.title }}</p>
                                <p class="text-sm font-medium text-primary">{{ formatCurrency(property.price) }}</p>
                                <p class="text-muted-foreground mt-1 flex items-center gap-1 text-xs">
                                    <MapPin class="h-3 w-3" />
                                    {{ property.city }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <!-- Recommended Properties -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle class="flex items-center gap-2">
                                <Sparkles class="h-5 w-5 text-yellow-500" />
                                Rekomendasi untuk Anda
                            </CardTitle>
                            <CardDescription>Berdasarkan kebutuhan dan preferensi Anda</CardDescription>
                        </div>
                        <Button variant="outline" size="sm" as-child>
                            <Link href="/properti">Cari Properti</Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="recommendedProperties.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                        <Sparkles class="mx-auto mb-2 h-8 w-8 opacity-50" />
                        <p>Belum ada rekomendasi. Buat kebutuhan properti untuk mendapatkan rekomendasi.</p>
                    </div>
                    <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <Link
                            v-for="property in recommendedProperties"
                            :key="property.id"
                            :href="`/properti/${property.slug}`"
                            class="group block overflow-hidden rounded-lg border transition hover:shadow-md"
                        >
                            <div class="bg-muted flex h-36 items-center justify-center overflow-hidden">
                                <img
                                    v-if="property.media && property.media.length > 0"
                                    :src="property.media[0].original_url"
                                    :alt="property.title"
                                    class="h-full w-full object-cover transition group-hover:scale-105"
                                />
                                <Building2 v-else class="text-muted-foreground h-8 w-8" />
                            </div>
                            <div class="p-3">
                                <p class="truncate text-sm font-semibold group-hover:underline">{{ property.title }}</p>
                                <p class="text-sm font-medium text-primary">{{ formatCurrency(property.price) }}</p>
                                <div class="text-muted-foreground mt-1 flex items-center gap-2 text-xs">
                                    <span class="flex items-center gap-1">
                                        <MapPin class="h-3 w-3" />
                                        {{ property.city }}
                                    </span>
                                    <span v-if="property.bedrooms" class="flex items-center gap-1">
                                        <BedDouble class="h-3 w-3" />
                                        {{ property.bedrooms }}
                                    </span>
                                    <span v-if="property.bathrooms" class="flex items-center gap-1">
                                        <Bath class="h-3 w-3" />
                                        {{ property.bathrooms }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
