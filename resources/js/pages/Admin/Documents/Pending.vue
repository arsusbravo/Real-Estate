<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Document, type Paginated } from '@/types';
import {
    FileText,
    CheckCircle,
    XCircle,
} from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Props = {
    documents: Paginated<Document>;
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Dokumen', href: '/admin/dokumen/pending' },
    { title: 'Menunggu Verifikasi', href: '/admin/dokumen/pending' },
];

function verifyDocument(id: number) {
    router.put(`/admin/dokumen/${id}/verify`);
}

function rejectDocument(id: number) {
    const reason = prompt('Alasan penolakan dokumen:');
    if (reason && reason.trim()) {
        router.put(`/admin/dokumen/${id}/reject`, { reason: reason.trim() });
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
</script>

<template>
    <Head title="Dokumen Menunggu Verifikasi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div>
                <h1 class="text-2xl font-bold">Dokumen Menunggu Verifikasi</h1>
                <p class="text-muted-foreground text-sm">{{ documents.total }} dokumen perlu diverifikasi</p>
            </div>

            <!-- Empty State -->
            <div v-if="documents.data.length === 0" class="text-muted-foreground py-12 text-center">
                <FileText class="mx-auto mb-4 h-12 w-12 opacity-50" />
                <p>Tidak ada dokumen yang menunggu verifikasi</p>
            </div>

            <!-- Documents List -->
            <div v-else class="space-y-3">
                <Card v-for="doc in documents.data" :key="doc.id">
                    <CardContent class="flex items-center gap-4 p-4">
                        <div class="bg-muted flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg">
                            <FileText class="text-muted-foreground h-6 w-6" />
                        </div>

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
                        </div>

                        <div class="flex items-center gap-2">
                            <Badge variant="outline">Menunggu</Badge>
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
                                @click="rejectDocument(doc.id)"
                            >
                                <XCircle class="mr-1 h-3 w-3" />
                                Tolak
                            </Button>
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
