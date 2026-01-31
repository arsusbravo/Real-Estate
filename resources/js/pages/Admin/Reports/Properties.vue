<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Property } from '@/types';
import {
    Building2,
    Eye,
    MessageSquare,
    ArrowLeft,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Props = {
    byStatus: Record<string, number>;
    byType: Record<string, number>;
    byCity: { city: string; count: number }[];
    topViewed: Property[];
    mostInquired: Property[];
    totals: {
        all: number;
        active: number;
        total_views: number;
        total_inquiries: number;
    };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Laporan', href: '/admin/laporan/transaksi' },
    { title: 'Properti', href: '/admin/laporan/properti' },
];

const statusLabel: Record<string, string> = {
    draft: 'Draf',
    pending_review: 'Menunggu Review',
    active: 'Aktif',
    sold: 'Terjual',
    rented: 'Tersewa',
    inactive: 'Nonaktif',
    rejected: 'Ditolak',
};

const typeLabel: Record<string, string> = {
    rumah: 'Rumah',
    apartemen: 'Apartemen',
    tanah: 'Tanah',
    ruko: 'Ruko',
    gudang: 'Gudang',
    kantor: 'Kantor',
};

function formatCurrency(value: number): string {
    if (value >= 1_000_000_000) return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    if (value >= 1_000_000) return `Rp ${(value / 1_000_000).toFixed(0)} Jt`;
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}
</script>

<template>
    <Head title="Laporan Properti" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link href="/admin/laporan/transaksi"><ArrowLeft class="h-4 w-4" /></Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Laporan Properti</h1>
                    <p class="text-muted-foreground text-sm">Statistik dan analisis properti</p>
                </div>
            </div>

            <!-- Totals -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardContent class="p-4 text-center">
                        <p class="text-2xl font-bold">{{ totals.all }}</p>
                        <p class="text-muted-foreground text-xs">Total Properti</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ totals.active }}</p>
                        <p class="text-muted-foreground text-xs">Aktif</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4 text-center">
                        <p class="text-2xl font-bold">{{ totals.total_views }}</p>
                        <p class="text-muted-foreground text-xs">Total Views</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4 text-center">
                        <p class="text-2xl font-bold">{{ totals.total_inquiries }}</p>
                        <p class="text-muted-foreground text-xs">Total Inquiry</p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <!-- By Status -->
                <Card>
                    <CardHeader><CardTitle>Berdasarkan Status</CardTitle></CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-for="(count, status) in byStatus" :key="status" class="flex items-center justify-between">
                                <span class="text-sm">{{ statusLabel[status as string] ?? status }}</span>
                                <Badge variant="secondary">{{ count }}</Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- By Type -->
                <Card>
                    <CardHeader><CardTitle>Berdasarkan Tipe</CardTitle></CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div v-for="(count, type) in byType" :key="type" class="flex items-center justify-between">
                                <span class="text-sm">{{ typeLabel[type as string] ?? type }}</span>
                                <Badge variant="secondary">{{ count }}</Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- By City -->
            <Card>
                <CardHeader><CardTitle>Berdasarkan Kota</CardTitle></CardHeader>
                <CardContent>
                    <div v-if="byCity.length === 0" class="text-muted-foreground py-4 text-center text-sm">Belum ada data</div>
                    <div v-else class="space-y-3">
                        <div v-for="row in byCity" :key="row.city" class="flex items-center justify-between">
                            <span class="text-sm">{{ row.city }}</span>
                            <Badge variant="outline">{{ row.count }} properti</Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Top Viewed -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Eye class="h-5 w-5" /> Paling Banyak Dilihat
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="topViewed.length === 0" class="text-muted-foreground py-4 text-center text-sm">Belum ada data</div>
                    <div v-else class="space-y-3">
                        <div v-for="(prop, idx) in topViewed" :key="prop.id" class="flex items-center gap-3 rounded-lg border p-3">
                            <span class="text-muted-foreground w-6 text-center text-sm font-bold">{{ idx + 1 }}</span>
                            <div class="min-w-0 flex-1">
                                <Link :href="`/admin/properti/${prop.id}`" class="font-medium hover:underline">{{ prop.title }}</Link>
                                <p class="text-muted-foreground text-xs">{{ formatCurrency(prop.price) }}</p>
                            </div>
                            <Badge variant="outline">{{ prop.views_count ?? 0 }} views</Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Most Inquired -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <MessageSquare class="h-5 w-5" /> Paling Banyak Inquiry
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="mostInquired.length === 0" class="text-muted-foreground py-4 text-center text-sm">Belum ada data</div>
                    <div v-else class="space-y-3">
                        <div v-for="(prop, idx) in mostInquired" :key="prop.id" class="flex items-center gap-3 rounded-lg border p-3">
                            <span class="text-muted-foreground w-6 text-center text-sm font-bold">{{ idx + 1 }}</span>
                            <div class="min-w-0 flex-1">
                                <Link :href="`/admin/properti/${prop.id}`" class="font-medium hover:underline">{{ prop.title }}</Link>
                                <p class="text-muted-foreground text-xs">{{ formatCurrency(prop.price) }}</p>
                            </div>
                            <Badge variant="outline">{{ prop.inquiries_count ?? 0 }} inquiry</Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
