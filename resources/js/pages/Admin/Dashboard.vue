<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Property, type Transaction, type Inquiry } from '@/types';
import {
    Building2,
    Users,
    FileCheck,
    MessageSquare,
    TrendingUp,
    Clock,
    CheckCircle,
    AlertCircle,
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Props = {
    metrics: {
        total_properties: number;
        active_properties: number;
        pending_properties: number;
        total_users: number;
        total_transactions: number;
        active_transactions: number;
        completed_transactions: number;
        new_inquiries: number;
    };
    revenueThisMonth: number;
    recentTransactions: Transaction[];
    pendingProperties: Property[];
    transactionsByStatus: Record<string, number>;
    recentInquiries: Inquiry[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
];

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
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
    completed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    disputed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Key Metrics -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Properti -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Properti</CardTitle>
                        <Building2 class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ metrics.active_properties }}</div>
                        <p class="text-muted-foreground text-xs">
                            <span v-if="metrics.pending_properties > 0" class="text-orange-600">
                                {{ metrics.pending_properties }} menunggu persetujuan
                            </span>
                            <span v-else>Semua properti aktif</span>
                        </p>
                    </CardContent>
                </Card>

                <!-- Total Transaksi Aktif -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Transaksi Aktif</CardTitle>
                        <FileCheck class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ metrics.active_transactions }}</div>
                        <p class="text-muted-foreground text-xs">
                            {{ metrics.completed_transactions }} transaksi selesai
                        </p>
                    </CardContent>
                </Card>

                <!-- Total Pengguna -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Pengguna</CardTitle>
                        <Users class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ metrics.total_users }}</div>
                        <p class="text-muted-foreground text-xs">Pengguna terdaftar</p>
                    </CardContent>
                </Card>

                <!-- Inquiry Baru -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Inquiry Baru</CardTitle>
                        <MessageSquare class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ metrics.new_inquiries }}</div>
                        <p class="text-muted-foreground text-xs">Belum ditanggapi</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Revenue Card -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">Pendapatan Komisi Bulan Ini</CardTitle>
                    <TrendingUp class="text-muted-foreground h-4 w-4" />
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-bold">{{ formatCurrency(revenueThisMonth ?? 0) }}</div>
                </CardContent>
            </Card>

            <!-- Content Grid -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Recent Transactions -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Transaksi Terbaru</CardTitle>
                                <CardDescription>{{ metrics.total_transactions }} total transaksi</CardDescription>
                            </div>
                            <Button variant="outline" size="sm" as-child>
                                <Link href="/admin/transaksi">Lihat Semua</Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="recentTransactions.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                            Belum ada transaksi
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="transaction in recentTransactions"
                                :key="transaction.id"
                                class="flex items-center justify-between"
                            >
                                <div class="min-w-0 flex-1">
                                    <Link
                                        :href="`/admin/transaksi/${transaction.uuid}`"
                                        class="text-sm font-medium hover:underline"
                                    >
                                        {{ transaction.transaction_number }}
                                    </Link>
                                    <p class="text-muted-foreground truncate text-xs">
                                        {{ transaction.property?.title ?? '-' }}
                                    </p>
                                    <p class="text-muted-foreground text-xs">
                                        {{ transaction.buyer?.name ?? '-' }}
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

                <!-- Pending Properties -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Properti Menunggu Persetujuan</CardTitle>
                                <CardDescription>{{ metrics.pending_properties }} properti pending</CardDescription>
                            </div>
                            <Button variant="outline" size="sm" as-child>
                                <Link href="/admin/properti/pending">Lihat Semua</Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="pendingProperties.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                            <CheckCircle class="mx-auto mb-2 h-8 w-8 text-green-500" />
                            Tidak ada properti yang menunggu persetujuan
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="property in pendingProperties"
                                :key="property.id"
                                class="flex items-center justify-between"
                            >
                                <div class="min-w-0 flex-1">
                                    <Link
                                        :href="`/admin/properti/${property.id}`"
                                        class="text-sm font-medium hover:underline"
                                    >
                                        {{ property.title }}
                                    </Link>
                                    <p class="text-muted-foreground text-xs">
                                        {{ property.seller?.name ?? '-' }} &middot; {{ formatDate(property.created_at) }}
                                    </p>
                                    <p class="text-xs font-medium">{{ formatCurrency(property.price) }}</p>
                                </div>
                                <Badge variant="outline" class="ml-2">
                                    <Clock class="mr-1 h-3 w-3" />
                                    Pending
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Inquiries -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Inquiry Terbaru</CardTitle>
                                <CardDescription>Pertanyaan dari calon pembeli</CardDescription>
                            </div>
                            <Button variant="outline" size="sm" as-child>
                                <Link href="/admin/inquiry">Lihat Semua</Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="recentInquiries.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                            Belum ada inquiry
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="inquiry in recentInquiries"
                                :key="inquiry.id"
                                class="flex items-start justify-between"
                            >
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium">{{ inquiry.name }}</p>
                                    <p class="text-muted-foreground truncate text-xs">
                                        {{ inquiry.property?.title ?? '-' }}
                                    </p>
                                    <p class="text-muted-foreground line-clamp-1 text-xs">{{ inquiry.message }}</p>
                                </div>
                                <span
                                    class="ml-2 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                    :class="inquiry.status === 'new'
                                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'
                                    "
                                >
                                    {{ inquiry.status === 'new' ? 'Baru' : inquiry.status === 'contacted' ? 'Dihubungi' : inquiry.status === 'converted' ? 'Konversi' : 'Ditutup' }}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Transaction Status Distribution -->
                <Card>
                    <CardHeader>
                        <CardTitle>Distribusi Status Transaksi</CardTitle>
                        <CardDescription>Jumlah transaksi per status</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="Object.keys(transactionsByStatus).length === 0" class="text-muted-foreground py-8 text-center text-sm">
                            Belum ada data transaksi
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="(count, status) in transactionsByStatus"
                                :key="status"
                                class="flex items-center justify-between"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                        :class="statusColors[status as string] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'"
                                    >
                                        {{ statusLabels[status as string] ?? status }}
                                    </span>
                                </div>
                                <span class="text-sm font-medium">{{ count }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <Card>
                <CardHeader>
                    <CardTitle>Aksi Cepat</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap gap-3">
                        <Button as-child>
                            <Link href="/admin/transaksi/buat">
                                <FileCheck class="mr-2 h-4 w-4" />
                                Buat Transaksi
                            </Link>
                        </Button>
                        <Button variant="outline" as-child>
                            <Link href="/admin/properti/pending">
                                <AlertCircle class="mr-2 h-4 w-4" />
                                Review Properti ({{ metrics.pending_properties }})
                            </Link>
                        </Button>
                        <Button variant="outline" as-child>
                            <Link href="/admin/notaris/buat">
                                <Scale class="mr-2 h-4 w-4" />
                                Tambah Notaris
                            </Link>
                        </Button>
                        <Button variant="outline" as-child>
                            <Link href="/admin/pengguna">
                                <Users class="mr-2 h-4 w-4" />
                                Kelola Pengguna
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
