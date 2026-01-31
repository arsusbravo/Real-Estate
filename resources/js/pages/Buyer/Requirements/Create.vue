<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Save, ArrowLeft } from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Buyer', href: '/buyer/dashboard' },
    { title: 'Kebutuhan', href: '/buyer/kebutuhan' },
    { title: 'Buat Kebutuhan', href: '/buyer/kebutuhan/buat' },
];

const propertyTypes = [
    { value: 'rumah', label: 'Rumah' },
    { value: 'apartemen', label: 'Apartemen' },
    { value: 'tanah', label: 'Tanah' },
    { value: 'ruko', label: 'Ruko' },
    { value: 'gudang', label: 'Gudang' },
    { value: 'kantor', label: 'Kantor' },
];

const jakartaAreas = [
    'Jakarta Pusat',
    'Jakarta Selatan',
    'Jakarta Barat',
    'Jakarta Timur',
    'Jakarta Utara',
    'Tangerang',
    'Tangerang Selatan',
    'Bekasi',
    'Depok',
    'Bogor',
];

const urgencyOptions = [
    { value: 'segera', label: 'Segera' },
    { value: '1_bulan', label: '1 Bulan' },
    { value: '3_bulan', label: '3 Bulan' },
    { value: '6_bulan', label: '6 Bulan' },
    { value: 'fleksibel', label: 'Fleksibel' },
];

const form = useForm({
    property_types: [] as string[],
    listing_type: 'beli' as 'beli' | 'sewa',
    min_price: undefined as number | undefined,
    max_price: undefined as number | undefined,
    min_land_area: undefined as number | undefined,
    max_land_area: undefined as number | undefined,
    min_building_area: undefined as number | undefined,
    max_building_area: undefined as number | undefined,
    min_bedrooms: undefined as number | undefined,
    min_bathrooms: undefined as number | undefined,
    preferred_locations: [] as string[],
    urgency: 'fleksibel' as string,
    additional_notes: '' as string,
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
    form.post('/buyer/kebutuhan', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Buat Kebutuhan Properti" />

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
                    <h1 class="text-2xl font-bold">Buat Kebutuhan Properti</h1>
                    <p class="text-muted-foreground text-sm">Deskripsikan properti yang Anda cari</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6 max-w-3xl">
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
                                    v-for="pt in propertyTypes"
                                    :key="pt.value"
                                    class="flex items-center gap-2"
                                >
                                    <Checkbox
                                        :checked="form.property_types.includes(pt.value)"
                                        @update:checked="togglePropertyType(pt.value)"
                                    />
                                    <span class="text-sm">{{ pt.label }}</span>
                                </label>
                            </div>
                            <p v-if="form.errors.property_types" class="mt-1 text-sm text-red-600">{{ form.errors.property_types }}</p>
                        </div>

                        <div>
                            <Label class="mb-2 block text-sm font-medium">Tipe Listing</Label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        v-model="form.listing_type"
                                        value="beli"
                                        class="text-primary focus:ring-primary"
                                    />
                                    <span class="text-sm">Beli</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input
                                        type="radio"
                                        v-model="form.listing_type"
                                        value="sewa"
                                        class="text-primary focus:ring-primary"
                                    />
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
                        <CardDescription>Tentukan budget dan spesifikasi yang diinginkan</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label for="min_price">Harga Minimum (Rp)</Label>
                                <Input id="min_price" v-model.number="form.min_price" type="number" placeholder="Contoh: 500000000" />
                                <p v-if="form.errors.min_price" class="mt-1 text-sm text-red-600">{{ form.errors.min_price }}</p>
                            </div>
                            <div>
                                <Label for="max_price">Harga Maksimum (Rp)</Label>
                                <Input id="max_price" v-model.number="form.max_price" type="number" placeholder="Contoh: 2000000000" />
                                <p v-if="form.errors.max_price" class="mt-1 text-sm text-red-600">{{ form.errors.max_price }}</p>
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label for="min_land_area">Luas Tanah Min (m&sup2;)</Label>
                                <Input id="min_land_area" v-model.number="form.min_land_area" type="number" placeholder="Contoh: 100" />
                            </div>
                            <div>
                                <Label for="max_land_area">Luas Tanah Max (m&sup2;)</Label>
                                <Input id="max_land_area" v-model.number="form.max_land_area" type="number" placeholder="Contoh: 500" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label for="min_building_area">Luas Bangunan Min (m&sup2;)</Label>
                                <Input id="min_building_area" v-model.number="form.min_building_area" type="number" placeholder="Contoh: 80" />
                            </div>
                            <div>
                                <Label for="max_building_area">Luas Bangunan Max (m&sup2;)</Label>
                                <Input id="max_building_area" v-model.number="form.max_building_area" type="number" placeholder="Contoh: 300" />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label for="min_bedrooms">Kamar Tidur Minimum</Label>
                                <Input id="min_bedrooms" v-model.number="form.min_bedrooms" type="number" placeholder="Contoh: 3" />
                            </div>
                            <div>
                                <Label for="min_bathrooms">Kamar Mandi Minimum</Label>
                                <Input id="min_bathrooms" v-model.number="form.min_bathrooms" type="number" placeholder="Contoh: 2" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Lokasi Preferensi -->
                <Card>
                    <CardHeader>
                        <CardTitle>Lokasi Preferensi</CardTitle>
                        <CardDescription>Pilih area yang Anda inginkan</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <label
                                v-for="area in jakartaAreas"
                                :key="area"
                                class="flex items-center gap-2"
                            >
                                <Checkbox
                                    :checked="form.preferred_locations.includes(area)"
                                    @update:checked="toggleLocation(area)"
                                />
                                <span class="text-sm">{{ area }}</span>
                            </label>
                        </div>
                        <p v-if="form.errors.preferred_locations" class="mt-1 text-sm text-red-600">{{ form.errors.preferred_locations }}</p>
                    </CardContent>
                </Card>

                <!-- Urgency & Notes -->
                <Card>
                    <CardHeader>
                        <CardTitle>Urgensi & Catatan</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <Label class="mb-2 block text-sm font-medium">Seberapa mendesak kebutuhan Anda?</Label>
                            <div class="flex flex-wrap gap-3">
                                <label
                                    v-for="opt in urgencyOptions"
                                    :key="opt.value"
                                    class="flex items-center gap-2"
                                >
                                    <input
                                        type="radio"
                                        v-model="form.urgency"
                                        :value="opt.value"
                                        class="text-primary focus:ring-primary"
                                    />
                                    <span class="text-sm">{{ opt.label }}</span>
                                </label>
                            </div>
                            <p v-if="form.errors.urgency" class="mt-1 text-sm text-red-600">{{ form.errors.urgency }}</p>
                        </div>

                        <div>
                            <Label for="additional_notes">Catatan Tambahan</Label>
                            <textarea
                                id="additional_notes"
                                v-model="form.additional_notes"
                                rows="4"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring mt-1 flex w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                placeholder="Deskripsikan preferensi lain yang Anda inginkan..."
                            />
                            <p v-if="form.errors.additional_notes" class="mt-1 text-sm text-red-600">{{ form.errors.additional_notes }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Submit -->
                <div class="flex items-center gap-3">
                    <Button type="submit" :disabled="form.processing">
                        <Save class="mr-2 h-4 w-4" />
                        Simpan Kebutuhan
                    </Button>
                    <Button variant="outline" as-child>
                        <Link href="/buyer/kebutuhan">Batal</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
