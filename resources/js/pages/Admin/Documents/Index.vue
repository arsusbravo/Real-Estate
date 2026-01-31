<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Document, type Paginated } from '@/types';
import {
    Search,
    FileText,
    CheckCircle,
    XCircle,
} from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ref, watch } from 'vue';

type Props = {
    documents: Paginated<Document>;
    filters: {
        status?: string;
        search?: string;
    };
    counts: {
        all: number;
        pending: number;
        verified: number;
    };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Dokumen', href: '/admin/dokumen' },
];

const search = ref(props.filters.search ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/dokumen', { search: val || undefined, status: props.filters.status }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

function filterByStatus(status?: string) {
    router.get('/admin/dokumen', { status, search: search.value || undefined }, {
        preserveState: true,
        replace: true,
    });
}

function verifyDocument(id: number) {
    router.put(`/admin/dokumen/${id}/verify`);
}

const rejectDocId = ref<number | null>(null);
const rejectReason = ref('');

function startReject(id: number) {
    const reason = prompt('Alasan penolakan dokumen:');
    if (reason && reason.trim()) {
        router.put(`/admin/dokumen/${id}/reject`, { rejection_reason: reason.trim() });
    }
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

function formatFileSize(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

const statusLabel: Record<string, string> = {
    uploaded: 'Diupload',
    under_review: 'Dalam Review',
    verified: 'Terverifikasi',
    rejected: 'Ditolak',
    expired: 'Kadaluarsa',
};

const statusVariant: Record<string, string> = {
    uploaded: 'outline',
    under_review: 'outline',
    verified: 'default',
    rejected: 'destructive',
    expired: 'secondary',
};
</script>

<template>
    <Head title="Kelola Dokumen" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold">Kelola Dokumen</h1>
                <p class="text-muted-foreground text-sm">{{ counts.all }} total dokumen</p>
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
                    :variant="filters.status === 'pending' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('pending')"
                >
                    Menunggu Verifikasi ({{ counts.pending }})
                </Button>
                <Button
                    :variant="filters.status === 'verified' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByStatus('verified')"
                >
                    Terverifikasi ({{ counts.verified }})
                </Button>
            </div>

            <!-- Search -->
            <div class="relative max-w-sm">
                <Search class="text-muted-foreground absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" />
                <Input
                    v-model="search"
                    type="search"
                    placeholder="Cari dokumen..."
                    class="pl-10"
                />
            </div>

            <!-- Empty State -->
            <div v-if="documents.data.length === 0" class="text-muted-foreground py-12 text-center">
                <FileText class="mx-auto mb-4 h-12 w-12 opacity-50" />
                <p>Tidak ada dokumen ditemukan</p>
            </div>

            <!-- Documents List -->
            <div v-else class="space-y-3">
                <Card v-for="doc in documents.data" :key="doc.id">
                    <CardContent class="flex items-center gap-4 p-4">
                        <!-- Icon -->
                        <div class="bg-muted flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg">
                            <FileText class="text-muted-foreground h-6 w-6" />
                        </div>

                        <!-- Info -->
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold">{{ doc.file_name }}</p>
                            <p class="text-muted-foreground text-sm">
                                {{ doc.document_type_label ?? doc.document_type }}
                                <span v-if="doc.category"> &middot; {{ doc.category }}</span>
                            </p>
                            <p class="text-muted-foreground text-xs">
                                Diupload oleh {{ doc.uploader?.name ?? '-' }}
                                &middot; {{ formatFileSize(doc.file_size) }}
                                &middot; {{ formatDate(doc.created_at) }}
                            </p>
                            <p v-if="doc.rejection_reason" class="mt-1 text-xs text-red-600">
                                Alasan penolakan: {{ doc.rejection_reason }}
                            </p>
                        </div>

                        <!-- Status & Actions -->
                        <div class="flex items-center gap-2">
                            <Badge :variant="(statusVariant[doc.status] as any) ?? 'secondary'">
                                {{ statusLabel[doc.status] ?? doc.status }}
                            </Badge>

                            <template v-if="doc.status === 'uploaded' || doc.status === 'under_review'">
                                <Button
                                    size="sm"
                                    class="bg-green-600 hover:bg-green-700"
                                    @click="verifyDocument(doc.id)"
                                >
                                    <CheckCircle class="mr-1 h-3 w-3" />
                                    Verifikasi
                                </Button>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    @click="startReject(doc.id)"
                                >
                                    <XCircle class="mr-1 h-3 w-3" />
                                    Tolak
                                </Button>
                            </template>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Pagination -->
            <div v-if="documents.last_page > 1" class="flex items-center justify-center gap-2 py-4">
                <template v-for="link in documents.links" :key="link.label">
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
