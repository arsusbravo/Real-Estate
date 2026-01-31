<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type Property } from '@/types';
import {
    Building2,
    MapPin,
    Ruler,
    BedDouble,
    Bath,
    Car,
    Home,
    Layers,
    Compass,
    Sofa,
    FileText,
    User,
    Send,
    ChevronLeft,
    ChevronRight,
    Phone,
    Mail,
    MessageSquare,
    CheckCircle,
    X,
} from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ref, computed } from 'vue';

type Props = {
    property: Property;
    relatedProperties: Property[];
};

const props = defineProps<Props>();

// Image gallery state
const selectedImageIndex = ref(0);

const images = computed(() => {
    return props.property.media && props.property.media.length > 0
        ? props.property.media
        : [];
});

function prevImage() {
    if (images.value.length === 0) return;
    selectedImageIndex.value = selectedImageIndex.value === 0
        ? images.value.length - 1
        : selectedImageIndex.value - 1;
}

function nextImage() {
    if (images.value.length === 0) return;
    selectedImageIndex.value = selectedImageIndex.value === images.value.length - 1
        ? 0
        : selectedImageIndex.value + 1;
}

// Inquiry form
const inquiryForm = useForm({
    name: '',
    email: '',
    phone: '',
    message: `Halo, saya tertarik dengan properti "${props.property.title}". Mohon informasi lebih lanjut.`,
    preferred_contact_method: 'whatsapp' as 'phone' | 'whatsapp' | 'email',
});

const inquirySubmitted = ref(false);

function submitInquiry() {
    inquiryForm.post(`/properti/${props.property.id}/inquiry`, {
        preserveScroll: true,
        onSuccess: () => {
            inquirySubmitted.value = true;
            inquiryForm.reset();
        },
    });
}

// Formatters
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

function formatFullCurrency(value: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
}

// Labels
const propertyTypeLabel: Record<string, string> = {
    rumah: 'Rumah',
    apartemen: 'Apartemen',
    tanah: 'Tanah',
    ruko: 'Ruko',
    gudang: 'Gudang',
    kantor: 'Kantor',
};

const listingTypeLabel: Record<string, string> = {
    dijual: 'Dijual',
    disewakan: 'Disewakan',
};

const statusLabel: Record<string, string> = {
    draft: 'Draf',
    pending_review: 'Menunggu Review',
    active: 'Aktif',
    sold: 'Terjual',
    rented: 'Tersewa',
    inactive: 'Nonaktif',
    rejected: 'Ditolak',
};

const certificateTypeLabel: Record<string, string> = {
    shm: 'SHM',
    shgb: 'SHGB',
    shp: 'SHP',
    girik: 'Girik',
    strata_title: 'Strata Title',
};

const furnishingLabel: Record<string, string> = {
    unfurnished: 'Tanpa Perabot',
    semi_furnished: 'Semi Furnished',
    fully_furnished: 'Fully Furnished',
};

const contactMethodLabel: Record<string, string> = {
    phone: 'Telepon',
    whatsapp: 'WhatsApp',
    email: 'Email',
};

const inputClass = 'w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500';
const selectClass = 'w-full rounded-md border border-neutral-300 bg-white px-3 py-1 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 mt-1 flex h-9';
</script>

