<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Property, type Paginated } from '@/types';
import {
    Building2,
    Clock,
    CheckCircle,
    XCircle,
    Eye,
    MapPin,
    Ruler,
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';

type Props = {
    properties: Paginated<Property>;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Properti', href: '/admin/properti' },
    { title: 'Menunggu Persetujuan' },
];

const rejectingId = ref<number | null>(null);
const rejectReason = ref('');

function formatCurrency(value: number): string {
    if (value >= 1_000_000_000) {
        return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    }
    if (value >= 1_000_000) {
        return `Rp ${(value / 1_000_000).toFixed(0)} Jt`;
    }
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

const propertyTypeLabel: Record<string, string> = {
    rumah: 'Rumah',
    apartemen: 'Apartemen',
    tanah: 'Tanah',
    ruko: 'Ruko',
    gudang: 'Gudang',
    kantor: 'Kantor',
};

function approveProperty(id: number) {
    router.put(`/admin/properti/${id}/approve`);
}

function rejectProperty(id: number) {
    if (!rejectReason.value.trim()) return;
    router.put(`/admin/properti/${id}/reject`, {
        rejection_reason: rejectReason.value,
    }, {
        onSuccess: () => {
            rejectingId.value = null;
            rejectReason.value = '';
        },
    });
}

function startReject(id: number) {
    rejectingId.value = id;
    rejectReason.value = '';
}

function cancelReject() {
    rejectingId.value = null;
    rejectReason.value = '';
}
</script>

<template>
    <Head title="Properti Menunggu Persetujuan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold">Properti Menunggu Persetujuan</h1>
                <p class="text-muted-foreground text-sm">{{ properties.total }} properti menunggu review</p>
            </div>

            <!-- Empty State -->
            <div v-if="properties.data.length === 0" class="text-muted-foreground py-16 text-center">
                <CheckCircle class="mx-auto mb-4 h-12 w-12 text-green-500 opacity-50" />
                <p class="text-lg font-medium">Tidak ada properti yang menunggu persetujuan</p>
                <p class="mt-1 text-sm">Semua properti sudah ditinjau.</p>
            </div>

            <!-- Properties Grid -->
            <div v-else class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <Card v-for="property in properties.data" :key="property.id">
                    <!-- Thumbnail -->
                    <div class="bg-muted relative aspect-video w-full overflow-hidden rounded-t-lg">
                        <img
                            v-if="property.media && property.media.length > 0"
                            :src="property.media[0].original_url"
                            :alt="property.title"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full items-center justify-center">
                            <Building2 class="text-muted-foreground h-10 w-10" />
                        </div>
                        <Badge variant="outline" class="absolute top-2 left-2 bg-white/90 dark:bg-black/70">
                            {{ propertyTypeLabel[property.property_type] ?? property.property_type }}
                        </Badge>
                    </div>

                    <CardHeader class="pb-2">
                        <Link
                            :href="`/admin/properti/${property.id}`"
                            class="font-semibold hover:underline line-clamp-1"
                        >
                            {{ property.title }}
                        </Link>
                        <CardDescription class="flex items-center gap-1">
                            <MapPin class="h-3 w-3" />
                            {{ property.city }}, {{ property.district }}
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="space-y-2 pb-3">
                        <p class="text-lg font-bold">{{ formatCurrency(property.price) }}</p>
                        <div class="text-muted-foreground flex items-center gap-3 text-xs">
                            <span class="flex items-center gap-1">
                                <Ruler class="h-3 w-3" /> {{ property.land_area }} m&sup2;
                            </span>
                            <span v-if="property.building_area">LB {{ property.building_area }} m&sup2;</span>
                        </div>
                        <div class="text-muted-foreground flex items-center gap-2 text-xs">
                            <Clock class="h-3 w-3" />
                            Dikirim oleh {{ property.seller?.name ?? '-' }} &middot; {{ formatDate(property.created_at) }}
                        </div>
                    </CardContent>

                    <CardFooter class="flex flex-col gap-2 pt-0">
                        <!-- Reject Form -->
                        <div v-if="rejectingId === property.id" class="w-full space-y-2">
                            <textarea
                                v-model="rejectReason"
                                rows="2"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                placeholder="Alasan penolakan..."
                            />
                            <div class="flex gap-2">
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    class="flex-1"
                                    :disabled="!rejectReason.trim()"
                                    @click="rejectProperty(property.id)"
                                >
                                    Konfirmasi Tolak
                                </Button>
                                <Button variant="outline" size="sm" @click="cancelReject">
                                    Batal
                                </Button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div v-else class="flex w-full gap-2">
                            <Button size="sm" class="flex-1 bg-green-600 hover:bg-green-700" @click="approveProperty(property.id)">
                                <CheckCircle class="mr-1 h-4 w-4" />
                                Setujui
                            </Button>
                            <Button variant="destructive" size="sm" class="flex-1" @click="startReject(property.id)">
                                <XCircle class="mr-1 h-4 w-4" />
                                Tolak
                            </Button>
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="`/admin/properti/${property.id}`">
                                    <Eye class="h-4 w-4" />
                                </Link>
                            </Button>
                        </div>
                    </CardFooter>
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
