<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Transaction } from '@/types';
import {
    FileCheck,
    Download,
    ArrowLeft,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ref } from 'vue';

type Props = {
    transactions: Transaction[];
    summary: {
        total_transactions: number;
        completed_transactions: number;
        cancelled_transactions: number;
        total_value: number;
        total_commission: number;
    };
    monthlyTrend: { month: string; count: number; total_value: number }[];
    filters: {
        start_date: string;
        end_date: string;
    };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Laporan', href: '/admin/laporan/transaksi' },
    { title: 'Transaksi', href: '/admin/laporan/transaksi' },
];

const startDate = ref(props.filters.start_date?.toString().slice(0, 10) ?? '');
const endDate = ref(props.filters.end_date?.toString().slice(0, 10) ?? '');

function applyFilter() {
    router.get('/admin/laporan/transaksi', {
        start_date: startDate.value || undefined,
        end_date: endDate.value || undefined,
    }, { preserveState: true });
}

function formatCurrency(value: number | null): string {
    if (!value) return 'Rp 0';
    if (value >= 1_000_000_000) return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    if (value >= 1_000_000) return `Rp ${(value / 1_000_000).toFixed(0)} Jt`;
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

const statusLabel: Record<string, string> = {
    inquiry: 'Inquiry',
    viewing_scheduled: 'Viewing Dijadwalkan',
    negotiation: 'Negosiasi',
    agreement_reached: 'Kesepakatan',
    dp_pending: 'Menunggu DP',
    dp_received: 'DP Diterima',
    document_collection: 'Pengumpulan Dokumen',
    notary_assigned: 'Notaris Ditugaskan',
    ajb_signing: 'Penandatanganan AJB',
    payment_completed: 'Pembayaran Selesai',
    completed: 'Selesai',
    cancelled: 'Dibatalkan',
    disputed: 'Sengketa',
};
</script>

<template>
    <Head title="Laporan Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link href="/admin/laporan/transaksi"><ArrowLeft class="h-4 w-4" /></Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Laporan Transaksi</h1>
                    <p class="text-muted-foreground text-sm">Detail semua transaksi dalam periode</p>
                </div>
            </div>

            <!-- Date Filter -->
            <Card>
                <CardContent class="flex flex-wrap items-end gap-4 p-4">
                    <div>
                        <Label>Dari Tanggal</Label>
                        <Input v-model="startDate" type="date" class="mt-1" />
                    </div>
                    <div>
                        <Label>Sampai Tanggal</Label>
                        <Input v-model="endDate" type="date" class="mt-1" />
                    </div>
                    <Button @click="applyFilter">Filter</Button>
                    <Button variant="outline" as-child>
                        <a :href="`/admin/laporan/export/transactions?start_date=${startDate}&end_date=${endDate}`">
                            <Download class="mr-2 h-4 w-4" />
                            Export CSV
                        </a>
                    </Button>
                </CardContent>
            </Card>

            <!-- Summary Stats -->
            <div class="grid gap-4 md:grid-cols-5">
                <Card>
                    <CardContent class="p-4 text-center">
                        <p class="text-2xl font-bold">{{ summary.total_transactions }}</p>
                        <p class="text-muted-foreground text-xs">Total Transaksi</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ summary.completed_transactions }}</p>
                        <p class="text-muted-foreground text-xs">Selesai</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4 text-center">
                        <p class="text-2xl font-bold text-red-600">{{ summary.cancelled_transactions }}</p>
                        <p class="text-muted-foreground text-xs">Dibatalkan</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4 text-center">
                        <p class="text-2xl font-bold">{{ formatCurrency(summary.total_value) }}</p>
                        <p class="text-muted-foreground text-xs">Total Nilai</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4 text-center">
                        <p class="text-2xl font-bold">{{ formatCurrency(summary.total_commission) }}</p>
                        <p class="text-muted-foreground text-xs">Total Komisi</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Monthly Trend -->
            <Card v-if="monthlyTrend.length > 0">
                <CardHeader><CardTitle>Tren Bulanan ({{ new Date().getFullYear() }})</CardTitle></CardHeader>
                <CardContent>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="px-4 py-3 text-left font-medium">Bulan</th>
                                <th class="px-4 py-3 text-right font-medium">Jumlah</th>
                                <th class="px-4 py-3 text-right font-medium">Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in monthlyTrend" :key="row.month" class="border-b last:border-0">
                                <td class="px-4 py-3 font-medium">{{ row.month }}</td>
                                <td class="px-4 py-3 text-right">{{ row.count }}</td>
                                <td class="px-4 py-3 text-right">{{ formatCurrency(row.total_value) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

            <!-- Transaction List -->
            <Card>
                <CardHeader><CardTitle>Daftar Transaksi ({{ transactions.length }})</CardTitle></CardHeader>
                <CardContent>
                    <div v-if="transactions.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                        Tidak ada transaksi dalam periode ini
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="px-3 py-3 text-left font-medium">No. Transaksi</th>
                                    <th class="px-3 py-3 text-left font-medium">Properti</th>
                                    <th class="px-3 py-3 text-left font-medium">Pembeli</th>
                                    <th class="px-3 py-3 text-left font-medium">Penjual</th>
                                    <th class="px-3 py-3 text-right font-medium">Harga</th>
                                    <th class="px-3 py-3 text-center font-medium">Status</th>
                                    <th class="px-3 py-3 text-right font-medium">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tx in transactions" :key="tx.id" class="border-b last:border-0 hover:bg-muted/30">
                                    <td class="px-3 py-3">
                                        <Link :href="`/admin/transaksi/${tx.id}`" class="text-primary hover:underline">
                                            {{ tx.transaction_number }}
                                        </Link>
                                    </td>
                                    <td class="px-3 py-3">{{ tx.property?.title ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ tx.buyer?.name ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ tx.seller?.name ?? '-' }}</td>
                                    <td class="px-3 py-3 text-right font-medium">{{ formatCurrency(tx.agreed_price) }}</td>
                                    <td class="px-3 py-3 text-center">
                                        <Badge variant="outline">{{ statusLabel[tx.status] ?? tx.status }}</Badge>
                                    </td>
                                    <td class="px-3 py-3 text-right">{{ formatDate(tx.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
