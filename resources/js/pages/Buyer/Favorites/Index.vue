<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Property, type Paginated } from '@/types';
import {
    Heart,
    Building2,
    MapPin,
    BedDouble,
    Bath,
    Ruler,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

type Props = {
    favorites: Paginated<Property>;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Buyer', href: '/buyer/dashboard' },
    { title: 'Favorit', href: '/buyer/favorit' },
];

function formatCurrency(value: number): string {
    if (value >= 1_000_000_000) return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    if (value >= 1_000_000) return `Rp ${(value / 1_000_000).toFixed(0)} Jt`;
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}

const propertyTypeLabel: Record<string, string> = {
    rumah: 'Rumah',
    apartemen: 'Apartemen',
    tanah: 'Tanah',
    ruko: 'Ruko',
    gudang: 'Gudang',
    kantor: 'Kantor',
};

function removeFavorite(propertyId: number) {
    router.delete(`/buyer/favorit/${propertyId}`, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Favorit Saya" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold">Favorit Saya</h1>
                <p class="text-muted-foreground text-sm">{{ favorites.total }} properti disimpan</p>
            </div>

            <!-- Empty State -->
            <div v-if="favorites.data.length === 0" class="flex flex-col items-center justify-center py-16">
                <Heart class="text-muted-foreground mb-4 h-16 w-16 opacity-50" />
                <h3 class="text-lg font-semibold">Belum ada properti favorit</h3>
                <p class="text-muted-foreground mt-1 text-sm">Simpan properti yang Anda sukai untuk melihatnya nanti.</p>
                <Button class="mt-4" as-child>
                    <Link href="/properti">Cari Properti</Link>
                </Button>
            </div>

            <!-- Favorites Grid -->
            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Card v-for="property in favorites.data" :key="property.id" class="group overflow-hidden">
                    <div class="relative">
                        <Link :href="`/properti/${property.slug}`">
                            <div class="bg-muted flex h-48 items-center justify-center overflow-hidden">
                                <img
                                    v-if="property.media && property.media.length > 0"
                                    :src="property.media[0].original_url"
                                    :alt="property.title"
                                    class="h-full w-full object-cover transition group-hover:scale-105"
                                />
                                <Building2 v-else class="text-muted-foreground h-12 w-12" />
                            </div>
                        </Link>
                        <button
                            class="absolute right-2 top-2 rounded-full bg-white/90 p-2 text-red-500 shadow-sm transition hover:bg-white hover:text-red-600 dark:bg-gray-900/90 dark:hover:bg-gray-900"
                            @click="removeFavorite(property.id)"
                            title="Hapus dari favorit"
                        >
                            <Heart class="h-4 w-4 fill-current" />
                        </button>
                        <Badge class="absolute left-2 top-2" variant="secondary">
                            {{ propertyTypeLabel[property.property_type] ?? property.property_type }}
                        </Badge>
                    </div>
                    <CardContent class="p-4">
                        <Link :href="`/properti/${property.slug}`" class="hover:underline">
                            <h3 class="truncate font-semibold">{{ property.title }}</h3>
                        </Link>
                        <p class="text-primary mt-1 text-lg font-bold">{{ formatCurrency(property.price) }}</p>
                        <p class="text-muted-foreground mt-1 flex items-center gap-1 text-sm">
                            <MapPin class="h-3.5 w-3.5" />
                            {{ property.city }}, {{ property.district }}
                        </p>
                        <div class="text-muted-foreground mt-2 flex items-center gap-3 text-sm">
                            <span v-if="property.bedrooms" class="flex items-center gap-1">
                                <BedDouble class="h-3.5 w-3.5" />
                                {{ property.bedrooms }} KT
                            </span>
                            <span v-if="property.bathrooms" class="flex items-center gap-1">
                                <Bath class="h-3.5 w-3.5" />
                                {{ property.bathrooms }} KM
                            </span>
                            <span v-if="property.land_area" class="flex items-center gap-1">
                                <Ruler class="h-3.5 w-3.5" />
                                {{ property.land_area }} m&sup2;
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Pagination -->
            <div v-if="favorites.last_page > 1" class="flex items-center justify-center gap-2 py-4">
                <template v-for="link in favorites.links" :key="link.label">
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
