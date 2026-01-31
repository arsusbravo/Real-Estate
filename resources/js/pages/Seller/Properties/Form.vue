<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Property } from '@/types';
import {
    ArrowLeft,
    Save,
    Plus,
    X,
} from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { computed, ref } from 'vue';

type SelectOption = { value: string; name: string };

type Props = {
    property?: Property;
    propertyTypes: SelectOption[];
    listingTypes: SelectOption[];
    certificateTypes: SelectOption[];
    furnishingTypes: SelectOption[];
};

const props = defineProps<Props>();

const isEditing = computed(() => !!props.property);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Seller', href: '/seller/dashboard' },
    { title: 'Properti', href: '/seller/properti' },
    { title: isEditing.value ? 'Edit Properti' : 'Tambah Properti', href: '#' },
];

const form = useForm({
    title: props.property?.title ?? '',
    description: props.property?.description ?? '',
    property_type: props.property?.property_type ?? '',
    listing_type: props.property?.listing_type ?? '',
    price: props.property?.price ?? '',
    price_negotiable: props.property?.price_negotiable ?? false,
    land_area: props.property?.land_area ?? '',
    building_area: props.property?.building_area ?? '',
    bedrooms: props.property?.bedrooms ?? '',
    bathrooms: props.property?.bathrooms ?? '',
    floors: props.property?.floors ?? 1,
    parking_spaces: props.property?.parking_spaces ?? '',
    furnishing: props.property?.furnishing ?? '',
    facing_direction: props.property?.facing_direction ?? '',
    province: props.property?.province ?? 'DKI Jakarta',
    city: props.property?.city ?? '',
    district: props.property?.district ?? '',
    subdistrict: props.property?.subdistrict ?? '',
    address: props.property?.address ?? '',
    postal_code: props.property?.postal_code ?? '',
    certificate_type: props.property?.certificate_type ?? '',
    certificate_number: props.property?.certificate_number ?? '',
    certificate_expiry: props.property?.certificate_expiry ?? '',
    features: (props.property?.features ?? []).map(f => f.feature_name),
});

const jakartaCities = [
    'Jakarta Pusat',
    'Jakarta Utara',
    'Jakarta Barat',
    'Jakarta Selatan',
    'Jakarta Timur',
    'Kepulauan Seribu',
];

const newFeature = ref('');

function addFeature() {
    const name = newFeature.value.trim();
    if (name && !form.features.includes(name)) {
        form.features.push(name);
    }
    newFeature.value = '';
}

function removeFeature(index: number) {
    form.features.splice(index, 1);
}

