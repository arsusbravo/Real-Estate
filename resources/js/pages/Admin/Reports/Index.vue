<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import {
    TrendingUp,
    DollarSign,
    Building2,
    FileCheck,
    ArrowRight,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

type Props = {
    stats: {
        total_transactions: number;
        completed_transactions: number;
        total_revenue: number;
        avg_transaction_value: number;
        properties_sold: number;
        properties_rented: number;
    };
    monthly_data: {
        month: string;
        transactions: number;
        revenue: number;
    }[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Laporan', href: '/admin/laporan' },
];

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

function formatCurrencyFull(value: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
}
</script>

<template>
    <Head title="Laporan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold">Laporan</h1>
                <p class="text-muted-foreground text-sm">Ringkasan data dan performa bisnis</p>
            </div>

            <!-- Stat Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Transaksi</CardTitle>
                        <FileCheck class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_transactions }}</div>
                        <p class="text-muted-foreground text-xs">
                            {{ stats.completed_transactions }} selesai
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Pendapatan</CardTitle>
                        <DollarSign class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ formatCurrency(stats.total_revenue) }}</div>
                        <p class="text-muted-foreground text-xs">
                            Rata-rata {{ formatCurrency(stats.avg_transaction_value) }}/transaksi
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Properti Terjual</CardTitle>
                        <Building2 class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.properties_sold }}</div>
                        <p class="text-muted-foreground text-xs">unit terjual</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Properti Tersewa</CardTitle>
                        <TrendingUp class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.properties_rented }}</div>
                        <p class="text-muted-foreground text-xs">unit tersewa</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Monthly Data Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Data Bulanan</CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="monthly_data.length === 0" class="text-muted-foreground py-6 text-center text-sm">
                        Belum ada data bulanan
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="px-4 py-3 text-left font-medium">Bulan</th>
                                    <th class="px-4 py-3 text-right font-medium">Transaksi</th>
                                    <th class="px-4 py-3 text-right font-medium">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="row in monthly_data"
                                    :key="row.month"
                                    class="border-b last:border-0 hover:bg-muted/30"
                                >
                                    <td class="px-4 py-3 font-medium">{{ row.month }}</td>
                                    <td class="px-4 py-3 text-right">{{ row.transactions }}</td>
                                    <td class="px-4 py-3 text-right font-medium">{{ formatCurrencyFull(row.revenue) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Report Links -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card class="hover:bg-muted/30 transition-colors">
                    <CardContent class="flex items-center justify-between p-4">
                        <div>
                            <p class="font-medium">Laporan Transaksi</p>
                            <p class="text-muted-foreground text-sm">Detail transaksi lengkap</p>
                        </div>
                        <Button variant="ghost" size="icon" as-child>
                            <Link href="/admin/laporan/transaksi">
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </Button>
                    </CardContent>
                </Card>

                <Card class="hover:bg-muted/30 transition-colors">
                    <CardContent class="flex items-center justify-between p-4">
                        <div>
                            <p class="font-medium">Laporan Properti</p>
                            <p class="text-muted-foreground text-sm">Statistik properti</p>
                        </div>
                        <Button variant="ghost" size="icon" as-child>
                            <Link href="/admin/laporan/properti">
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </Button>
                    </CardContent>
                </Card>

                <Card class="hover:bg-muted/30 transition-colors">
                    <CardContent class="flex items-center justify-between p-4">
                        <div>
                            <p class="font-medium">Laporan Pendapatan</p>
                            <p class="text-muted-foreground text-sm">Analisis pendapatan</p>
                        </div>
                        <Button variant="ghost" size="icon" as-child>
                            <Link href="/admin/laporan/pendapatan">
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
