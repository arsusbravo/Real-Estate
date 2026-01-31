<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type User, type Property, type Transaction, type BuyerRequirement } from '@/types';
import {
    User as UserIcon,
    Mail,
    Phone,
    Calendar,
    Building2,
    ShoppingBag,
    FileText,
    Heart,
    Eye,
    MoreHorizontal,
    Shield,
    ShieldCheck,
    ShieldX,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Props = {
    user: User & {
        properties?: Property[];
        buyer_transactions?: Transaction[];
        seller_transactions?: Transaction[];
        requirements?: BuyerRequirement[];
        properties_count?: number;
        buyer_transactions_count?: number;
        seller_transactions_count?: number;
        favorites_count?: number;
    };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Pengguna', href: '/admin/pengguna' },
    { title: props.user.name, href: `/admin/pengguna/${props.user.id}` },
];

const roleLabel: Record<string, string> = {
    admin: 'Admin',
    buyer: 'Pembeli',
    seller: 'Penjual',
};

const roleColor: Record<string, string> = {
    admin: 'destructive',
    buyer: 'default',
    seller: 'secondary',
};

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function formatCurrency(value: number): string {
    if (value >= 1_000_000_000) return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    if (value >= 1_000_000) return `Rp ${(value / 1_000_000).toFixed(0)} Jt`;
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

const statusLabel: Record<string, string> = {
    draft: 'Draf',
    pending_review: 'Menunggu Review',
    active: 'Aktif',
    sold: 'Terjual',
    rented: 'Tersewa',
    inactive: 'Nonaktif',
    rejected: 'Ditolak',
};

function toggleStatus() {
    if (confirm(props.user.email_verified_at ? 'Nonaktifkan pengguna ini?' : 'Aktifkan pengguna ini?')) {
        router.put(`/admin/pengguna/${props.user.id}/toggle-status`);
    }
}
</script>

<template>
    <Head :title="`Pengguna: ${user.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- User Header -->
            <Card>
                <CardContent class="flex items-start gap-6 p-6">
                    <div class="bg-muted flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full">
                        <UserIcon class="text-muted-foreground h-8 w-8" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold">{{ user.name }}</h1>
                            <Badge :variant="(roleColor[user.role] as any) ?? 'secondary'">
                                {{ roleLabel[user.role] ?? user.role }}
                            </Badge>
                            <Badge v-if="user.email_verified_at" variant="outline" class="text-green-600 border-green-300">
                                Aktif
                            </Badge>
                            <Badge v-else variant="outline" class="text-red-600 border-red-300">
                                Nonaktif
                            </Badge>
                        </div>
                        <div class="text-muted-foreground mt-2 flex flex-wrap gap-4 text-sm">
                            <span class="flex items-center gap-1">
                                <Mail class="h-4 w-4" /> {{ user.email }}
                            </span>
                            <span v-if="user.phone" class="flex items-center gap-1">
                                <Phone class="h-4 w-4" /> {{ user.phone }}
                            </span>
                            <span class="flex items-center gap-1">
                                <Calendar class="h-4 w-4" /> Bergabung {{ formatDate(user.created_at) }}
                            </span>
                        </div>
                    </div>
                    <Button
                        :variant="user.email_verified_at ? 'outline' : 'default'"
                        size="sm"
                        @click="toggleStatus"
                    >
                        <ShieldX v-if="user.email_verified_at" class="mr-2 h-4 w-4" />
                        <ShieldCheck v-else class="mr-2 h-4 w-4" />
                        {{ user.email_verified_at ? 'Nonaktifkan' : 'Aktifkan' }}
                    </Button>
                </CardContent>
            </Card>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardContent class="flex items-center gap-3 p-4">
                        <Building2 class="text-muted-foreground h-8 w-8" />
                        <div>
                            <p class="text-2xl font-bold">{{ user.properties_count ?? 0 }}</p>
                            <p class="text-muted-foreground text-sm">Properti</p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-center gap-3 p-4">
                        <ShoppingBag class="text-muted-foreground h-8 w-8" />
                        <div>
                            <p class="text-2xl font-bold">{{ user.buyer_transactions_count ?? 0 }}</p>
                            <p class="text-muted-foreground text-sm">Transaksi (Pembeli)</p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-center gap-3 p-4">
                        <FileText class="text-muted-foreground h-8 w-8" />
                        <div>
                            <p class="text-2xl font-bold">{{ user.seller_transactions_count ?? 0 }}</p>
                            <p class="text-muted-foreground text-sm">Transaksi (Penjual)</p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-center gap-3 p-4">
                        <Heart class="text-muted-foreground h-8 w-8" />
                        <div>
                            <p class="text-2xl font-bold">{{ user.favorites_count ?? 0 }}</p>
                            <p class="text-muted-foreground text-sm">Favorit</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Properties -->
            <Card v-if="user.properties && user.properties.length > 0">
                <CardHeader>
                    <CardTitle>Properti Terbaru</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-for="property in user.properties"
                        :key="property.id"
                        class="flex items-center justify-between rounded-lg border p-3"
                    >
                        <div>
                            <Link :href="`/admin/properti/${property.id}`" class="font-medium hover:underline">
                                {{ property.title }}
                            </Link>
                            <p class="text-muted-foreground text-sm">
                                {{ formatCurrency(property.price) }} &middot; {{ formatDate(property.created_at) }}
                            </p>
                        </div>
                        <Badge variant="outline">{{ statusLabel[property.status] ?? property.status }}</Badge>
                    </div>
                </CardContent>
            </Card>

            <!-- Buyer Transactions -->
            <Card v-if="user.buyer_transactions && user.buyer_transactions.length > 0">
                <CardHeader>
                    <CardTitle>Transaksi sebagai Pembeli</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-for="tx in user.buyer_transactions"
                        :key="tx.id"
                        class="flex items-center justify-between rounded-lg border p-3"
                    >
                        <div>
                            <Link :href="`/admin/transaksi/${tx.id}`" class="font-medium hover:underline">
                                {{ tx.transaction_number }}
                            </Link>
                            <p class="text-muted-foreground text-sm">
                                {{ tx.property?.title ?? '-' }} &middot; {{ formatDate(tx.created_at) }}
                            </p>
                        </div>
                        <Badge variant="outline">{{ tx.status_label ?? tx.status }}</Badge>
                    </div>
                </CardContent>
            </Card>

            <!-- Seller Transactions -->
            <Card v-if="user.seller_transactions && user.seller_transactions.length > 0">
                <CardHeader>
                    <CardTitle>Transaksi sebagai Penjual</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-for="tx in user.seller_transactions"
                        :key="tx.id"
                        class="flex items-center justify-between rounded-lg border p-3"
                    >
                        <div>
                            <Link :href="`/admin/transaksi/${tx.id}`" class="font-medium hover:underline">
                                {{ tx.transaction_number }}
                            </Link>
                            <p class="text-muted-foreground text-sm">
                                {{ tx.property?.title ?? '-' }} &middot; {{ formatDate(tx.created_at) }}
                            </p>
                        </div>
                        <Badge variant="outline">{{ tx.status_label ?? tx.status }}</Badge>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
