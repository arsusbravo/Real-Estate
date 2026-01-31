<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Inquiry } from '@/types';
import {
    ArrowLeft,
    Building2,
    Mail,
    Phone,
    MessageSquare,
    ArrowRightCircle,
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';

type Props = {
    inquiry: Inquiry;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Inquiry', href: '/admin/inquiry' },
    { title: 'Detail' },
];

const form = useForm({
    status: props.inquiry.status,
    admin_notes: props.inquiry.admin_notes ?? '',
});

function submitNotes() {
    form.put(`/admin/inquiry/${props.inquiry.id}/status`);
}

function markContacted() {
    router.put(`/admin/inquiry/${props.inquiry.id}/status`, { status: 'contacted' });
}

function convertToTransaction() {
    router.post(`/admin/inquiry/${props.inquiry.id}/convert`);
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
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

const contactMethodLabel: Record<string, string> = {
    phone: 'Telepon',
    whatsapp: 'WhatsApp',
    email: 'Email',
};
</script>

<template>
    <Head title="Detail Inquiry" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Button variant="outline" size="icon" as-child>
                        <Link href="/admin/inquiry">
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold">Detail Inquiry</h1>
                        <div class="mt-1 flex items-center gap-2">
                            <Badge :variant="(statusVariant[inquiry.status] as any) ?? 'secondary'">
                                {{ statusLabel[inquiry.status] ?? inquiry.status }}
                            </Badge>
                            <span class="text-muted-foreground text-sm">{{ formatDate(inquiry.created_at) }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <Button
                        v-if="inquiry.status === 'new'"
                        variant="outline"
                        @click="markContacted"
                    >
                        <Phone class="mr-2 h-4 w-4" />
                        Tandai Dihubungi
                    </Button>
                    <Button
                        v-if="inquiry.status !== 'converted' && inquiry.status !== 'closed'"
                        @click="convertToTransaction"
                    >
                        <ArrowRightCircle class="mr-2 h-4 w-4" />
                        Konversi ke Transaksi
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Main Content -->
                <div class="space-y-4 lg:col-span-2">
                    <!-- Inquiry Details -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Pengirim</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <p class="text-muted-foreground text-xs">Nama</p>
                                    <p class="text-sm font-medium">{{ inquiry.name }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Email</p>
                                    <div class="flex items-center gap-1">
                                        <Mail class="text-muted-foreground h-3 w-3" />
                                        <a :href="`mailto:${inquiry.email}`" class="text-sm font-medium hover:underline">
                                            {{ inquiry.email }}
                                        </a>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Telepon</p>
                                    <div class="flex items-center gap-1">
                                        <Phone class="text-muted-foreground h-3 w-3" />
                                        <p class="text-sm font-medium">{{ inquiry.phone ?? '-' }}</p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">WhatsApp</p>
                                    <p class="text-sm font-medium">{{ inquiry.whatsapp ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Metode Kontak Preferensi</p>
                                    <p class="text-sm font-medium">
                                        {{ contactMethodLabel[inquiry.preferred_contact_method] ?? inquiry.preferred_contact_method }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Status</p>
                                    <Badge :variant="(statusVariant[inquiry.status] as any) ?? 'secondary'">
                                        {{ statusLabel[inquiry.status] ?? inquiry.status }}
                                    </Badge>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Message -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <MessageSquare class="h-5 w-5" />
                                Pesan
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="whitespace-pre-line text-sm">{{ inquiry.message }}</p>
                        </CardContent>
                    </Card>

                    <!-- Property Info -->
                    <Card v-if="inquiry.property">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Building2 class="h-5 w-5" />
                                Properti Terkait
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex items-center gap-4">
                                <div class="bg-muted flex h-16 w-24 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg">
                                    <img
                                        v-if="inquiry.property.media && inquiry.property.media.length > 0"
                                        :src="inquiry.property.media[0].original_url"
                                        :alt="inquiry.property.title"
                                        class="h-full w-full object-cover"
                                    />
                                    <Building2 v-else class="text-muted-foreground h-6 w-6" />
                                </div>
                                <div>
                                    <Link
                                        :href="`/admin/properti/${inquiry.property.id}`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ inquiry.property.title }}
                                    </Link>
                                    <p class="text-muted-foreground text-sm">
                                        {{ inquiry.property.city }}, {{ inquiry.property.district }}
                                    </p>
                                    <p class="text-sm font-medium">
                                        {{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(inquiry.property.price) }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4">
                    <!-- Admin Notes -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Catatan Admin</CardTitle>
                            <CardDescription>Tambahkan catatan internal tentang inquiry ini</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div>
                                <Label>Status</Label>
                                <select
                                    v-model="form.status"
                                    class="border-input bg-background ring-offset-background focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                >
                                    <option value="new">Baru</option>
                                    <option value="contacted">Dihubungi</option>
                                    <option value="converted">Dikonversi</option>
                                    <option value="closed">Ditutup</option>
                                </select>
                                <p v-if="form.errors.status" class="mt-1 text-xs text-red-600">{{ form.errors.status }}</p>
                            </div>
                            <div>
                                <Label>Catatan</Label>
                                <textarea
                                    v-model="form.admin_notes"
                                    rows="4"
                                    class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                    placeholder="Tulis catatan admin..."
                                />
                                <p v-if="form.errors.admin_notes" class="mt-1 text-xs text-red-600">{{ form.errors.admin_notes }}</p>
                            </div>
                        </CardContent>
                        <CardFooter>
                            <Button @click="submitNotes" :disabled="form.processing" class="w-full">
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Catatan' }}
                            </Button>
                        </CardFooter>
                    </Card>

                    <!-- User Info -->
                    <Card v-if="inquiry.user">
                        <CardHeader>
                            <CardTitle class="text-sm">Pengguna Terdaftar</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm font-medium">{{ inquiry.user.name }}</p>
                            <p class="text-muted-foreground text-xs">{{ inquiry.user.email }}</p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
