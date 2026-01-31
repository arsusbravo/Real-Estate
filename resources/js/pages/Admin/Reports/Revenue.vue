<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import {
    DollarSign,
    TrendingUp,
    TrendingDown,
    ArrowLeft,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

type Props = {
    monthlyRevenue: { month: string; revenue: number; transactions: number }[];
    totalRevenue: number;
    totalValue: number;
    lastYearRevenue: number;
    year: number;
    availableYears: number[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Laporan', href: '/admin/laporan/transaksi' },
    { title: 'Pendapatan', href: '/admin/laporan/pendapatan' },
];

function changeYear(year: number) {
    router.get('/admin/laporan/pendapatan', { year }, { preserveState: true });
}

function formatCurrency(value: number | null): string {
    if (!value) return 'Rp 0';
    if (value >= 1_000_000_000) return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    if (value >= 1_000_000) return `Rp ${(value / 1_000_000).toFixed(0)} Jt`;
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

function formatCurrencyFull(value: number | null): string {
    if (!value) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
}

const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

function getMonthName(month: string): string {
    const idx = parseInt(month, 10) - 1;
    return monthNames[idx] ?? month;
}

const growthPercent = props.lastYearRevenue > 0
    ? (((props.totalRevenue - props.lastYearRevenue) / props.lastYearRevenue) * 100).toFixed(1)
    : null;
const isGrowth = props.totalRevenue >= props.lastYearRevenue;
</script>

<template>
    <Head title="Laporan Pendapatan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link href="/admin/laporan/transaksi"><ArrowLeft class="h-4 w-4" /></Link>
                </Button>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold">Laporan Pendapatan</h1>
                    <p class="text-muted-foreground text-sm">Analisis pendapatan komisi</p>
                </div>
                <!-- Year Selector -->
                <div class="flex gap-2">
                    <Button
                        v-for="y in availableYears"
                        :key="y"
                        :variant="y === year ? 'default' : 'outline'"
                        size="sm"
                        @click="changeYear(y)"
                    >
                        {{ y }}
                    </Button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Pendapatan {{ year }}</CardTitle>
                        <DollarSign class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(totalRevenue) }}</div>
                        <div v-if="growthPercent !== null" class="mt-1 flex items-center gap-1 text-xs">
                            <TrendingUp v-if="isGrowth" class="h-3 w-3 text-green-600" />
                            <TrendingDown v-else class="h-3 w-3 text-red-600" />
                            <span :class="isGrowth ? 'text-green-600' : 'text-red-600'">
                                {{ isGrowth ? '+' : '' }}{{ growthPercent }}% dari tahun lalu
                            </span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Nilai Transaksi</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(totalValue) }}</div>
                        <p class="text-muted-foreground text-xs">Nilai transaksi selesai</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pendapatan Tahun Lalu</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(lastYearRevenue) }}</div>
                        <p class="text-muted-foreground text-xs">{{ year - 1 }}</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Monthly Revenue Table -->
            <Card>
                <CardHeader><CardTitle>Pendapatan Bulanan {{ year }}</CardTitle></CardHeader>
                <CardContent>
                    <div v-if="monthlyRevenue.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                        Belum ada data pendapatan untuk tahun {{ year }}
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="px-4 py-3 text-left font-medium">Bulan</th>
                                    <th class="px-4 py-3 text-right font-medium">Transaksi</th>
                                    <th class="px-4 py-3 text-right font-medium">Pendapatan Komisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in monthlyRevenue" :key="row.month" class="border-b last:border-0 hover:bg-muted/30">
                                    <td class="px-4 py-3 font-medium">{{ getMonthName(row.month) }}</td>
                                    <td class="px-4 py-3 text-right">{{ row.transactions }}</td>
                                    <td class="px-4 py-3 text-right font-medium">{{ formatCurrencyFull(row.revenue) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 font-bold">
                                    <td class="px-4 py-3">Total</td>
                                    <td class="px-4 py-3 text-right">{{ monthlyRevenue.reduce((s, r) => s + r.transactions, 0) }}</td>
                                    <td class="px-4 py-3 text-right">{{ formatCurrencyFull(totalRevenue) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
