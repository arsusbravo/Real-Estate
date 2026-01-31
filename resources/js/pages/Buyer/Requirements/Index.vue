<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type BuyerRequirement } from '@/types';
import {
    ClipboardList,
    Plus,
    MapPin,
    Target,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Props = {
    requirements: BuyerRequirement[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Buyer', href: '/buyer/dashboard' },
    { title: 'Kebutuhan', href: '/buyer/kebutuhan' },
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

const urgencyColors: Record<string, string> = {
    segera: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    '1_bulan': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    '3_bulan': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    '6_bulan': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    fleksibel: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
};

const statusLabels: Record<string, string> = {
    active: 'Aktif',
    matched: 'Cocok',
    closed: 'Ditutup',
};

const statusColors: Record<string, string> = {
    active: 'default',
    matched: 'secondary',
    closed: 'outline',
};
</script>

<template>
    <Head title="Kebutuhan Properti" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Kebutuhan Properti</h1>
                    <p class="text-muted-foreground text-sm">Kelola kebutuhan dan preferensi properti Anda</p>
                </div>
                <Button as-child>
                    <Link href="/buyer/kebutuhan/buat">
                        <Plus class="mr-2 h-4 w-4" />
                        Buat Kebutuhan Baru
                    </Link>
                </Button>
            </div>

            <!-- Empty State -->
            <div v-if="requirements.length === 0" class="flex flex-col items-center justify-center py-16">
                <ClipboardList class="text-muted-foreground mb-4 h-16 w-16 opacity-50" />
                <h3 class="text-lg font-semibold">Belum ada kebutuhan properti</h3>
                <p class="text-muted-foreground mt-1 max-w-md text-center text-sm">
                    Buat kebutuhan properti Anda agar kami bisa mencarikan properti yang sesuai.
                </p>
                <Button class="mt-4" as-child>
                    <Link href="/buyer/kebutuhan/buat">
                        <Plus class="mr-2 h-4 w-4" />
                        Buat Kebutuhan
                    </Link>
                </Button>
            </div>

            <!-- Requirements List -->
            <div v-else class="space-y-4">
                <Card v-for="req in requirements" :key="req.id">
                    <CardContent class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Property Types -->
                                <div class="flex flex-wrap gap-1.5 mb-2">
                                    <Badge v-for="pt in req.property_types" :key="pt" variant="secondary">
                                        {{ propertyTypeLabel[pt] ?? pt }}
                                    </Badge>
                                    <Badge variant="outline">
                                        {{ req.listing_type === 'beli' ? 'Beli' : 'Sewa' }}
                                    </Badge>
                                </div>

                                <!-- Budget -->
                                <p class="text-lg font-semibold">
                                    {{ formatCurrency(req.min_price) }} - {{ formatCurrency(req.max_price) }}
                                </p>

                                <!-- Locations -->
                                <p class="text-muted-foreground mt-1 flex items-center gap-1 text-sm">
                                    <MapPin class="h-3.5 w-3.5" />
                                    {{ req.preferred_locations.join(', ') || 'Semua lokasi' }}
                                </p>

                                <!-- Specs -->
                                <div class="text-muted-foreground mt-2 flex flex-wrap gap-3 text-sm">
                                    <span v-if="req.min_bedrooms">Min. {{ req.min_bedrooms }} kamar tidur</span>
                                    <span v-if="req.min_bathrooms">Min. {{ req.min_bathrooms }} kamar mandi</span>
                                    <span v-if="req.min_land_area">LT {{ req.min_land_area }}-{{ req.max_land_area ?? '...' }} m&sup2;</span>
                                    <span v-if="req.min_building_area">LB {{ req.min_building_area }}-{{ req.max_building_area ?? '...' }} m&sup2;</span>
                                </div>

                                <!-- Additional Notes -->
                                <p v-if="req.additional_notes" class="text-muted-foreground mt-2 text-sm italic">
                                    "{{ req.additional_notes }}"
                                </p>

                                <!-- Date -->
                                <p class="text-muted-foreground mt-2 text-xs">
                                    Dibuat {{ formatDate(req.created_at) }}
                                </p>
                            </div>

                            <!-- Status & Urgency -->
                            <div class="ml-4 flex flex-col items-end gap-2">
                                <Badge :variant="(statusColors[req.status] as any) ?? 'secondary'">
                                    {{ statusLabels[req.status] ?? req.status }}
                                </Badge>
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="urgencyColors[req.urgency] ?? 'bg-gray-100 text-gray-800'"
                                >
                                    <Target class="mr-1 h-3 w-3" />
                                    {{ urgencyLabels[req.urgency] ?? req.urgency }}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
