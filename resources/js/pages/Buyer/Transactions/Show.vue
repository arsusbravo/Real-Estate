<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Transaction } from '@/types';
import {
    ArrowLeft,
    Building2,
    MapPin,
    Calendar,
    Upload,
    FileText,
    CheckCircle,
    Circle,
    Clock,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';

type Props = {
    transaction: Transaction;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Buyer', href: '/buyer/dashboard' },
    { title: 'Transaksi', href: '/buyer/transaksi' },
    { title: props.transaction.transaction_number, href: `/buyer/transaksi/${props.transaction.uuid}` },
];

function formatCurrency(value: number): string {
    if (value >= 1_000_000_000) return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    if (value >= 1_000_000) return `Rp ${(value / 1_000_000).toFixed(0)} Jt`;
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

function formatDate(date: string | null): string {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
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

const progressSteps = [
    { key: 'inquiry', label: 'Inquiry', dateField: 'inquiry_date' },
    { key: 'viewing', label: 'Viewing', dateField: 'viewing_date' },
    { key: 'negotiation', label: 'Negosiasi', dateField: 'negotiation_started_at' },
    { key: 'agreement', label: 'Kesepakatan', dateField: 'agreement_date' },
    { key: 'dp', label: 'Pembayaran DP', dateField: 'dp_paid_at' },
    { key: 'notary', label: 'Notaris', dateField: 'notary_assigned_at' },
    { key: 'ajb', label: 'AJB', dateField: 'ajb_signed_at' },
    { key: 'certificate', label: 'Sertifikat', dateField: 'certificate_transferred_at' },
    { key: 'completed', label: 'Selesai', dateField: 'completed_at' },
];

function getStepStatus(dateField: string): 'completed' | 'current' | 'pending' {
    const value = (props.transaction as any)[dateField];
    if (value) return 'completed';
    const currentStep = props.transaction.current_step;
    const stepIndex = progressSteps.findIndex(s => s.dateField === dateField);
    if (stepIndex === currentStep) return 'current';
    return 'pending';
}

const documentInput = ref<HTMLInputElement | null>(null);

function triggerUpload() {
    documentInput.value?.click();
}

function handleFileUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    if (!target.files || target.files.length === 0) return;

    const formData = new FormData();
    for (const file of target.files) {
        formData.append('documents[]', file);
    }

    router.post(`/buyer/transaksi/${props.transaction.uuid}/dokumen`, formData, {
        preserveScroll: true,
        forceFormData: true,
    });
}

const documentStatusLabels: Record<string, string> = {
    uploaded: 'Diupload',
    under_review: 'Dalam Review',
    verified: 'Terverifikasi',
    rejected: 'Ditolak',
    expired: 'Kedaluwarsa',
};

const documentStatusColors: Record<string, string> = {
    uploaded: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    under_review: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    verified: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    expired: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
};
</script>

<template>
    <Head :title="`Transaksi ${transaction.transaction_number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link href="/buyer/transaksi">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">{{ transaction.transaction_number }}</h1>
                    <p class="text-muted-foreground text-sm">
                        {{ statusLabels[transaction.status] ?? transaction.status }}
                    </p>
                </div>
            </div>

            <!-- Property Info Card -->
            <Card v-if="transaction.property">
                <CardHeader>
                    <CardTitle>Informasi Properti</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex items-start gap-4">
                        <div class="bg-muted flex h-24 w-32 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg">
                            <img
                                v-if="transaction.property.media && transaction.property.media.length > 0"
                                :src="transaction.property.media[0].original_url"
                                :alt="transaction.property.title"
                                class="h-full w-full object-cover"
                            />
                            <Building2 v-else class="text-muted-foreground h-8 w-8" />
                        </div>
                        <div>
                            <h3 class="font-semibold">{{ transaction.property.title }}</h3>
                            <p class="text-primary text-lg font-bold">
                                {{ formatCurrency(transaction.agreed_price ?? transaction.property.price) }}
                            </p>
                            <p class="text-muted-foreground mt-1 flex items-center gap-1 text-sm">
                                <MapPin class="h-3.5 w-3.5" />
                                {{ transaction.property.city }}, {{ transaction.property.district }}
                            </p>
                            <p class="text-muted-foreground text-sm">
                                Penjual: {{ transaction.seller?.name ?? '-' }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Progress Timeline -->
            <Card>
                <CardHeader>
                    <CardTitle>Status Proses</CardTitle>
                    <CardDescription>Progress transaksi Anda</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="relative">
                        <div class="flex items-start justify-between">
                            <div
                                v-for="(step, index) in progressSteps"
                                :key="step.key"
                                class="flex flex-1 flex-col items-center text-center"
                            >
                                <div class="relative flex items-center justify-center">
                                    <!-- Connector line -->
                                    <div
                                        v-if="index > 0"
                                        class="absolute right-1/2 top-1/2 h-0.5 -translate-y-1/2"
                                        :class="getStepStatus(step.dateField) === 'completed' ? 'bg-primary' : 'bg-muted'"
                                        :style="{ width: 'calc(100% + 2rem)' }"
                                    />
                                    <!-- Step icon -->
                                    <div class="relative z-10">
                                        <CheckCircle
                                            v-if="getStepStatus(step.dateField) === 'completed'"
                                            class="text-primary h-6 w-6"
                                        />
                                        <Clock
                                            v-else-if="getStepStatus(step.dateField) === 'current'"
                                            class="h-6 w-6 text-yellow-500"
                                        />
                                        <Circle
                                            v-else
                                            class="text-muted-foreground h-6 w-6"
                                        />
                                    </div>
                                </div>
                                <p class="mt-2 text-xs font-medium">{{ step.label }}</p>
                                <p class="text-muted-foreground text-xs">
                                    {{ formatDate((transaction as any)[step.dateField]) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Key Dates -->
            <Card>
                <CardHeader>
                    <CardTitle>Tanggal Penting</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="flex items-center gap-3 rounded-lg border p-3">
                            <Calendar class="text-muted-foreground h-5 w-5" />
                            <div>
                                <p class="text-xs font-medium text-muted-foreground">Tanggal Inquiry</p>
                                <p class="text-sm">{{ formatDate(transaction.inquiry_date) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg border p-3">
                            <Calendar class="text-muted-foreground h-5 w-5" />
                            <div>
                                <p class="text-xs font-medium text-muted-foreground">Tanggal Viewing</p>
                                <p class="text-sm">{{ formatDate(transaction.viewing_date) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg border p-3">
                            <Calendar class="text-muted-foreground h-5 w-5" />
                            <div>
                                <p class="text-xs font-medium text-muted-foreground">Tanggal Kesepakatan</p>
                                <p class="text-sm">{{ formatDate(transaction.agreement_date) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg border p-3">
                            <Calendar class="text-muted-foreground h-5 w-5" />
                            <div>
                                <p class="text-xs font-medium text-muted-foreground">DP Dibayar</p>
                                <p class="text-sm">{{ formatDate(transaction.dp_paid_at) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg border p-3">
                            <Calendar class="text-muted-foreground h-5 w-5" />
                            <div>
                                <p class="text-xs font-medium text-muted-foreground">AJB Ditandatangani</p>
                                <p class="text-sm">{{ formatDate(transaction.ajb_signed_at) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg border p-3">
                            <Calendar class="text-muted-foreground h-5 w-5" />
                            <div>
                                <p class="text-xs font-medium text-muted-foreground">Selesai</p>
                                <p class="text-sm">{{ formatDate(transaction.completed_at) }}</p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Documents Section -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Dokumen</CardTitle>
                            <CardDescription>Dokumen terkait transaksi ini</CardDescription>
                        </div>
                        <div>
                            <input
                                ref="documentInput"
                                type="file"
                                multiple
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                class="hidden"
                                @change="handleFileUpload"
                            />
                            <Button variant="outline" size="sm" @click="triggerUpload">
                                <Upload class="mr-2 h-4 w-4" />
                                Upload Dokumen
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="!transaction.documents || transaction.documents.length === 0" class="text-muted-foreground py-8 text-center text-sm">
                        <FileText class="mx-auto mb-2 h-8 w-8 opacity-50" />
                        <p>Belum ada dokumen</p>
                    </div>
                    <div v-else class="space-y-3">
                        <div
                            v-for="doc in transaction.documents"
                            :key="doc.id"
                            class="flex items-center justify-between rounded-lg border p-3"
                        >
                            <div class="flex items-center gap-3">
                                <FileText class="text-muted-foreground h-5 w-5" />
                                <div>
                                    <p class="text-sm font-medium">{{ doc.document_type_label ?? doc.document_type }}</p>
                                    <p class="text-muted-foreground text-xs">{{ doc.file_name }} &middot; {{ formatDate(doc.created_at) }}</p>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                :class="documentStatusColors[doc.status] ?? 'bg-gray-100 text-gray-800'"
                            >
                                {{ documentStatusLabels[doc.status] ?? doc.status }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Notes -->
            <Card v-if="transaction.notes">
                <CardHeader>
                    <CardTitle>Catatan</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm">{{ transaction.notes }}</p>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
