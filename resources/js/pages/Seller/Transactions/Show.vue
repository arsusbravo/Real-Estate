<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Transaction } from '@/types';
import {
    ArrowLeft,
    User,
    Building2,
    Calendar,
    FileText,
    DollarSign,
    Activity,
    Upload,
    Download,
    Mail,
    Phone,
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { ref } from 'vue';

type Props = {
    transaction: Transaction;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Seller', href: '/seller/dashboard' },
    { title: 'Transaksi', href: '/seller/transaksi' },
    { title: props.transaction.transaction_number, href: '#' },
];

function formatCurrency(value: number | null): string {
    if (!value) return '-';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
}

function formatDate(date: string | null): string {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function formatDateTime(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
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
    viewing_completed: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
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

const documentStatusLabels: Record<string, string> = {
    uploaded: 'Diupload',
    under_review: 'Dalam Review',
    verified: 'Terverifikasi',
    rejected: 'Ditolak',
    expired: 'Kedaluwarsa',
};

// Document upload form
const uploadForm = useForm({
    file: null as File | null,
    document_type: '',
    category: '',
});

const fileInput = ref<HTMLInputElement | null>(null);

function onFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        uploadForm.file = target.files[0];
    }
}

function submitDocument() {
    uploadForm.post(`/seller/transaksi/${props.transaction.id}/dokumen`, {
        forceFormData: true,
        onSuccess: () => {
            uploadForm.reset();
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        },
    });
}

const documentTypes = [
    { value: 'ktp', name: 'KTP' },
    { value: 'kk', name: 'Kartu Keluarga' },
    { value: 'npwp', name: 'NPWP' },
    { value: 'sertifikat', name: 'Sertifikat Tanah/Bangunan' },
    { value: 'imb', name: 'IMB / PBG' },
    { value: 'pbb', name: 'PBB' },
    { value: 'ajb', name: 'AJB' },
    { value: 'surat_kuasa', name: 'Surat Kuasa' },
    { value: 'lainnya', name: 'Lainnya' },
];
</script>

<template>
    <Head :title="`Transaksi ${transaction.transaction_number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center gap-3">
                <Button variant="outline" size="icon" as-child>
                    <Link href="/seller/transaksi">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">{{ transaction.transaction_number }}</h1>
                    <div class="mt-1 flex items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
                            :class="statusColors[transaction.status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'"
                        >
                            {{ statusLabels[transaction.status] ?? transaction.status }}
                        </span>
                        <span class="text-muted-foreground text-sm">Step {{ transaction.current_step }}</span>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="space-y-4 lg:col-span-2">
                    <!-- Property Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Building2 class="h-5 w-5" />
                                Properti
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="transaction.property" class="flex items-center gap-4">
                                <div class="bg-muted flex h-16 w-24 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg">
                                    <img
                                        v-if="transaction.property.media && transaction.property.media.length > 0"
                                        :src="transaction.property.media[0].original_url"
                                        :alt="transaction.property.title"
                                        class="h-full w-full object-cover"
                                    />
                                    <Building2 v-else class="text-muted-foreground h-6 w-6" />
                                </div>
                                <div>
                                    <p class="font-medium">{{ transaction.property.title }}</p>
                                    <p class="text-muted-foreground text-sm">
                                        {{ transaction.property.city }}, {{ transaction.property.district }}
                                    </p>
                                    <p class="text-sm font-medium">{{ formatCurrency(transaction.property.price) }}</p>
                                </div>
                            </div>
                            <p v-else class="text-muted-foreground text-sm">Data properti tidak tersedia</p>
                        </CardContent>
                    </Card>

                    <!-- Buyer Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <User class="h-5 w-5" />
                                Informasi Pembeli
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="transaction.buyer" class="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <p class="text-muted-foreground text-xs">Nama</p>
                                    <p class="text-sm font-medium">{{ transaction.buyer.name }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Email</p>
                                    <p class="text-sm font-medium">{{ transaction.buyer.email }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Telepon</p>
                                    <p class="text-sm font-medium">{{ transaction.buyer.phone ?? '-' }}</p>
                                </div>
                            </div>
                            <p v-else class="text-muted-foreground text-sm">Data pembeli tidak tersedia</p>
                        </CardContent>
                    </Card>

                    <!-- Financial Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <DollarSign class="h-5 w-5" />
                                Informasi Keuangan
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                <div>
                                    <p class="text-muted-foreground text-xs">Harga Kesepakatan</p>
                                    <p class="text-sm font-bold">{{ formatCurrency(transaction.agreed_price) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">DP</p>
                                    <p class="text-sm font-medium">{{ formatCurrency(transaction.dp_amount) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Komisi (%)</p>
                                    <p class="text-sm font-medium">{{ transaction.commission_percentage }}%</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Jumlah Komisi</p>
                                    <p class="text-sm font-medium">{{ formatCurrency(transaction.commission_amount) }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Documents -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <FileText class="h-5 w-5" />
                                Dokumen ({{ transaction.documents?.length ?? 0 }})
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Documents List -->
                            <div v-if="!transaction.documents || transaction.documents.length === 0" class="text-muted-foreground py-6 text-center text-sm">
                                Belum ada dokumen
                            </div>
                            <div v-else class="space-y-3">
                                <div
                                    v-for="doc in transaction.documents"
                                    :key="doc.id"
                                    class="flex items-center justify-between rounded-lg border p-3"
                                >
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium">{{ doc.document_type_label ?? doc.document_type }}</p>
                                        <p class="text-muted-foreground text-xs">{{ doc.file_name }}</p>
                                        <p class="text-muted-foreground text-xs">
                                            {{ doc.category }} &middot; {{ formatDate(doc.created_at) }}
                                        </p>
                                        <p v-if="doc.rejection_reason" class="mt-1 text-xs text-red-600 dark:text-red-400">
                                            Alasan ditolak: {{ doc.rejection_reason }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Badge
                                            :variant="doc.status === 'verified' ? 'default' : doc.status === 'rejected' ? 'destructive' : 'secondary'"
                                        >
                                            {{ documentStatusLabels[doc.status] ?? doc.status }}
                                        </Badge>
                                        <a :href="doc.file_path" target="_blank">
                                            <Button variant="ghost" size="icon">
                                                <Download class="h-4 w-4" />
                                            </Button>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <Separator />

                            <!-- Upload Form -->
                            <div>
                                <h4 class="mb-3 text-sm font-semibold">Upload Dokumen Baru</h4>
                                <form @submit.prevent="submitDocument" class="space-y-3">
                                    <div class="grid gap-3 md:grid-cols-3">
                                        <div class="space-y-2">
                                            <Label for="document_type">Tipe Dokumen</Label>
                                            <select
                                                id="document_type"
                                                v-model="uploadForm.document_type"
                                                class="border-input bg-background ring-offset-background focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                            >
                                                <option value="" disabled>Pilih tipe dokumen</option>
                                                <option v-for="opt in documentTypes" :key="opt.value" :value="opt.value">
                                                    {{ opt.name }}
                                                </option>
                                            </select>
                                            <p v-if="uploadForm.errors.document_type" class="text-sm text-red-600">{{ uploadForm.errors.document_type }}</p>
                                        </div>

                                        <div class="space-y-2">
                                            <Label for="category">Kategori</Label>
                                            <Input
                                                id="category"
                                                v-model="uploadForm.category"
                                                type="text"
                                                placeholder="Contoh: penjual, properti"
                                            />
                                            <p v-if="uploadForm.errors.category" class="text-sm text-red-600">{{ uploadForm.errors.category }}</p>
                                        </div>

                                        <div class="space-y-2">
                                            <Label for="file">File</Label>
                                            <input
                                                id="file"
                                                ref="fileInput"
                                                type="file"
                                                class="border-input bg-background ring-offset-background focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm file:mr-2 file:rounded file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                                @change="onFileChange"
                                            />
                                            <p v-if="uploadForm.errors.file" class="text-sm text-red-600">{{ uploadForm.errors.file }}</p>
                                        </div>
                                    </div>

                                    <Button
                                        type="submit"
                                        :disabled="uploadForm.processing || !uploadForm.file || !uploadForm.document_type"
                                    >
                                        <Upload class="mr-2 h-4 w-4" />
                                        {{ uploadForm.processing ? 'Mengupload...' : 'Upload Dokumen' }}
                                    </Button>
                                </form>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Notes -->
                    <Card v-if="transaction.notes">
                        <CardHeader>
                            <CardTitle>Catatan</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="whitespace-pre-line text-sm">{{ transaction.notes }}</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar: Timeline -->
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Activity class="h-5 w-5" />
                                Riwayat Aktivitas
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="!transaction.activities || transaction.activities.length === 0" class="text-muted-foreground py-6 text-center text-sm">
                                Belum ada aktivitas
                            </div>
                            <div v-else class="relative space-y-0">
                                <div
                                    v-for="(activity, index) in transaction.activities"
                                    :key="activity.id"
                                    class="relative pb-6 pl-6 last:pb-0"
                                >
                                    <!-- Timeline line -->
                                    <div
                                        v-if="index < (transaction.activities?.length ?? 0) - 1"
                                        class="bg-border absolute left-[9px] top-3 h-full w-px"
                                    />
                                    <!-- Dot -->
                                    <div class="bg-primary absolute left-0 top-1.5 h-[18px] w-[18px] rounded-full border-2 border-white dark:border-gray-900" />
                                    <div>
                                        <p class="text-sm font-medium">{{ activity.description }}</p>
                                        <p class="text-muted-foreground text-xs">
                                            {{ activity.user?.name ?? 'Sistem' }} &middot; {{ formatDateTime(activity.created_at) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
