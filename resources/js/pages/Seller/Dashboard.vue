<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Property, type Inquiry } from '@/types';
import {
    Building2,
    Home,
    Clock,
    MessageSquare,
    FileCheck,
    Activity,
    Plus,
    Eye,
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Props = {
    stats: {
        total_listings: number;
        active_listings: number;
        pending_listings: number;
        total_inquiries: number;
        total_transactions: number;
        active_transactions: number;
    };
    recent_inquiries: Inquiry[];
    recent_properties: Property[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Seller', href: '/seller/dashboard' },
    { title: 'Dashboard', href: '/seller/dashboard' },
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

const propertyStatusLabel: Record<string, string> = {
    draft: 'Draf',
    pending_review: 'Menunggu Review',
    active: 'Aktif',
    sold: 'Terjual',
    rented: 'Tersewa',
    inactive: 'Nonaktif',
    rejected: 'Ditolak',
};

const propertyStatusColor: Record<string, string> = {
    draft: 'secondary',
    pending_review: 'outline',
    active: 'default',
    sold: 'destructive',
    rented: 'destructive',
    inactive: 'secondary',
    rejected: 'destructive',
};

const inquiryStatusLabel: Record<string, string> = {
    new: 'Baru',
    contacted: 'Dihubungi',
    converted: 'Konversi',
    closed: 'Ditutup',
};

const inquiryStatusColor: Record<string, string> = {
    new: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    contacted: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    converted: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    closed: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
};
</script>

<template>
    <Head title="Seller Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Stat Cards Grid 3x2 -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Listing</CardTitle>
                        <Building2 class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_listings }}</div>
                        <p class="text-muted-foreground text-xs">Semua properti Anda</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Listing Aktif</CardTitle>
                        <Home class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.active_listings }}</div>
                        <p class="text-muted-foreground text-xs">Properti yang tayang</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Listing Pending</CardTitle>
                        <Clock class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.pending_listings }}</div>
                        <p class="text-muted-foreground text-xs">Menunggu persetujuan</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Inquiry</CardTitle>
                        <MessageSquare class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_inquiries }}</div>
                        <p class="text-muted-foreground text-xs">Pertanyaan masuk</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Transaksi</CardTitle>
                        <FileCheck class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_transactions }}</div>
                        <p class="text-muted-foreground text-xs">Semua transaksi</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Transaksi Aktif</CardTitle>
                        <Activity class="text-muted-foreground h-4 w-4" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.active_transactions }}</div>
                        <p class="text-muted-foreground text-xs">Sedang dalam proses</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <div class="flex flex-wrap gap-3">
                <Button as-child>
                    <Link href="/seller/properti/buat">
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Properti
                    </Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/seller/inquiry">
                        <Eye class="mr-2 h-4 w-4" />
                        Lihat Inquiry
                    </Link>
                </Button>
            </div>

            <!-- Content Grid -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Recent Inquiries -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Inquiry Terbaru</CardTitle>
                                <CardDescription>Pertanyaan dari calon pembeli</CardDescription>
                            </div>
                            <Button variant="outline" size="sm" as-child>
                                <Link href="/seller/inquiry">Lihat Semua</Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="recent_inquiries.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                            <MessageSquare class="mx-auto mb-2 h-8 w-8 opacity-50" />
                            Belum ada inquiry
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="inquiry in recent_inquiries"
                                :key="inquiry.id"
                                class="flex items-start justify-between"
                            >
                                <div class="min-w-0 flex-1">
                                    <Link
                                        :href="`/seller/inquiry/${inquiry.id}`"
                                        class="text-sm font-medium hover:underline"
                                    >
                                        {{ inquiry.name }}
                                    </Link>
                                    <p class="text-muted-foreground truncate text-xs">
                                        {{ inquiry.property?.title ?? '-' }}
                                    </p>
                                    <p class="text-muted-foreground text-xs">
                                        {{ formatDate(inquiry.created_at) }}
                                    </p>
                                </div>
                                <span
                                    class="ml-2 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                                    :class="inquiryStatusColor[inquiry.status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'"
                                >
                                    {{ inquiryStatusLabel[inquiry.status] ?? inquiry.status }}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Properties -->
                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Properti Terbaru</CardTitle>
                                <CardDescription>Listing properti Anda</CardDescription>
                            </div>
                            <Button variant="outline" size="sm" as-child>
                                <Link href="/seller/properti">Lihat Semua</Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="recent_properties.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                            <Building2 class="mx-auto mb-2 h-8 w-8 opacity-50" />
                            Belum ada properti
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="property in recent_properties"
                                :key="property.id"
                                class="flex items-center justify-between"
                            >
                                <div class="min-w-0 flex-1">
                                    <Link
                                        :href="`/seller/properti/${property.id}/edit`"
                                        class="text-sm font-medium hover:underline"
                                    >
                                        {{ property.title }}
                                    </Link>
                                    <p class="text-xs font-medium">{{ formatCurrency(property.price) }}</p>
                                    <p class="text-muted-foreground text-xs">
                                        {{ formatDate(property.created_at) }}
                                    </p>
                                </div>
                                <Badge :variant="(propertyStatusColor[property.status] as any) ?? 'secondary'">
                                    {{ propertyStatusLabel[property.status] ?? property.status }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