function submit() {
    if (isEditing.value && props.property) {
        form.put(`/seller/properti/${props.property.id}`);
    } else {
        form.post('/seller/properti');
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Edit Properti' : 'Tambah Properti'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center gap-3">
                <Button variant="outline" size="icon" as-child>
                    <Link href="/seller/properti">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">{{ isEditing ? 'Edit Properti' : 'Tambah Properti' }}</h1>
                    <p class="text-muted-foreground text-sm">
                        {{ isEditing ? 'Perbarui informasi properti Anda' : 'Isi data properti yang ingin Anda jual atau sewakan' }}
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Section 1: Informasi Dasar -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Dasar</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="title">Judul Properti</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                type="text"
                                placeholder="Contoh: Rumah Mewah 2 Lantai di Menteng"
                            />
                            <p v-if="form.errors.title" class="text-sm text-red-600">{{ form.errors.title }}</p>
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Deskripsi</Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="5"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                placeholder="Jelaskan properti Anda secara detail..."
                            />
                            <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="property_type">Tipe Properti</Label>
                                <select
                                    id="property_type"
                                    v-model="form.property_type"
                                    class="border-input bg-background ring-offset-background focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                >
                                    <option value="" disabled>Pilih tipe properti</option>
                                    <option v-for="opt in propertyTypes" :key="opt.value" :value="opt.value">
                                        {{ opt.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.property_type" class="text-sm text-red-600">{{ form.errors.property_type }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="listing_type">Tipe Listing</Label>
                                <select
                                    id="listing_type"
                                    v-model="form.listing_type"
                                    class="border-input bg-background ring-offset-background focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                >
                                    <option value="" disabled>Pilih tipe listing</option>
                                    <option v-for="opt in listingTypes" :key="opt.value" :value="opt.value">
                                        {{ opt.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.listing_type" class="text-sm text-red-600">{{ form.errors.listing_type }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Section 2: Harga -->
                <Card>
                    <CardHeader>
                        <CardTitle>Harga</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label for="price">Harga (Rp)</Label>
                            <Input
                                id="price"
                                v-model="form.price"
                                type="number"
                                min="0"
                                placeholder="Contoh: 1500000000"
                            />
                            <p v-if="form.errors.price" class="text-sm text-red-600">{{ form.errors.price }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="price_negotiable"
                                :model-value="form.price_negotiable"
                                @update:model-value="(val: boolean | 'indeterminate') => form.price_negotiable = val === true"
                            />
                            <Label for="price_negotiable" class="cursor-pointer">Harga bisa dinegosiasi</Label>
                        </div>
                    </CardContent>
                </Card>

                <!-- Section 3: Spesifikasi -->
                <Card>
                    <CardHeader>
                        <CardTitle>Spesifikasi</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                            <div class="space-y-2">
                                <Label for="land_area">Luas Tanah (m²)</Label>
                                <Input
                                    id="land_area"
                                    v-model="form.land_area"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                />
                                <p v-if="form.errors.land_area" class="text-sm text-red-600">{{ form.errors.land_area }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="building_area">Luas Bangunan (m²)</Label>
                                <Input
                                    id="building_area"
                                    v-model="form.building_area"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                />
                                <p v-if="form.errors.building_area" class="text-sm text-red-600">{{ form.errors.building_area }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="bedrooms">Kamar Tidur</Label>
                                <Input
                                    id="bedrooms"
                                    v-model="form.bedrooms"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                />
                                <p v-if="form.errors.bedrooms" class="text-sm text-red-600">{{ form.errors.bedrooms }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="bathrooms">Kamar Mandi</Label>
                                <Input
                                    id="bathrooms"
                                    v-model="form.bathrooms"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                />
                                <p v-if="form.errors.bathrooms" class="text-sm text-red-600">{{ form.errors.bathrooms }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="floors">Jumlah Lantai</Label>
                                <Input
                                    id="floors"
                                    v-model="form.floors"
                                    type="number"
                                    min="1"
                                    placeholder="1"
                                />
                                <p v-if="form.errors.floors" class="text-sm text-red-600">{{ form.errors.floors }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="parking_spaces">Tempat Parkir</Label>
                                <Input
                                    id="parking_spaces"
                                    v-model="form.parking_spaces"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                />
                                <p v-if="form.errors.parking_spaces" class="text-sm text-red-600">{{ form.errors.parking_spaces }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="furnishing">Perabotan</Label>
                                <select
                                    id="furnishing"
                                    v-model="form.furnishing"
                                    class="border-input bg-background ring-offset-background focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                >
                                    <option value="" disabled>Pilih</option>
                                    <option v-for="opt in furnishingTypes" :key="opt.value" :value="opt.value">
                                        {{ opt.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.furnishing" class="text-sm text-red-600">{{ form.errors.furnishing }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="facing_direction">Hadap</Label>
                                <Input
                                    id="facing_direction"
                                    v-model="form.facing_direction"
                                    type="text"
                                    placeholder="Contoh: Utara"
                                />
                                <p v-if="form.errors.facing_direction" class="text-sm text-red-600">{{ form.errors.facing_direction }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Section 4: Lokasi -->
                <Card>
                    <CardHeader>
                        <CardTitle>Lokasi</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="province">Provinsi</Label>
                                <Input
                                    id="province"
                                    v-model="form.province"
                                    type="text"
                                    placeholder="DKI Jakarta"
                                />
                                <p v-if="form.errors.province" class="text-sm text-red-600">{{ form.errors.province }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="city">Kota/Kabupaten</Label>
                                <select
                                    id="city"
                                    v-model="form.city"
                                    class="border-input bg-background ring-offset-background focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                >
                                    <option value="" disabled>Pilih kota</option>
                                    <option v-for="city in jakartaCities" :key="city" :value="city">
                                        {{ city }}
                                    </option>
                                </select>
                                <p v-if="form.errors.city" class="text-sm text-red-600">{{ form.errors.city }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="district">Kecamatan</Label>
                                <Input
                                    id="district"
                                    v-model="form.district"
                                    type="text"
                                    placeholder="Contoh: Menteng"
                                />
                                <p v-if="form.errors.district" class="text-sm text-red-600">{{ form.errors.district }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="subdistrict">Kelurahan</Label>
                                <Input
                                    id="subdistrict"
                                    v-model="form.subdistrict"
                                    type="text"
                                    placeholder="Contoh: Menteng"
                                />
                                <p v-if="form.errors.subdistrict" class="text-sm text-red-600">{{ form.errors.subdistrict }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="address">Alamat Lengkap</Label>
                            <textarea
                                id="address"
                                v-model="form.address"
                                rows="3"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                placeholder="Alamat lengkap properti..."
                            />
                            <p v-if="form.errors.address" class="text-sm text-red-600">{{ form.errors.address }}</p>
                        </div>

                        <div class="max-w-xs space-y-2">
                            <Label for="postal_code">Kode Pos</Label>
                            <Input
                                id="postal_code"
                                v-model="form.postal_code"
                                type="text"
                                placeholder="Contoh: 10310"
                            />
                            <p v-if="form.errors.postal_code" class="text-sm text-red-600">{{ form.errors.postal_code }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Section 5: Sertifikat -->
                <Card>
                    <CardHeader>
                        <CardTitle>Sertifikat</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="certificate_type">Tipe Sertifikat</Label>
                                <select
                                    id="certificate_type"
                                    v-model="form.certificate_type"
                                    class="border-input bg-background ring-offset-background focus-visible:ring-ring w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                >
                                    <option value="" disabled>Pilih tipe sertifikat</option>
                                    <option v-for="opt in certificateTypes" :key="opt.value" :value="opt.value">
                                        {{ opt.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.certificate_type" class="text-sm text-red-600">{{ form.errors.certificate_type }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="certificate_number">Nomor Sertifikat</Label>
                                <Input
                                    id="certificate_number"
                                    v-model="form.certificate_number"
                                    type="text"
                                    placeholder="Nomor sertifikat"
                                />
                                <p v-if="form.errors.certificate_number" class="text-sm text-red-600">{{ form.errors.certificate_number }}</p>
                            </div>

                            <div class="space-y-2">
                                <Label for="certificate_expiry">Tanggal Kedaluwarsa</Label>
                                <Input
                                    id="certificate_expiry"
                                    v-model="form.certificate_expiry"
                                    type="date"
                                />
                                <p v-if="form.errors.certificate_expiry" class="text-sm text-red-600">{{ form.errors.certificate_expiry }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Section 6: Fitur -->
                <Card>
                    <CardHeader>
                        <CardTitle>Fitur (Opsional)</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex gap-2">
                            <Input
                                v-model="newFeature"
                                type="text"
                                placeholder="Nama fitur, contoh: Kolam Renang"
                                class="max-w-sm"
                                @keydown.enter.prevent="addFeature"
                            />
                            <Button type="button" variant="outline" @click="addFeature">
                                <Plus class="mr-1 h-4 w-4" />
                                Tambah
                            </Button>
                        </div>

                        <div v-if="form.features.length > 0" class="flex flex-wrap gap-2">
                            <div
                                v-for="(feature, index) in form.features"
                                :key="index"
                                class="bg-secondary text-secondary-foreground inline-flex items-center gap-1 rounded-full px-3 py-1 text-sm"
                            >
                                {{ feature }}
                                <button
                                    type="button"
                                    class="hover:text-destructive ml-1"
                                    @click="removeFeature(index)"
                                >
                                    <X class="h-3 w-3" />
                                </button>
                            </div>
                        </div>
                        <p v-else class="text-muted-foreground text-sm">Belum ada fitur ditambahkan</p>
                        <p v-if="form.errors.features" class="text-sm text-red-600">{{ form.errors.features }}</p>
                    </CardContent>
                </Card>

                <!-- Submit -->
                <div class="flex items-center gap-3">
                    <Button type="submit" :disabled="form.processing">
                        <Save class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Perbarui Properti' : 'Simpan Properti') }}
                    </Button>
                    <Button type="button" variant="outline" as-child>
                        <Link href="/seller/properti">Batal</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
