<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Inquiry } from '@/types';
import {
    ArrowLeft,
    User,
    Mail,
    Phone,
    MessageCircle,
    Building2,
    Calendar,
    ExternalLink,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';

type Props = {
    inquiry: Inquiry;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Seller', href: '/seller/dashboard' },
    { title: 'Inquiry', href: '/seller/inquiry' },
    { title: props.inquiry.name, href: '#' },
];

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatCurrency(value: number): string {
    if (value >= 1_000_000_000) return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    if (value >= 1_000_000) return `Rp ${(value / 1_000_000).toFixed(0)} Jt`;
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
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

const contactMethodLabel: Record<string, string> = {
    phone: 'Telepon',
    whatsapp: 'WhatsApp',
    email: 'Email',
};

function getWhatsAppLink(number: string): string {
    const cleaned = number.replace(/\D/g, '');
    return `https://wa.me/${cleaned}`;
}

function getEmailLink(email: string, propertyTitle?: string): string {
    const subject = encodeURIComponent(`Re: Inquiry - ${propertyTitle ?? 'Properti'}`);
    return `mailto:${email}?subject=${subject}`;
}
</script>

<template>
    <Head :title="`Inquiry - ${inquiry.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center gap-3">
                <Button variant="outline" size="icon" as-child>
                    <Link href="/seller/inquiry">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-bold">{{ inquiry.name }}</h1>
                        <Badge :variant="(statusColor[inquiry.status] as any) ?? 'secondary'">
                            {{ statusLabel[inquiry.status] ?? inquiry.status }}
                        </Badge>
                    </div>
                    <p class="text-muted-foreground text-sm">
                        Diterima pada {{ formatDate(inquiry.created_at) }}
                    </p>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Inquiry Detail -->
                <div class="space-y-4 lg:col-span-2">
                    <!-- Contact Info -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <User class="h-5 w-5" />
                                Informasi Kontak
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-muted-foreground text-xs">Nama</p>
                                    <p class="text-sm font-medium">{{ inquiry.name }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Email</p>
                                    <p class="text-sm font-medium">{{ inquiry.email }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Telepon</p>
                                    <p class="text-sm font-medium">{{ inquiry.phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">WhatsApp</p>
                                    <p class="text-sm font-medium">{{ inquiry.whatsapp ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Metode Kontak Pilihan</p>
                                    <p class="text-sm font-medium">{{ contactMethodLabel[inquiry.preferred_contact_method] ?? inquiry.preferred_contact_method }}</p>
                                </div>
                                <div>
                                    <p class="text-muted-foreground text-xs">Status</p>
                                    <Badge :variant="(statusColor[inquiry.status] as any) ?? 'secondary'" class="mt-1">
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
                                <MessageCircle class="h-5 w-5" />
                                Pesan
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="whitespace-pre-line text-sm">{{ inquiry.message }}</p>
                        </CardContent>
                    </Card>

                    <!-- Contact Actions -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Hubungi Pengirim</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex flex-wrap gap-3">
                                <Button
                                    v-if="inquiry.whatsapp"
                                    as-child
                                    class="bg-green-600 hover:bg-green-700"
                                >
                                    <a :href="getWhatsAppLink(inquiry.whatsapp)" target="_blank" rel="noopener noreferrer">
                                        <MessageCircle class="mr-2 h-4 w-4" />
                                        WhatsApp
                                        <ExternalLink class="ml-1 h-3 w-3" />
                                    </a>
                                </Button>
                                <Button variant="outline" as-child>
                                    <a :href="getEmailLink(inquiry.email, inquiry.property?.title)" target="_blank" rel="noopener noreferrer">
                                        <Mail class="mr-2 h-4 w-4" />
                                        Kirim Email
                                        <ExternalLink class="ml-1 h-3 w-3" />
                                    </a>
                                </Button>
                                <Button
                                    v-if="inquiry.phone"
                                    variant="outline"
                                    as-child
                                >
                                    <a :href="`tel:${inquiry.phone}`">
                                        <Phone class="mr-2 h-4 w-4" />
                                        Telepon
                                    </a>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar: Property Card -->
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Building2 class="h-5 w-5" />
                                Properti Terkait
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="inquiry.property">
                                <div class="bg-muted mb-3 flex h-36 items-center justify-center overflow-hidden rounded-lg">
                                    <img
                                        v-if="inquiry.property.media && inquiry.property.media.length > 0"
                                        :src="inquiry.property.media[0].original_url"
                                        :alt="inquiry.property.title"
                                        class="h-full w-full object-cover"
                                    />
                                    <Building2 v-else class="text-muted-foreground h-8 w-8" />
                                </div>
                                <h3 class="font-semibold">{{ inquiry.property.title }}</h3>
                                <p class="text-sm font-medium text-primary">{{ formatCurrency(inquiry.property.price) }}</p>
                                <p class="text-muted-foreground mt-1 text-xs">
                                    {{ inquiry.property.city }}, {{ inquiry.property.district }}
                                </p>
                                <Separator class="my-3" />
                                <Button variant="outline" size="sm" class="w-full" as-child>
                                    <Link :href="`/seller/properti/${inquiry.property.id}/edit`">
                                        Lihat Properti
                                    </Link>
                                </Button>
                            </div>
                            <p v-else class="text-muted-foreground text-sm">Data properti tidak tersedia</p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
