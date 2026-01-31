<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type BuyerRequirement, type Property } from '@/types';
import { Save, ArrowLeft } from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';

type Props = {
    requirement: BuyerRequirement;
    propertyTypes: { value: string; name: string }[];
    listingTypes: { value: string; name: string }[];
    certificateTypes: { value: string; name: string }[];
    urgencyOptions: { value: string; name: string }[];
    cities: string[];
    matchingProperties: Property[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Buyer', href: '/buyer/dashboard' },
    { title: 'Kebutuhan', href: '/buyer/kebutuhan' },
    { title: 'Edit Kebutuhan', href: '#' },
];

const propertyTypeLabels: Record<string, string> = {
    rumah: 'Rumah',
    apartemen: 'Apartemen',
    tanah: 'Tanah',
    ruko: 'Ruko',
    gudang: 'Gudang',
    kantor: 'Kantor',
};

const urgencyLabels: Record<string, string> = {
    segera: 'Segera',
    '1_bulan': '1 Bulan',
    '3_bulan': '3 Bulan',
    '6_bulan': '6 Bulan',
    fleksibel: 'Fleksibel',
};

const form = useForm({
    property_types: [...(props.requirement.property_types ?? [])] as string[],
    listing_type: props.requirement.listing_type ?? 'beli',
    min_price: props.requirement.min_price ?? undefined as number | undefined,
    max_price: props.requirement.max_price ?? undefined as number | undefined,
    min_land_area: props.requirement.min_land_area ?? undefined as number | undefined,
    max_land_area: props.requirement.max_land_area ?? undefined as number | undefined,
    min_building_area: props.requirement.min_building_area ?? undefined as number | undefined,
    max_building_area: props.requirement.max_building_area ?? undefined as number | undefined,
    min_bedrooms: props.requirement.min_bedrooms ?? undefined as number | undefined,
    min_bathrooms: props.requirement.min_bathrooms ?? undefined as number | undefined,
    preferred_locations: [...(props.requirement.preferred_locations ?? [])],
    urgency: props.requirement.urgency ?? 'fleksibel',
    additional_notes: props.requirement.additional_notes ?? '',
    status: props.requirement.status ?? 'active',
});

function togglePropertyType(value: string) {
    const index = form.property_types.indexOf(value);
    if (index === -1) {
        form.property_types.push(value);
    } else {
        form.property_types.splice(index, 1);
    }
}

function toggleLocation(value: string) {
    const index = form.preferred_locations.indexOf(value);
    if (index === -1) {
        form.preferred_locations.push(value);
    } else {
        form.preferred_locations.splice(index, 1);
    }
}

function submit() {
    form.put(`/buyer/kebutuhan/${props.requirement.id}`, {
        preserveScroll: true,
    });
}

function formatCurrency(value: number): string {
    if (value >= 1_000_000_000) return `Rp ${(value / 1_000_000_000).toFixed(1)} M`;
    if (value >= 1_000_000) return `Rp ${(value / 1_000_000).toFixed(0)} Jt`;
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
}
</script>

<template>
    <Head title="Edit Kebutuhan Properti" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link href="/buyer/kebutuhan">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Edit Kebutuhan Properti</h1>
                    <p class="text-muted-foreground text-sm">Perbarui kriteria pencarian properti Anda</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-6 lg:col-span-2">
                    <!-- Status -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Status</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" v-model="form.status" value="active" class="text-primary focus:ring-primary" />
                                    <span class="text-sm">Aktif</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" v-model="form.status" value="closed" class="text-primary focus:ring-primary" />
                                    <span class="text-sm">Ditutup</span>
                                </label>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Tipe Properti -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Tipe Properti</CardTitle>
                            <CardDescription>Pilih tipe properti yang Anda cari</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div>
                                <Label class="mb-2 block text-sm font-medium">Tipe Properti</Label>
                                <div class="flex flex-wrap gap-3">
                                    <label
                                        v-for="pt in (propertyTypes.length > 0 ? propertyTypes : Object.entries(propertyTypeLabels).map(([v, n]) => ({ value: v, name: n })))"
                                        :key="pt.value"
                                        class="flex items-center gap-2"
                                    >
                                        <Checkbox
                                            :checked="form.property_types.includes(pt.value)"
                                            @update:checked="togglePropertyType(pt.value)"
                                        />
                                        <span class="text-sm">{{ pt.name ?? propertyTypeLabels[pt.value] ?? pt.value }}</span>
                                    </label>
                                </div>
                                <p v-if="form.errors.property_types" class="mt-1 text-sm text-red-600">{{ form.errors.property_types }}</p>
                            </div>

                            <div>
                                <Label class="mb-2 block text-sm font-medium">Tipe Listing</Label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2">
                                        <input type="radio" v-model="form.listing_type" value="beli" class="text-primary focus:ring-primary" />
                                        <span class="text-sm">Beli</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="radio" v-model="form.listing_type" value="sewa" class="text-primary focus:ring-primary" />
                                        <span class="text-sm">Sewa</span>
                                    </label>
                                </div>
                                <p v-if="form.errors.listing_type" class="mt-1 text-sm text-red-600">{{ form.errors.listing_type }}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Budget & Spesifikasi -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Budget & Spesifikasi</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <Label for="min_price">Harga Minimum (Rp)</Label>
                                    <Input id="min_price" v-model.number="form.min_price" type="number" placeholder="500000000" />
                                    <p v-if="form.errors.min_price" class="mt-1 text-sm text-red-600">{{ form.errors.min_price }}</p>
                                </div>
                                <div>
                                    <Label for="max_price">Harga Maksimum (Rp)</Label>
                                    <Input id="max_price" v-model.number="form.max_price" type="number" placeholder="2000000000" />
                                    <p v-if="form.errors.max_price" class="mt-1 text-sm text-red-600">{{ form.errors.max_price }}</p>
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <Label>Luas Tanah Min (m²)</Label>
                                    <Input v-model.number="form.min_land_area" type="number" placeholder="100" />
                                </div>
                                <div>
                                    <Label>Luas Tanah Max (m²)</Label>
                                    <Input v-model.number="form.max_land_area" type="number" placeholder="500" />
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <Label>Luas Bangunan Min (m²)</Label>
                                    <Input v-model.number="form.min_building_area" type="number" placeholder="80" />
                                </div>
                                <div>
                                    <Label>Luas Bangunan Max (m²)</Label>
                                    <Input v-model.number="form.max_building_area" type="number" placeholder="300" />
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <Label>Kamar Tidur Minimum</Label>
                                    <Input v-model.number="form.min_bedrooms" type="number" placeholder="3" />
                                </div>
                                <div>
                                    <Label>Kamar Mandi Minimum</Label>
                                    <Input v-model.number="form.min_bathrooms" type="number" placeholder="2" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Lokasi Preferensi -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Lokasi Preferensi</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <label v-for="area in cities" :key="area" class="flex items-center gap-2">
                                    <Checkbox
                                        :checked="form.preferred_locations.includes(area)"
                                        @update:checked="toggleLocation(area)"
                                    />
                                    <span class="text-sm">{{ area }}</span>
                                </label>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Urgency & Notes -->
                    <Card>
                        <CardHeader><CardTitle>Urgensi & Catatan</CardTitle></CardHeader>
                        <CardContent class="space-y-4">
                            <div>
                                <Label class="mb-2 block text-sm font-medium">Seberapa mendesak?</Label>
                                <div class="flex flex-wrap gap-3">
                                    <label
                                        v-for="opt in (urgencyOptions.length > 0 ? urgencyOptions : Object.entries(urgencyLabels).map(([v, n]) => ({ value: v, name: n })))"
                                        :key="opt.value"
                                        class="flex items-center gap-2"
                                    >
                                        <input type="radio" v-model="form.urgency" :value="opt.value" class="text-primary focus:ring-primary" />
                                        <span class="text-sm">{{ opt.name ?? urgencyLabels[opt.value] ?? opt.value }}</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <Label for="additional_notes">Catatan Tambahan</Label>
                                <textarea
                                    id="additional_notes"
                                    v-model="form.additional_notes"
                                    rows="4"
                                    class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring mt-1 flex w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                    placeholder="Preferensi lain..."
                                />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Submit -->
                    <div class="flex items-center gap-3">
                        <Button type="submit" :disabled="form.processing">
                            <Save class="mr-2 h-4 w-4" />
                            Simpan Perubahan
                        </Button>
                        <Button variant="outline" as-child>
                            <Link href="/buyer/kebutuhan">Batal</Link>
                        </Button>
                    </div>
                </form>

                <!-- Matching Properties Sidebar -->
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle>Properti Cocok</CardTitle>
                            <CardDescription>Properti yang sesuai kriteria Anda</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="matchingProperties.length === 0" class="text-muted-foreground py-4 text-center text-sm">
                                Belum ada properti yang cocok
                            </div>
                            <div v-else class="space-y-3">
                                <Link
                                    v-for="prop in matchingProperties"
                                    :key="prop.id"
                                    :href="`/properti/${prop.slug || prop.id}`"
                                    class="block rounded-lg border p-3 transition-colors hover:bg-muted/50"
                                >
                                    <div class="flex gap-3">
                                        <div class="bg-muted flex h-14 w-20 flex-shrink-0 items-center justify-center overflow-hidden rounded">
                                            <img
                                                v-if="prop.media && prop.media.length > 0"
                                                :src="prop.media[0].original_url"
                                                :alt="prop.title"
                                                class="h-full w-full object-cover"
                                            />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium">{{ prop.title }}</p>
                                            <p class="text-primary text-sm font-bold">{{ formatCurrency(prop.price) }}</p>
                                            <p class="text-muted-foreground text-xs">{{ prop.city }}</p>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
