<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Property, type Paginated } from '@/types';
import {
    Building2,
    Search,
    Plus,
    MoreHorizontal,
    Pencil,
    Send,
    Trash2,
    MessageSquare,
} from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ref, watch } from 'vue';

type Props = {
    properties: Paginated<Property>;
    filters: {
        status?: string;
        search?: string;
    };
    counts: {
        all: number;
        draft: number;
        active: number;
        pending: number;
    };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Seller', href: '/seller/dashboard' },
    { title: 'Properti', href: '/seller/properti' },
];

const search = ref(props.filters.search ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/seller/properti', { search: val || undefined, status: props.filters.status }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

function filterByStatus(status?: string) {
    router.get('/seller/properti', { status, search: search.value || undefined }, {
        preserveState: true,
        replace: true,
    });
}

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

const statusLabel: Record<string, string> = {
    draft: 'Draf',
    pending_review: 'Menunggu Review',
    active: 'Aktif',
    sold: 'Terjual',
    rented: 'Tersewa',
    inactive: 'Nonaktif',
    rejected: 'Ditolak',
};

const statusColor: Record<string, string> = {
    draft: 'secondary',
    pending_review: 'outline',
    active: 'default',
    sold: 'destructive',
    rented: 'destructive',
    inactive: 'secondary',
    rejected: 'destructive',
};

const propertyTypeLabel: Record<string, string> = {
    rumah: 'Rumah',
    apartemen: 'Apartemen',
    tanah: 'Tanah',
    ruko: 'Ruko',
    gudang: 'Gudang',
    kantor: 'Kantor',
};

function submitForReview(id: number) {
    router.post(`/seller/properti/${id}/submit`, {}, {
        preserveState: true,
    });
}

function deleteProperty(id: number) {
    if (confirm('Apakah Anda yakin ingin menghapus properti ini?')) {
        router.delete(`/seller/properti/${id}`, {
            preserveState: true,
        });
    }
}
</script>

<template>
    <Head title="Properti Saya" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Properti Saya</h1>
                    <p class="text-muted-foreground text-sm">{{ counts.all }} total properti</p>
                </div>
                <Button as-child>
                    <Link href="/seller/properti/buat">
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Properti
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
                    Semua ({{ counts.all }})
                </Button>
                <Button
                    :variant="filters.status === 'draft' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('draft')"
                >
                    Draf ({{ counts.draft }})
                </Button>
                <Button
                    :variant="filters.status === 'active' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('active')"
                >
                    Aktif ({{ counts.active }})
                </Button>
                <Button
                    :variant="filters.status === 'pending_review' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('pending_review')"
                >
                    Pending ({{ counts.pending }})
                </Button>
            </div>

            <!-- Search -->
            <div class="relative max-w-sm">
                <Search class="text-muted-foreground absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" />
                <Input
                    v-model="search"
                    type="search"
                    placeholder="Cari properti..."
                    class="pl-10"
                />
            </div>

            <!-- Properties List -->
            <div v-if="properties.data.length === 0" class="text-muted-foreground py-12 text-center">
                <Building2 class="mx-auto mb-4 h-12 w-12 opacity-50" />
                <p>Tidak ada properti ditemukan</p>
            </div>

            <div v-else class="space-y-3">
                <Card v-for="property in properties.data" :key="property.id">
                    <CardContent class="flex items-center gap-4 p-4">
                        <!-- Thumbnail -->
                        <div class="bg-muted flex h-20 w-28 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg">
                            <img
                                v-if="property.media && property.media.length > 0"
                                :src="property.media[0].original_url"
                                :alt="property.title"
                                class="h-full w-full object-cover"
                            />
                            <Building2 v-else class="text-muted-foreground h-8 w-8" />
                        </div>

                        <!-- Info -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start gap-2">
                                <p class="truncate font-semibold">{{ property.title }}</p>
                            </div>
                            <p class="text-muted-foreground text-sm">
                                {{ propertyTypeLabel[property.property_type] ?? property.property_type }}
                                &middot; {{ property.city }}, {{ property.district }}
                            </p>
                            <p class="text-sm font-medium">{{ formatCurrency(property.price) }}</p>
                            <div class="text-muted-foreground mt-1 flex items-center gap-3 text-xs">
                                <span class="flex items-center gap-1">
                                    <MessageSquare class="h-3 w-3" />
                                    {{ property.inquiries_count ?? 0 }} inquiry
                                </span>
                                <span>{{ formatDate(property.created_at) }}</span>
                            </div>
                        </div>

                        <!-- Status & Actions -->
                        <div class="flex items-center gap-2">
                            <Badge :variant="(statusColor[property.status] as any) ?? 'secondary'">
                                {{ statusLabel[property.status] ?? property.status }}
                            </Badge>

                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="ghost" size="icon">
                                        <MoreHorizontal class="h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem as-child>
                                        <Link :href="`/seller/properti/${property.id}/edit`">
                                            <Pencil class="mr-2 h-4 w-4" />
                                            Edit
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        v-if="property.status === 'draft' || property.status === 'rejected'"
                                        @click="submitForReview(property.id)"
                                    >
                                        <Send class="mr-2 h-4 w-4" />
                                        Submit Review
                                    </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem
                                        class="text-red-600 focus:text-red-600"
                                        @click="deleteProperty(property.id)"
                                    >
                                        <Trash2 class="mr-2 h-4 w-4" />
                                        Hapus
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Pagination -->
            <div v-if="properties.last_page > 1" class="flex items-center justify-center gap-2 py-4">
                <template v-for="link in properties.links" :key="link.label">
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
