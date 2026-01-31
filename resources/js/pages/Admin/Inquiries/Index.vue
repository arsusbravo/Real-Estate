<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Inquiry, type Paginated } from '@/types';
import {
    Search,
    MessageSquare,
    MoreHorizontal,
    Eye,
    Phone,
    ArrowRightCircle,
} from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ref, watch } from 'vue';

type Props = {
    inquiries: Paginated<Inquiry>;
    filters: {
        status?: string;
        search?: string;
    };
    counts: {
        all: number;
        new: number;
        contacted: number;
    };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Inquiry', href: '/admin/inquiry' },
];

const search = ref(props.filters.search ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/inquiry', { search: val || undefined, status: props.filters.status }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

function filterByStatus(status?: string) {
    router.get('/admin/inquiry', { status, search: search.value || undefined }, {
        preserveState: true,
        replace: true,
    });
}

function markContacted(id: number) {
    router.put(`/admin/inquiry/${id}/status`, { status: 'contacted' });
}

function convertToTransaction(id: number) {
    router.post(`/admin/inquiry/${id}/convert`);
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

function truncate(text: string, length: number = 80): string {
    if (text.length <= length) return text;
    return text.substring(0, length) + '...';
}

const statusLabel: Record<string, string> = {
    new: 'Baru',
    contacted: 'Dihubungi',
    converted: 'Dikonversi',
    closed: 'Ditutup',
};

const statusVariant: Record<string, string> = {
    new: 'default',
    contacted: 'outline',
    converted: 'secondary',
    closed: 'secondary',
};
</script>

<template>
    <Head title="Kelola Inquiry" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold">Kelola Inquiry</h1>
                <p class="text-muted-foreground text-sm">{{ counts.all }} total inquiry</p>
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
                    :variant="filters.status === 'new' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('new')"
                >
                    Baru ({{ counts.new }})
                </Button>
                <Button
                    :variant="filters.status === 'contacted' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('contacted')"
                >
                    Dihubungi ({{ counts.contacted }})
                </Button>
            </div>

            <!-- Search -->
            <div class="relative max-w-sm">
                <Search class="text-muted-foreground absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" />
                <Input
                    v-model="search"
                    type="search"
                    placeholder="Cari inquiry..."
                    class="pl-10"
                />
            </div>

            <!-- Empty State -->
            <div v-if="inquiries.data.length === 0" class="text-muted-foreground py-12 text-center">
                <MessageSquare class="mx-auto mb-4 h-12 w-12 opacity-50" />
                <p>Tidak ada inquiry ditemukan</p>
            </div>

            <!-- Inquiry List -->
            <div v-else class="space-y-3">
                <Card v-for="inquiry in inquiries.data" :key="inquiry.id">
                    <CardContent class="flex items-start gap-4 p-4">
                        <!-- Info -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start gap-2">
                                <Link
                                    :href="`/admin/inquiry/${inquiry.id}`"
                                    class="font-semibold hover:underline"
                                >
                                    {{ inquiry.name }}
                                </Link>
                                <Badge :variant="(statusVariant[inquiry.status] as any) ?? 'secondary'">
                                    {{ statusLabel[inquiry.status] ?? inquiry.status }}
                                </Badge>
                            </div>
                            <p class="text-muted-foreground text-sm">
                                {{ inquiry.email }}
                                <span v-if="inquiry.phone"> &middot; {{ inquiry.phone }}</span>
                            </p>
                            <p class="mt-1 text-sm">{{ truncate(inquiry.message) }}</p>
                            <div class="mt-2 flex items-center gap-3 text-xs text-muted-foreground">
                                <span v-if="inquiry.property">
                                    Properti: {{ inquiry.property.title }}
                                </span>
                                <span>&middot;</span>
                                <span>{{ formatDate(inquiry.created_at) }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button variant="ghost" size="icon">
                                        <MoreHorizontal class="h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem as-child>
                                        <Link :href="`/admin/inquiry/${inquiry.id}`">
                                            <Eye class="mr-2 h-4 w-4" />
                                            Lihat Detail
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        v-if="inquiry.status === 'new'"
                                        @click="markContacted(inquiry.id)"
                                    >
                                        <Phone class="mr-2 h-4 w-4" />
                                        Tandai Dihubungi
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        v-if="inquiry.status !== 'converted' && inquiry.status !== 'closed'"
                                        @click="convertToTransaction(inquiry.id)"
                                    >
                                        <ArrowRightCircle class="mr-2 h-4 w-4" />
                                        Konversi ke Transaksi
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
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
