<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Transaction } from '@/types';
import { FileCheck, ArrowRight } from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Props = {
    transactions: Transaction[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Buyer', href: '/buyer/dashboard' },
    { title: 'Transaksi', href: '/buyer/transaksi' },
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
    viewing_completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    negotiation: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
    agreement_reached: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    dp_pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    dp_received: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    document_collection: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    document_verification: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    notary_assigned: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
    notary_review: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
    ajb_preparation: 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200',
    ajb_signing: 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200',
    ajb_signed: 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200',
    payment_processing: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
    payment_completed: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
    certificate_transfer: 'bg-lime-100 text-lime-800 dark:bg-lime-900 dark:text-lime-200',
    certificate_completed: 'bg-lime-100 text-lime-800 dark:bg-lime-900 dark:text-lime-200',
    handover: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    on_hold: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
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
                <p class="text-muted-foreground text-sm">{{ transactions.length }} total transaksi</p>
            </div>

            <!-- Empty State -->
            <div v-if="transactions.length === 0" class="flex flex-col items-center justify-center py-16">
                <FileCheck class="text-muted-foreground mb-4 h-16 w-16 opacity-50" />
                <h3 class="text-lg font-semibold">Belum ada transaksi</h3>
                <p class="text-muted-foreground mt-1 text-sm">Transaksi akan muncul setelah Anda memulai proses pembelian.</p>
                <Button class="mt-4" as-child>
                    <Link href="/properti">Cari Properti</Link>
                </Button>
            </div>

            <!-- Transactions List -->
            <div v-else class="space-y-3">
                <Card v-for="transaction in transactions" :key="transaction.id">
                    <CardContent class="flex items-center gap-4 p-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="`/buyer/transaksi/${transaction.uuid}`"
                                    class="font-semibold hover:underline"
                                >
                                    {{ transaction.transaction_number }}
                                </Link>
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="statusColors[transaction.status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'"
                                >
                                    {{ statusLabels[transaction.status] ?? transaction.status }}
                                </span>
                            </div>
                            <p class="text-muted-foreground mt-1 truncate text-sm">
                                {{ transaction.property?.title ?? '-' }}
                            </p>
                            <div class="text-muted-foreground mt-1 flex items-center gap-4 text-xs">
                                <span>Dibuat: {{ formatDate(transaction.created_at) }}</span>
                                <span v-if="transaction.agreed_price">
                                    Harga: {{ formatCurrency(transaction.agreed_price) }}
                                </span>
                                <span v-if="transaction.completed_at">
                                    Selesai: {{ formatDate(transaction.completed_at) }}
                                </span>
                            </div>
                        </div>
                        <Button variant="ghost" size="sm" as-child>
                            <Link :href="`/buyer/transaksi/${transaction.uuid}`">
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
