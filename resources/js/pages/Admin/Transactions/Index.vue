<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Transaction, type Paginated } from '@/types';
import {
    Search,
    FileCheck,
    Plus,
    Eye,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ref, watch } from 'vue';

type Props = {
    transactions: Paginated<Transaction>;
    filters: {
        status?: string;
        search?: string;
    };
    statusOptions: { value: string; label: string }[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Transaksi', href: '/admin/transaksi' },
];

const search = ref(props.filters.search ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/transaksi', { search: val || undefined, status: props.filters.status }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

function filterByStatus(status?: string) {
    router.get('/admin/transaksi', { status, search: search.value || undefined }, {
        preserveState: true,
        replace: true,
    });
}

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
    document_verification: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    notary_assigned: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
    completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    disputed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
};
</script>

<template>
    <Head title="Kelola Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Kelola Transaksi</h1>
                    <p class="text-muted-foreground text-sm">{{ transactions.total }} total transaksi</p>
                </div>
                <Button as-child>
                    <Link href="/admin/transaksi/buat">
                        <Plus class="mr-2 h-4 w-4" />
                        Buat Transaksi
                    </Link>
                </Button>
            </div>

            <!-- Status Tabs -->
            <div class="flex flex-wrap gap-2">
                <Button
                    :variant="!filters.status ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus(undefined)"
                >
                    Semua
                </Button>
                <Button
                    v-for="option in statusOptions"
                    :key="option.value"
                    :variant="filters.status === option.value ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus(option.value)"
                >
                    {{ option.label }}
                </Button>
            </div>

            <!-- Search -->
            <div class="relative max-w-sm">
                <Search class="text-muted-foreground absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" />
                <Input
                    v-model="search"
                    type="search"
                    placeholder="Cari nomor transaksi..."
                    class="pl-10"
                />
            </div>

            <!-- Empty State -->
            <div v-if="transactions.data.length === 0" class="text-muted-foreground py-12 text-center">
                <FileCheck class="mx-auto mb-4 h-12 w-12 opacity-50" />
                <p>Tidak ada transaksi ditemukan</p>
            </div>

            <!-- Transactions Table -->
            <div v-else class="overflow-x-auto rounded-lg border">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-muted/50 border-b">
                            <th class="px-4 py-3 text-left font-medium">No. Transaksi</th>
                            <th class="px-4 py-3 text-left font-medium">Properti</th>
                            <th class="px-4 py-3 text-left font-medium">Pembeli</th>
                            <th class="px-4 py-3 text-left font-medium">Penjual</th>
                            <th class="px-4 py-3 text-left font-medium">Status</th>
                            <th class="px-4 py-3 text-right font-medium">Harga</th>
                            <th class="px-4 py-3 text-left font-medium">Tanggal</th>
                            <th class="px-4 py-3 text-center font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="transaction in transactions.data"
                            :key="transaction.id"
                            class="border-b last:border-0 hover:bg-muted/30"
                        >
                            <td class="px-4 py-3">
                                <Link
                                    :href="`/admin/transaksi/${transaction.uuid}`"
                                    class="font-medium hover:underline"
                                >
                                    {{ transaction.transaction_number }}
                                </Link>
                            </td>
                            <td class="px-4 py-3">
                                <p class="max-w-[200px] truncate">{{ transaction.property?.title ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3">{{ transaction.buyer?.name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ transaction.seller?.name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                    :class="statusColors[transaction.status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'"
                                >
                                    {{ statusLabels[transaction.status] ?? transaction.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-medium">
                                {{ transaction.agreed_price ? formatCurrency(transaction.agreed_price) : '-' }}
                            </td>
                            <td class="text-muted-foreground px-4 py-3">
                                {{ formatDate(transaction.created_at) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <Button variant="ghost" size="icon" as-child>
                                    <Link :href="`/admin/transaksi/${transaction.uuid}`">
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
