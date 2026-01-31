<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Transaction, type Paginated } from '@/types';
import {
    FileCheck,
    Calendar,
    User,
    Building2,
} from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Props = {
    transactions: Paginated<Transaction>;
    filters: {
        status?: string;
    };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Seller', href: '/seller/dashboard' },
    { title: 'Transaksi', href: '/seller/transaksi' },
];

function filterByStatus(status?: string) {
    router.get('/seller/transaksi', { status }, {
        preserveState: true,
        replace: true,
    });
}

function formatCurrency(value: number | null): string {
    if (!value) return '-';
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
    viewing_completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    negotiation: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
    agreement_reached: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    dp_pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    dp_received: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    document_collection: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    document_verification: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    notary_assigned: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    disputed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
};
</script>

<template>
    <Head title="Transaksi Saya" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold">Transaksi Saya</h1>
                <p class="text-muted-foreground text-sm">{{ transactions.total }} total transaksi</p>
            </div>

            <!-- Status Filter -->
            <div class="flex flex-wrap gap-2">
                <Button
                    :variant="!filters.status ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus(undefined)"
                >
                    Semua
                </Button>
                <Button
                    :variant="filters.status === 'completed' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('completed')"
                >
                    Selesai
                </Button>
                <Button
                    :variant="filters.status === 'cancelled' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('cancelled')"
                >
                    Dibatalkan
                </Button>
            </div>

            <!-- Transactions List -->
            <div v-if="transactions.data.length === 0" class="text-muted-foreground py-12 text-center">
                <FileCheck class="mx-auto mb-4 h-12 w-12 opacity-50" />
                <p>Tidak ada transaksi ditemukan</p>
            </div>

            <div v-else class="space-y-3">
                <Link
                    v-for="transaction in transactions.data"
                    :key="transaction.id"
                    :href="`/seller/transaksi/${transaction.id}`"
                    class="block"
                >
                    <Card class="transition hover:shadow-md">
                        <CardContent class="p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold">{{ transaction.transaction_number }}</p>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                            :class="statusColors[transaction.status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'"
                                        >
                                            {{ statusLabels[transaction.status] ?? transaction.status }}
                                        </span>
                                    </div>

                                    <div class="mt-2 space-y-1">
                                        <p class="text-muted-foreground flex items-center gap-1 text-sm">
                                            <Building2 class="h-3.5 w-3.5" />
                                            {{ transaction.property?.title ?? '-' }}
                                        </p>
                                        <p class="text-muted-foreground flex items-center gap-1 text-sm">
                                            <User class="h-3.5 w-3.5" />
                                            Pembeli: {{ transaction.buyer?.name ?? '-' }}
                                        </p>
                                    </div>

                                    <div class="text-muted-foreground mt-2 flex items-center gap-3 text-xs">
                                        <span v-if="transaction.agreed_price" class="font-medium text-foreground">
                                            {{ formatCurrency(transaction.agreed_price) }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <Calendar class="h-3 w-3" />
                                            {{ formatDate(transaction.created_at) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="transactions.last_page > 1" class="flex items-center justify-center gap-2 py-4">
                <template v-for="link in transactions.links" :key="link.label">
                    <Button
                        v-if="link.url"
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        as-child
                    >
                        <Link :href="link.url" v-html="link.label" />
                    </Button>
                    <Button
                        v-else
                        variant="outline"
                        size="sm"
                        disabled
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
