<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Inquiry, type Paginated } from '@/types';
import {
    MessageSquare,
    Mail,
    Phone,
    Calendar,
    Eye,
} from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Props = {
    inquiries: Paginated<Inquiry>;
    filters: {
        status?: string;
    };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Seller', href: '/seller/dashboard' },
    { title: 'Inquiry', href: '/seller/inquiry' },
];

function filterByStatus(status?: string) {
    router.get('/seller/inquiry', { status }, {
        preserveState: true,
        replace: true,
    });
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

const statusLabel: Record<string, string> = {
    new: 'Baru',
    contacted: 'Dihubungi',
    converted: 'Konversi',
    closed: 'Ditutup',
};

const statusColor: Record<string, string> = {
    new: 'default',
    contacted: 'secondary',
    converted: 'outline',
    closed: 'secondary',
};

function truncate(text: string, max: number): string {
    return text.length > max ? text.substring(0, max) + '...' : text;
}
</script>

<template>
    <Head title="Inquiry" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold">Inquiry</h1>
                <p class="text-muted-foreground text-sm">Daftar pertanyaan dari calon pembeli</p>
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
                    :variant="filters.status === 'new' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('new')"
                >
                    Baru
                </Button>
                <Button
                    :variant="filters.status === 'contacted' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('contacted')"
                >
                    Dihubungi
                </Button>
                <Button
                    :variant="filters.status === 'converted' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('converted')"
                >
                    Konversi
                </Button>
                <Button
                    :variant="filters.status === 'closed' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('closed')"
                >
                    Ditutup
                </Button>
            </div>

            <!-- Inquiry List -->
            <div v-if="inquiries.data.length === 0" class="text-muted-foreground py-12 text-center">
                <MessageSquare class="mx-auto mb-4 h-12 w-12 opacity-50" />
                <p>Tidak ada inquiry ditemukan</p>
            </div>

            <div v-else class="space-y-3">
                <Card v-for="inquiry in inquiries.data" :key="inquiry.id">
                    <CardContent class="p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <Link
                                        :href="`/seller/inquiry/${inquiry.id}`"
                                        class="font-semibold hover:underline"
                                    >
                                        {{ inquiry.name }}
                                    </Link>
                                    <Badge :variant="(statusColor[inquiry.status] as any) ?? 'secondary'">
                                        {{ statusLabel[inquiry.status] ?? inquiry.status }}
                                    </Badge>
                                </div>

                                <p class="text-muted-foreground mt-1 text-sm">
                                    Properti: <span class="font-medium">{{ inquiry.property?.title ?? '-' }}</span>
                                </p>

                                <p class="text-muted-foreground mt-1 text-sm">
                                    {{ truncate(inquiry.message, 120) }}
                                </p>

                                <div class="text-muted-foreground mt-2 flex flex-wrap items-center gap-3 text-xs">
                                    <span class="flex items-center gap-1">
                                        <Mail class="h-3 w-3" />
                                        {{ inquiry.email }}
                                    </span>
                                    <span v-if="inquiry.phone" class="flex items-center gap-1">
                                        <Phone class="h-3 w-3" />
                                        {{ inquiry.phone }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <Calendar class="h-3 w-3" />
                                        {{ formatDate(inquiry.created_at) }}
                                    </span>
                                </div>
                            </div>

                            <Button variant="outline" size="sm" as-child>
                                <Link :href="`/seller/inquiry/${inquiry.id}`">
                                    <Eye class="mr-1 h-4 w-4" />
                                    Detail
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Pagination -->
            <div v-if="inquiries.last_page > 1" class="flex items-center justify-center gap-2 py-4">
                <template v-for="link in inquiries.links" :key="link.label">
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