<template>
    <Head :title="`${property.title} - PropertiKu`" />

    <PublicLayout>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="mb-4 flex items-center gap-2 text-sm text-neutral-500">
                <Link href="/properti" class="hover:text-emerald-600">Properti</Link>
                <span>/</span>
                <span class="line-clamp-1 text-neutral-800">{{ property.title }}</span>
            </nav>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Main Content (2 cols) -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Image Gallery -->
                    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm">
                        <div class="relative">
                            <!-- Main Image -->
                            <div class="aspect-[16/9] overflow-hidden bg-neutral-100">
                                <img
                                    v-if="images.length > 0"
                                    :src="images[selectedImageIndex].original_url"
                                    :alt="property.title"
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="flex h-full items-center justify-center">
                                    <Building2 class="h-16 w-16 text-neutral-300" />
                                </div>
                            </div>

                            <!-- Navigation Arrows -->
                            <template v-if="images.length > 1">
                                <button
                                    class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-black/50 p-2 text-white transition-colors hover:bg-black/70"
                                    @click="prevImage"
                                >
                                    <ChevronLeft class="h-5 w-5" />
                                </button>
                                <button
                                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-black/50 p-2 text-white transition-colors hover:bg-black/70"
                                    @click="nextImage"
                                >
                                    <ChevronRight class="h-5 w-5" />
                                </button>
                                <!-- Image Counter -->
                                <div class="absolute bottom-3 right-3 rounded-full bg-black/60 px-3 py-1 text-xs text-white">
                                    {{ selectedImageIndex + 1 }} / {{ images.length }}
                                </div>
                            </template>
                        </div>

                        <!-- Thumbnails -->
                        <div v-if="images.length > 1" class="flex gap-2 overflow-x-auto p-3">
                            <button
                                v-for="(media, index) in images"
                                :key="media.id"
                                class="shrink-0 overflow-hidden rounded-md border-2 transition-colors"
                                :class="index === selectedImageIndex ? 'border-emerald-600' : 'border-transparent opacity-60 hover:opacity-100'"
                                @click="selectedImageIndex = index"
                            >
                                <img
                                    :src="media.preview_url || media.original_url"
                                    :alt="`Foto ${index + 1}`"
                                    class="h-16 w-20 object-cover"
                                    loading="lazy"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- Property Info -->
                    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-wrap items-start gap-2">
                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="property.listing_type === 'dijual' ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white'"
                            >
                                {{ listingTypeLabel[property.listing_type] ?? property.listing_type }}
                            </span>
                            <span class="rounded-full border border-neutral-300 bg-white px-2.5 py-1 text-xs font-medium text-neutral-700">
                                {{ propertyTypeLabel[property.property_type] ?? property.property_type }}
                            </span>
                            <span class="rounded-full border border-neutral-300 bg-white px-2.5 py-1 text-xs font-medium text-neutral-700">
                                {{ statusLabel[property.status] ?? property.status }}
                            </span>
                        </div>
                        <h1 class="mt-3 text-2xl font-bold text-neutral-800 sm:text-3xl">{{ property.title }}</h1>
                        <p class="mt-2 text-2xl font-bold text-emerald-600">
                            {{ formatFullCurrency(property.price) }}
                        </p>
                        <p v-if="property.listing_type === 'disewakan'" class="text-sm text-neutral-500">per tahun</p>
                    </div>

                    <!-- Specs Grid -->
                    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                        <div class="border-b border-neutral-200 px-6 py-4">
                            <h2 class="text-lg font-semibold text-neutral-800">Spesifikasi</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                                <div class="flex items-center gap-3 rounded-lg border border-neutral-200 p-3">
                                    <Ruler class="h-5 w-5 shrink-0 text-emerald-600" />
                                    <div>
                                        <p class="text-xs text-neutral-500">Luas Tanah</p>
                                        <p class="text-sm font-semibold text-neutral-800">{{ property.land_area }} m&sup2;</p>
                                    </div>
                                </div>
                                <div v-if="property.building_area" class="flex items-center gap-3 rounded-lg border border-neutral-200 p-3">
                                    <Home class="h-5 w-5 shrink-0 text-emerald-600" />
                                    <div>
                                        <p class="text-xs text-neutral-500">Luas Bangunan</p>
                                        <p class="text-sm font-semibold text-neutral-800">{{ property.building_area }} m&sup2;</p>
                                    </div>
                                </div>
                                <div v-if="property.bedrooms" class="flex items-center gap-3 rounded-lg border border-neutral-200 p-3">
                                    <BedDouble class="h-5 w-5 shrink-0 text-emerald-600" />
                                    <div>
                                        <p class="text-xs text-neutral-500">Kamar Tidur</p>
                                        <p class="text-sm font-semibold text-neutral-800">{{ property.bedrooms }}</p>
                                    </div>
                                </div>
                                <div v-if="property.bathrooms" class="flex items-center gap-3 rounded-lg border border-neutral-200 p-3">
                                    <Bath class="h-5 w-5 shrink-0 text-emerald-600" />
                                    <div>
                                        <p class="text-xs text-neutral-500">Kamar Mandi</p>
                                        <p class="text-sm font-semibold text-neutral-800">{{ property.bathrooms }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 rounded-lg border border-neutral-200 p-3">
                                    <Layers class="h-5 w-5 shrink-0 text-emerald-600" />
                                    <div>
                                        <p class="text-xs text-neutral-500">Lantai</p>
                                        <p class="text-sm font-semibold text-neutral-800">{{ property.floors }}</p>
                                    </div>
                                </div>
                                <div v-if="property.parking_spaces" class="flex items-center gap-3 rounded-lg border border-neutral-200 p-3">
                                    <Car class="h-5 w-5 shrink-0 text-emerald-600" />
                                    <div>
                                        <p class="text-xs text-neutral-500">Parkir</p>
                                        <p class="text-sm font-semibold text-neutral-800">{{ property.parking_spaces }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 rounded-lg border border-neutral-200 p-3">
                                    <Sofa class="h-5 w-5 shrink-0 text-emerald-600" />
                                    <div>
                                        <p class="text-xs text-neutral-500">Furnishing</p>
                                        <p class="text-sm font-semibold text-neutral-800">{{ furnishingLabel[property.furnishing] ?? property.furnishing }}</p>
                                    </div>
                                </div>
                                <div v-if="property.facing_direction" class="flex items-center gap-3 rounded-lg border border-neutral-200 p-3">
                                    <Compass class="h-5 w-5 shrink-0 text-emerald-600" />
                                    <div>
                                        <p class="text-xs text-neutral-500">Hadap</p>
                                        <p class="text-sm font-semibold text-neutral-800">{{ property.facing_direction }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                        <div class="border-b border-neutral-200 px-6 py-4">
                            <h2 class="text-lg font-semibold text-neutral-800">Deskripsi</h2>
                        </div>
                        <div class="p-6">
                            <p class="whitespace-pre-line text-sm leading-relaxed text-neutral-700">{{ property.description }}</p>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                        <div class="border-b border-neutral-200 px-6 py-4">
                            <h2 class="text-lg font-semibold text-neutral-800">Lokasi</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-start gap-3">
                                <MapPin class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
                                <div>
                                    <p class="text-sm font-medium text-neutral-800">{{ property.address }}</p>
                                    <p class="mt-1 text-sm text-neutral-500">
                                        {{ property.subdistrict ? property.subdistrict + ', ' : '' }}{{ property.district }}, {{ property.city }}, {{ property.province }}
                                        {{ property.postal_code ? `(${property.postal_code})` : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate -->
                    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                        <div class="border-b border-neutral-200 px-6 py-4">
                            <h2 class="text-lg font-semibold text-neutral-800">Sertifikat</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-3">
                                <FileText class="h-5 w-5 shrink-0 text-emerald-600" />
                                <div>
                                    <p class="text-sm font-medium text-neutral-800">
                                        {{ certificateTypeLabel[property.certificate_type] ?? property.certificate_type }}
                                    </p>
                                    <p class="text-xs text-neutral-500">Tipe Sertifikat</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div v-if="property.features && property.features.length > 0" class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                        <div class="border-b border-neutral-200 px-6 py-4">
                            <h2 class="text-lg font-semibold text-neutral-800">Fitur</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2">
                                <span v-for="feature in property.features" :key="feature.id" class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                    {{ feature.feature_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (1 col) -->
                <div class="space-y-6">
                    <!-- Seller Info -->
                    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                        <div class="border-b border-neutral-200 px-6 py-4">
                            <h2 class="text-lg font-semibold text-neutral-800">Informasi Penjual</h2>
                        </div>
                        <div class="p-6">
                            <div v-if="property.seller" class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50">
                                    <User class="h-6 w-6 text-emerald-600" />
                                </div>
                                <div>
                                    <p class="font-semibold text-neutral-800">{{ property.seller.name }}</p>
                                    <p class="text-xs text-neutral-500">Penjual</p>
                                </div>
                            </div>
                            <p v-else class="text-sm text-neutral-500">Informasi penjual tidak tersedia</p>
                        </div>
                    </div>

                    <!-- Inquiry Form -->
                    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                        <div class="border-b border-neutral-200 px-6 py-4">
                            <h2 class="text-lg font-semibold text-neutral-800">Kirim Pertanyaan</h2>
                            <p class="mt-1 text-sm text-neutral-500">Hubungi penjual untuk informasi lebih lanjut</p>
                        </div>
                        <div class="p-6">
                            <!-- Success State -->
                            <div v-if="inquirySubmitted" class="py-4 text-center">
                                <CheckCircle class="mx-auto mb-3 h-10 w-10 text-emerald-500" />
                                <h3 class="font-semibold text-neutral-800">Pertanyaan Terkirim!</h3>
                                <p class="mt-1 text-sm text-neutral-500">
                                    Kami akan segera menghubungi Anda melalui metode kontak yang dipilih.
                                </p>
                                <button
                                    class="mt-4 rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                                    @click="inquirySubmitted = false"
                                >
                                    Kirim Pertanyaan Lain
                                </button>
                            </div>

                            <!-- Form -->
                            <form v-else @submit.prevent="submitInquiry" class="space-y-4">
                                <div>
                                    <label for="inquiry-name" class="text-sm font-medium text-neutral-700">Nama Lengkap</label>
                                    <input
                                        id="inquiry-name"
                                        v-model="inquiryForm.name"
                                        placeholder="Nama Anda"
                                        :class="inputClass"
                                        class="mt-1"
                                        required
                                    />
                                    <p v-if="inquiryForm.errors.name" class="mt-1 text-xs text-red-500">
                                        {{ inquiryForm.errors.name }}
                                    </p>
                                </div>

                                <div>
                                    <label for="inquiry-email" class="text-sm font-medium text-neutral-700">Email</label>
                                    <input
                                        id="inquiry-email"
                                        v-model="inquiryForm.email"
                                        type="email"
                                        placeholder="email@contoh.com"
                                        :class="inputClass"
                                        class="mt-1"
                                        required
                                    />
                                    <p v-if="inquiryForm.errors.email" class="mt-1 text-xs text-red-500">
                                        {{ inquiryForm.errors.email }}
                                    </p>
                                </div>

                                <div>
                                    <label for="inquiry-phone" class="text-sm font-medium text-neutral-700">No. Telepon / WhatsApp</label>
                                    <input
                                        id="inquiry-phone"
                                        v-model="inquiryForm.phone"
                                        type="tel"
                                        placeholder="08xxxxxxxxxx"
                                        :class="inputClass"
                                        class="mt-1"
                                    />
                                    <p v-if="inquiryForm.errors.phone" class="mt-1 text-xs text-red-500">
                                        {{ inquiryForm.errors.phone }}
                                    </p>
                                </div>

                                <div>
                                    <label for="inquiry-message" class="text-sm font-medium text-neutral-700">Pesan</label>
                                    <textarea
                                        id="inquiry-message"
                                        v-model="inquiryForm.message"
                                        rows="4"
                                        class="mt-1 w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                        placeholder="Tulis pesan Anda..."
                                        required
                                    />
                                    <p v-if="inquiryForm.errors.message" class="mt-1 text-xs text-red-500">
                                        {{ inquiryForm.errors.message }}
                                    </p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-neutral-700">Hubungi Saya Via</label>
                                    <select
                                        v-model="inquiryForm.preferred_contact_method"
                                        :class="selectClass"
                                    >
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="phone">Telepon</option>
                                        <option value="email">Email</option>
                                    </select>
                                    <p v-if="inquiryForm.errors.preferred_contact_method" class="mt-1 text-xs text-red-500">
                                        {{ inquiryForm.errors.preferred_contact_method }}
                                    </p>
                                </div>

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-700 disabled:opacity-50"
                                    :disabled="inquiryForm.processing"
                                >
                                    <Send class="mr-2 h-4 w-4" />
                                    {{ inquiryForm.processing ? 'Mengirim...' : 'Kirim Pertanyaan' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Properties -->
            <section v-if="relatedProperties.length > 0" class="mt-12">
                <hr class="mb-8 border-neutral-200" />
                <h2 class="mb-6 text-xl font-bold text-neutral-800">Properti Serupa</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <Link
                        v-for="related in relatedProperties"
                        :key="related.id"
                        :href="`/properti/${related.slug || related.id}`"
                        class="group"
                    >
                        <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm transition-shadow group-hover:shadow-md">
                            <div class="relative aspect-4/3 overflow-hidden bg-neutral-100">
                                <img
                                    v-if="related.media && related.media.length > 0"
                                    :src="related.media[0].original_url"
                                    :alt="related.title"
                                    class="h-full w-full object-cover transition-transform group-hover:scale-105"
                                    loading="lazy"
                                />
                                <div v-else class="flex h-full items-center justify-center">
                                    <Building2 class="h-10 w-10 text-neutral-300" />
                                </div>
                                <span
                                    class="absolute left-2 top-2 rounded-full px-2.5 py-1 text-xs font-semibold"
                                    :class="related.listing_type === 'dijual' ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white'"
                                >
                                    {{ listingTypeLabel[related.listing_type] ?? related.listing_type }}
                                </span>
                            </div>
                            <div class="p-3">
                                <p class="text-sm font-bold text-emerald-600">{{ formatCurrency(related.price) }}</p>
                                <h3 class="mt-1 line-clamp-1 text-sm font-semibold text-neutral-800 group-hover:underline">
                                    {{ related.title }}
                                </h3>
                                <p class="mt-1 line-clamp-1 text-xs text-neutral-500">
                                    {{ related.district }}, {{ related.city }}
                                </p>
                                <div class="mt-2 flex items-center gap-2 text-xs text-neutral-500">
                                    <span v-if="related.land_area" class="flex items-center gap-1">
                                        <Ruler class="h-3 w-3" />
                                        {{ related.land_area }} m&sup2;
                                    </span>
                                    <span v-if="related.bedrooms" class="flex items-center gap-1">
                                        <BedDouble class="h-3 w-3" />
                                        {{ related.bedrooms }}
                                    </span>
                                    <span v-if="related.bathrooms" class="flex items-center gap-1">
                                        <Bath class="h-3 w-3" />
                                        {{ related.bathrooms }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </PublicLayout>
</template>
