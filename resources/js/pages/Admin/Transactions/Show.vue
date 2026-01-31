<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Transaction, type NotaryPartner, type TransactionActivity } from '@/types';
import {
    ArrowLeft,
    User,
    Building2,
    Scale,
    Calendar,
    FileText,
    CheckCircle,
    XCircle,
    Clock,
    Download,
    DollarSign,
    Activity,
    MessageSquare,
    Shield,
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ref } from 'vue';

type Props = {
    transaction: Transaction;
    allowedStatuses: string[];
    notaries: NotaryPartner[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Transaksi', href: '/admin/transaksi' },
    { title: props.transaction.transaction_number },
];

const selectedStatus = ref(props.transaction.status);
const statusNotes = ref('');
const selectedNotary = ref(props.transaction.notary_id ?? '');

const rejectDocId = ref<number | null>(null);
const rejectDocReason = ref('');

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

function updateStatus() {
    router.put(`/admin/transaksi/${props.transaction.uuid}/status`, {
        status: selectedStatus.value,
        notes: statusNotes.value || undefined,
        notary_id: selectedNotary.value || undefined,
    }, {
        onSuccess: () => {
            statusNotes.value = '';
        },
    });
}

function verifyDocument(docId: number) {
    router.put(`/admin/dokumen/${docId}/verify`);
}

function rejectDocument(docId: number) {
    if (!rejectDocReason.value.trim()) return;
    router.put(`/admin/dokumen/${docId}/reject`, {
        rejection_reason: rejectDocReason.value,
    }, {
        onSuccess: () => {
            rejectDocId.value = null;
            rejectDocReason.value = '';
        },
    });
}
</script>

<template>
    <Head :title="`Transaksi ${transaction.transaction_number}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Button variant="outline" size="icon" as-child>
                        <Link href="/admin/transaksi">
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold">{{ transaction.transaction_number }}</h1>
                        <div class="flex items-center gap-2 mt-1">
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
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="space-y-4 lg:col-span-2">
                    <!-- Pihak Terkait -->
                    <div class="grid gap-4 md:grid-cols-3">
                        <!-- Pembeli -->
                        <Card>
                            <CardHeader class="pb-2">
                                <CardTitle class="text-sm">Pembeli</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="flex items-center gap-2">
                                    <div class="bg-muted flex h-8 w-8 items-center justify-center rounded-full">
                                        <User class="text-muted-foreground h-4 w-4" />
                                    </div>
                                    <div>
                                        <Link
                                            v-if="transaction.buyer"
                                            :href="`/admin/pengguna/${transaction.buyer.id}`"
                                            class="text-sm font-medium hover:underline"
                                        >
                                            {{ transaction.buyer.name }}
                                        </Link>
                                        <p v-else class="text-sm text-muted-foreground">-</p>
                                        <p class="text-muted-foreground text-xs">{{ transaction.buyer?.email }}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Penjual -->
                        <Card>
                            <CardHeader class="pb-2">
                                <CardTitle class="text-sm">Penjual</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="flex items-center gap-2">
                                    <div class="bg-muted flex h-8 w-8 items-center justify-center rounded-full">
                                        <User class="text-muted-foreground h-4 w-4" />
                                    </div>
                                    <div>
                                        <Link
                                            v-if="transaction.seller"
                                            :href="`/admin/pengguna/${transaction.seller.id}`"
                                            class="text-sm font-medium hover:underline"
                                        >
                                            {{ transaction.seller.name }}
                                        </Link>
                                        <p v-else class="text-sm text-muted-foreground">-</p>
                                        <p class="text-muted-foreground text-xs">{{ transaction.seller?.email }}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Notaris -->
                        <Card>
                            <CardHeader class="pb-2">
                                <CardTitle class="text-sm">Notaris</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div v-if="transaction.notary" class="flex items-center gap-2">
                                    <div class="bg-muted flex h-8 w-8 items-center justify-center rounded-full">
                                        <Scale class="text-muted-foreground h-4 w-4" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">{{ transaction.notary.name }}</p>
                                        <p class="text-muted-foreground text-xs">{{ transaction.notary.office_name }}</p>
                                    </div>
                                </div>
                                <p v-else class="text-muted-foreground text-sm">Belum ditugaskan</p>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Properti -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Properti</CardTitle>
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
                                    <Link
                                        :href="`/admin/properti/${transaction.property.id}`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ transaction.property.title }}
                                    </Link>
                                    <p class="text-muted-foreground text-sm">
                                        {{ transaction.property.city }}, {{ transaction.property.district }}
                                    </p>
                                    <p class="text-sm font-medium">{{ formatCurrency(transaction.property.price) }}</p>
                                </div>
                            </div>
                            <p v-else class="text-muted-foreground text-sm">Data properti tidak tersedia</p>
                        </CardContent>
                    </Card>

                    <!-- Info Keuangan -->
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

                    <!-- Tanggal Penting -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Calendar class="h-5 w-5" />
                                Tanggal Penting
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                                <div>
                                    <p class="text-muted-foreground text-xs">Inquiry</p>
                                    <p class="text-sm font-medium">{{ formatDate(transaction.inquiry_date) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Viewing</p>
                                    <p class="text-sm font-medium">{{ formatDate(transaction.viewing_date) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Negosiasi Dimulai</p>
                                    <p class="text-sm font-medium">{{ formatDate(transaction.negotiation_started_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Kesepakatan</p>
                                    <p class="text-sm font-medium">{{ formatDate(transaction.agreement_date) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">DP Dibayar</p>
                                    <p class="text-sm font-medium">{{ formatDate(transaction.dp_paid_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Notaris Ditugaskan</p>
                                    <p class="text-sm font-medium">{{ formatDate(transaction.notary_assigned_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">AJB Ditandatangani</p>
                                    <p class="text-sm font-medium">{{ formatDate(transaction.ajb_signed_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Sertifikat Dialihkan</p>
                                    <p class="text-sm font-medium">{{ formatDate(transaction.certificate_transferred_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Selesai</p>
                                    <p class="text-sm font-medium">{{ formatDate(transaction.completed_at) }}</p>
                                </div>
                                <div v-if="transaction.cancelled_at">
                                    <p class="text-muted-foreground text-xs">Dibatalkan</p>
                                    <p class="text-sm font-medium text-red-600">{{ formatDate(transaction.cancelled_at) }}</p>
                                </div>
                            </div>
                            <div v-if="transaction.cancellation_reason" class="mt-4 rounded-lg bg-red-50 p-3 dark:bg-red-950">
                                <p class="text-sm font-medium text-red-800 dark:text-red-200">Alasan Pembatalan:</p>
                                <p class="text-sm text-red-700 dark:text-red-300">{{ transaction.cancellation_reason }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Dokumen -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <FileText class="h-5 w-5" />
                                Dokumen ({{ transaction.documents?.length ?? 0 }})
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
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
                                            Diupload oleh {{ doc.uploader?.name ?? '-' }} &middot; {{ formatDate(doc.created_at) }}
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
                                                @click="rejectDocId = doc.id; rejectDocReason = ''"
                                            >
                                                <XCircle class="mr-1 h-3 w-3" />
                                                Tolak
                                            </Button>
                                        </template>
                                    </div>

                                    <!-- Reject reason for doc -->
                                    <div v-if="rejectDocId === doc.id" class="mt-2 w-full space-y-2">
                                        <textarea
                                            v-model="rejectDocReason"
                                            rows="2"
                                            class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                            placeholder="Alasan penolakan dokumen..."
                                        />
                                        <div class="flex gap-2">
                                            <Button variant="destructive" size="sm" :disabled="!rejectDocReason.trim()" @click="rejectDocument(doc.id)">
                                                Konfirmasi Tolak
                                            </Button>
                                            <Button variant="outline" size="sm" @click="rejectDocId = null">Batal</Button>
                                        </div>
                                    </div>

                                    <!-- Show rejection reason -->
                                    <p v-if="doc.rejection_reason" class="mt-1 text-xs text-red-600 dark:text-red-400">
                                        Alasan: {{ doc.rejection_reason }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Catatan -->
                    <Card v-if="transaction.notes">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <MessageSquare class="h-5 w-5" />
                                Catatan
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm whitespace-pre-line">{{ transaction.notes }}</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4">
                    <!-- Update Status -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Update Status</CardTitle>
                            <CardDescription>Pilih status baru untuk transaksi ini</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div>
                                <label class="text-sm font-medium">Status Baru</label>
                                <select
                                    v-model="selectedStatus"
                                    class="border-input bg-background ring-offset-background focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                >
                                    <option v-for="status in allowedStatuses" :key="status" :value="status">
                                        {{ statusLabels[status] ?? status }}
                                    </option>
                                </select>
                            </div>

                            <!-- Notary Selection (when status changes to notary_assigned) -->
                            <div v-if="selectedStatus === 'notary_assigned' && notaries.length > 0">
                                <label class="text-sm font-medium">Pilih Notaris</label>
                                <select
                                    v-model="selectedNotary"
                                    class="border-input bg-background ring-offset-background focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                >
                                    <option value="">-- Pilih Notaris --</option>
                                    <option v-for="notary in notaries" :key="notary.id" :value="notary.id">
                                        {{ notary.name }} - {{ notary.office_name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="text-sm font-medium">Catatan (Opsional)</label>
                                <textarea
                                    v-model="statusNotes"
                                    rows="3"
                                    class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                    placeholder="Tambahkan catatan..."
                                />
                            </div>

                            <Button @click="updateStatus" :disabled="selectedStatus === transaction.status" class="w-full">
                                Update Status
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Timeline / Activity Log -->
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
                                    class="relative pl-6 pb-6 last:pb-0"
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
