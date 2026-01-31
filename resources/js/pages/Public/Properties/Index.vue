<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { type Property, type Paginated } from '@/types';
import {
    Building2,
    Search,
    BedDouble,
    Bath,
    Ruler,
    Home,
    SlidersHorizontal,
    X,
    ChevronDown,
    ChevronUp,
    ArrowUpDown,
    RotateCcw,
} from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ref, watch, computed } from 'vue';

type SelectOption = { value: string; name: string };

type Props = {
    properties: Paginated<Property>;
    filters: Record<string, string>;
    propertyTypes: SelectOption[];
    listingTypes: SelectOption[];
    certificateTypes: SelectOption[];
    furnishingTypes: SelectOption[];
    cities: SelectOption[];
};

const props = defineProps<Props>();

const selectClass = 'border-neutral-300 bg-white placeholder:text-neutral-400 focus:ring-emerald-500 focus:border-emerald-500 mt-1 flex h-9 w-full rounded-md border px-3 py-1 text-sm shadow-sm transition-colors focus:outline-none focus:ring-1';

const filters = ref<Record<string, string>>({
    search: props.filters.search ?? '',
    property_type: props.filters.property_type ?? '',
    listing_type: props.filters.listing_type ?? '',
    city: props.filters.city ?? '',
    min_price: props.filters.min_price ?? '',
    max_price: props.filters.max_price ?? '',
    min_land_area: props.filters.min_land_area ?? '',
    max_land_area: props.filters.max_land_area ?? '',
    min_building_area: props.filters.min_building_area ?? '',
    max_building_area: props.filters.max_building_area ?? '',
    bedrooms: props.filters.bedrooms ?? '',
    bathrooms: props.filters.bathrooms ?? '',
    certificate_type: props.filters.certificate_type ?? '',
    furnishing: props.filters.furnishing ?? '',
    sort: props.filters.sort ?? '',
});

const showMobileFilters = ref(false);
const showAdvancedFilters = ref(false);

const hasActiveFilters = computed(() => {
    return Object.entries(filters.value).some(([key, val]) => key !== 'search' && key !== 'sort' && val !== '');
});

const activeFilterCount = computed(() => {
    return Object.entries(filters.value).filter(([key, val]) => key !== 'search' && key !== 'sort' && val !== '').length;
});

function applyFilters() {
    const params: Record<string, string> = {};
    for (const [key, val] of Object.entries(filters.value)) {
        if (val) params[key] = val;
    }
    router.get('/properti', params, {
        preserveState: true,
        replace: true,
    });
}

function clearFilters() {
    for (const key of Object.keys(filters.value)) {
        filters.value[key] = '';
    }
    applyFilters();
}

let searchTimeout: ReturnType<typeof setTimeout>;
watch(() => filters.value.search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 400);
});

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

const sortOptions = [
    { value: '', name: 'Terbaru' },
    { value: 'price_asc', name: 'Harga Terendah' },
    { value: 'price_desc', name: 'Harga Tertinggi' },
    { value: 'oldest', name: 'Terlama' },
    { value: 'land_area', name: 'Luas Tanah Terbesar' },
];

const pricePresets = [
    { label: '< 500 Jt', min: '', max: '500000000' },
    { label: '500 Jt - 1 M', min: '500000000', max: '1000000000' },
    { label: '1 - 2 M', min: '1000000000', max: '2000000000' },
    { label: '2 - 5 M', min: '2000000000', max: '5000000000' },
    { label: '> 5 M', min: '5000000000', max: '' },
];

function applyPricePreset(preset: { min: string; max: string }) {
    filters.value.min_price = preset.min;
    filters.value.max_price = preset.max;
    applyFilters();
}
</script>

<template>
    <Head title="Cari Properti - PropertiKu" />

    <PublicLayout>
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <!-- Page Title & Search Bar -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-neutral-800 sm:text-3xl">Cari Properti</h1>
                <p class="mt-1 text-sm text-neutral-500">
                    Temukan {{ properties.total }} properti yang tersedia
                </p>
            </div>

            <!-- Search Bar + Quick Filters -->
            <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm">
                <!-- Row 1: Search + Sort -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" />
                        <input
                            v-model="filters.search"
                            type="search"
                            placeholder="Cari berdasarkan nama, alamat, kecamatan, atau kota..."
                            class="w-full rounded-md border border-neutral-300 bg-white py-2 pl-10 pr-3 text-sm shadow-sm placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <ArrowUpDown class="h-4 w-4 flex-shrink-0 text-neutral-400" />
                        <select
                            v-model="filters.sort"
                            :class="selectClass"
                            class="w-44"
                            @change="applyFilters"
                        >
                            <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">
                                {{ opt.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Row 2: Quick Filter Chips -->
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <select
                        v-model="filters.property_type"
                        :class="selectClass"
                        class="w-auto min-w-[120px]"
                        @change="applyFilters"
                    >
                        <option value="">Semua Tipe</option>
                        <option v-for="opt in propertyTypes" :key="opt.value" :value="opt.value">
                            {{ opt.name }}
                        </option>
                    </select>

                    <select
                        v-model="filters.listing_type"
                        :class="selectClass"
                        class="w-auto min-w-[140px]"
                        @change="applyFilters"
                    >
                        <option value="">Dijual & Disewakan</option>
                        <option v-for="opt in listingTypes" :key="opt.value" :value="opt.value">
                            {{ opt.name }}
                        </option>
                    </select>

                    <select
                        v-model="filters.city"
                        :class="selectClass"
                        class="w-auto min-w-[150px]"
                        @change="applyFilters"
                    >
                        <option value="">Semua Kota</option>
                        <option v-for="opt in cities" :key="opt.value" :value="opt.value">
                            {{ opt.name }}
                        </option>
                    </select>

                    <select
                        v-model="filters.bedrooms"
                        :class="selectClass"
                        class="w-auto min-w-[100px]"
                        @change="applyFilters"
                    >
                        <option value="">KT</option>
                        <option value="1">1+ KT</option>
                        <option value="2">2+ KT</option>
                        <option value="3">3+ KT</option>
                        <option value="4">4+ KT</option>
                        <option value="5">5+ KT</option>
                    </select>

                    <button
                        class="inline-flex items-center gap-1 rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-neutral-50"
                        @click="showAdvancedFilters = !showAdvancedFilters"
                    >
                        <SlidersHorizontal class="h-4 w-4" />
                        Filter Lanjutan
                        <span v-if="activeFilterCount > 0" class="ml-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-emerald-600 px-1.5 text-xs text-white">
                            {{ activeFilterCount }}
                        </span>
                        <ChevronUp v-if="showAdvancedFilters" class="ml-1 h-3 w-3" />
                        <ChevronDown v-else class="ml-1 h-3 w-3" />
                    </button>

                    <button
                        v-if="hasActiveFilters"
                        class="inline-flex items-center gap-1 rounded-md px-3 py-2 text-sm text-neutral-500 transition-colors hover:bg-neutral-100 hover:text-neutral-700"
                        @click="clearFilters"
                    >
                        <RotateCcw class="h-3 w-3" />
                        Reset
                    </button>
                </div>

                <!-- Advanced Filters Panel -->
                <div v-if="showAdvancedFilters" class="mt-4 border-t border-neutral-200 pt-4">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Price Range -->
                        <div class="sm:col-span-2">
                            <label class="text-xs font-medium text-neutral-700">Rentang Harga</label>
                            <div class="mt-1.5 flex flex-wrap gap-1.5">
                                <button
                                    v-for="preset in pricePresets"
                                    :key="preset.label"
                                    class="rounded-full border px-3 py-1 text-xs transition-colors"
                                    :class="filters.min_price === preset.min && filters.max_price === preset.max ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-neutral-300 bg-white text-neutral-700 hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-700'"
                                    @click="applyPricePreset(preset)"
                                >
                                    {{ preset.label }}
                                </button>
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                <input
                                    v-model="filters.min_price"
                                    type="number"
                                    placeholder="Harga Min"
                                    class="w-full rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs shadow-sm placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                    @change="applyFilters"
                                />
                                <span class="text-xs text-neutral-400">-</span>
                                <input
                                    v-model="filters.max_price"
                                    type="number"
                                    placeholder="Harga Maks"
                                    class="w-full rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs shadow-sm placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                    @change="applyFilters"
                                />
                            </div>
                        </div>

                        <!-- Land Area -->
                        <div>
                            <label class="text-xs font-medium text-neutral-700">Luas Tanah (m²)</label>
                            <div class="mt-1.5 flex items-center gap-2">
                                <input
                                    v-model="filters.min_land_area"
                                    type="number"
                                    placeholder="Min"
                                    class="w-full rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs shadow-sm placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                    @change="applyFilters"
                                />
                                <span class="text-xs text-neutral-400">-</span>
                                <input
                                    v-model="filters.max_land_area"
                                    type="number"
                                    placeholder="Maks"
                                    class="w-full rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs shadow-sm placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                    @change="applyFilters"
                                />
                            </div>
                        </div>

                        <!-- Building Area -->
                        <div>
                            <label class="text-xs font-medium text-neutral-700">Luas Bangunan (m²)</label>
                            <div class="mt-1.5 flex items-center gap-2">
                                <input
                                    v-model="filters.min_building_area"
                                    type="number"
                                    placeholder="Min"
                                    class="w-full rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs shadow-sm placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                    @change="applyFilters"
                                />
                                <span class="text-xs text-neutral-400">-</span>
                                <input
                                    v-model="filters.max_building_area"
                                    type="number"
                                    placeholder="Maks"
                                    class="w-full rounded-md border border-neutral-300 bg-white px-3 py-1.5 text-xs shadow-sm placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                    @change="applyFilters"
                                />
                            </div>
                        </div>

                        <!-- Bathrooms -->
                        <div>
                            <label class="text-xs font-medium text-neutral-700">Kamar Mandi</label>
                            <select
                                v-model="filters.bathrooms"
                                :class="selectClass"
                                @change="applyFilters"
                            >
                                <option value="">Semua</option>
                                <option value="1">1+</option>
                                <option value="2">2+</option>
                                <option value="3">3+</option>
                                <option value="4">4+</option>
                            </select>
                        </div>

                        <!-- Certificate Type -->
                        <div>
                            <label class="text-xs font-medium text-neutral-700">Sertifikat</label>
                            <select
                                v-model="filters.certificate_type"
                                :class="selectClass"
                                @change="applyFilters"
                            >
                                <option value="">Semua Sertifikat</option>
                                <option v-for="opt in certificateTypes" :key="opt.value" :value="opt.value">
                                    {{ opt.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Furnishing -->
                        <div>
                            <label class="text-xs font-medium text-neutral-700">Perabotan</label>
                            <select
                                v-model="filters.furnishing"
                                :class="selectClass"
                                @change="applyFilters"
                            >
                                <option value="">Semua</option>
                                <option v-for="opt in furnishingTypes" :key="opt.value" :value="opt.value">
                                    {{ opt.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Active Filter Tags -->
                <div v-if="hasActiveFilters" class="mt-3 flex flex-wrap gap-1.5">
                    <span
                        v-if="filters.property_type"
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 pr-1 text-xs font-medium text-emerald-700"
                    >
                        {{ propertyTypes.find(t => t.value === filters.property_type)?.name ?? filters.property_type }}
                        <button class="ml-1 rounded-full p-0.5 hover:bg-emerald-200" @click="filters.property_type = ''; applyFilters()">
                            <X class="h-3 w-3" />
                        </button>
                    </span>
                    <span
                        v-if="filters.listing_type"
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 pr-1 text-xs font-medium text-emerald-700"
                    >
                        {{ listingTypes.find(t => t.value === filters.listing_type)?.name ?? filters.listing_type }}
                        <button class="ml-1 rounded-full p-0.5 hover:bg-emerald-200" @click="filters.listing_type = ''; applyFilters()">
                            <X class="h-3 w-3" />
                        </button>
                    </span>
                    <span
                        v-if="filters.city"
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 pr-1 text-xs font-medium text-emerald-700"
                    >
                        {{ filters.city }}
                        <button class="ml-1 rounded-full p-0.5 hover:bg-emerald-200" @click="filters.city = ''; applyFilters()">
                            <X class="h-3 w-3" />
                        </button>
                    </span>
                    <span
                        v-if="filters.min_price || filters.max_price"
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 pr-1 text-xs font-medium text-emerald-700"
                    >
                        Harga: {{ filters.min_price ? formatCurrency(Number(filters.min_price)) : '0' }} - {{ filters.max_price ? formatCurrency(Number(filters.max_price)) : '∞' }}
                        <button class="ml-1 rounded-full p-0.5 hover:bg-emerald-200" @click="filters.min_price = ''; filters.max_price = ''; applyFilters()">
                            <X class="h-3 w-3" />
                        </button>
                    </span>
                    <span
                        v-if="filters.bedrooms"
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 pr-1 text-xs font-medium text-emerald-700"
                    >
                        {{ filters.bedrooms }}+ KT
                        <button class="ml-1 rounded-full p-0.5 hover:bg-emerald-200" @click="filters.bedrooms = ''; applyFilters()">
                            <X class="h-3 w-3" />
                        </button>
                    </span>
                    <span
                        v-if="filters.bathrooms"
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 pr-1 text-xs font-medium text-emerald-700"
                    >
                        {{ filters.bathrooms }}+ KM
                        <button class="ml-1 rounded-full p-0.5 hover:bg-emerald-200" @click="filters.bathrooms = ''; applyFilters()">
                            <X class="h-3 w-3" />
                        </button>
                    </span>
                    <span
                        v-if="filters.min_land_area || filters.max_land_area"
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 pr-1 text-xs font-medium text-emerald-700"
                    >
                        LT: {{ filters.min_land_area || '0' }} - {{ filters.max_land_area || '∞' }} m²
                        <button class="ml-1 rounded-full p-0.5 hover:bg-emerald-200" @click="filters.min_land_area = ''; filters.max_land_area = ''; applyFilters()">
                            <X class="h-3 w-3" />
                        </button>
                    </span>
                    <span
                        v-if="filters.min_building_area || filters.max_building_area"
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 pr-1 text-xs font-medium text-emerald-700"
                    >
                        LB: {{ filters.min_building_area || '0' }} - {{ filters.max_building_area || '∞' }} m²
                        <button class="ml-1 rounded-full p-0.5 hover:bg-emerald-200" @click="filters.min_building_area = ''; filters.max_building_area = ''; applyFilters()">
                            <X class="h-3 w-3" />
                        </button>
                    </span>
                    <span
                        v-if="filters.certificate_type"
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 pr-1 text-xs font-medium text-emerald-700"
                    >
                        {{ certificateTypes.find(t => t.value === filters.certificate_type)?.name ?? filters.certificate_type }}
                        <button class="ml-1 rounded-full p-0.5 hover:bg-emerald-200" @click="filters.certificate_type = ''; applyFilters()">
                            <X class="h-3 w-3" />
                        </button>
                    </span>
                    <span
                        v-if="filters.furnishing"
                        class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 pr-1 text-xs font-medium text-emerald-700"
                    >
                        {{ furnishingTypes.find(t => t.value === filters.furnishing)?.name ?? filters.furnishing }}
                        <button class="ml-1 rounded-full p-0.5 hover:bg-emerald-200" @click="filters.furnishing = ''; applyFilters()">
                            <X class="h-3 w-3" />
                        </button>
                    </span>
                </div>
            </div>

            <!-- Results Info -->
            <div class="mb-4 flex items-center justify-between text-sm text-neutral-500">
                <span v-if="properties.from && properties.to">
                    Menampilkan {{ properties.from }}-{{ properties.to }} dari {{ properties.total }} properti
                </span>
                <span v-else>
                    Tidak ada properti ditemukan
                </span>
            </div>

            <!-- Empty State -->
            <div v-if="properties.data.length === 0" class="py-16 text-center">
                <Building2 class="mx-auto mb-4 h-16 w-16 text-neutral-300" />
                <h3 class="text-lg font-semibold text-neutral-800">Tidak Ada Properti</h3>
                <p class="mt-1 text-sm text-neutral-500">
                    Coba ubah filter pencarian Anda untuk menemukan properti yang sesuai.
                </p>
                <button
                    class="mt-4 inline-flex items-center rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-medium text-neutral-700 shadow-sm hover:bg-neutral-50"
                    @click="clearFilters"
                >
                    <RotateCcw class="mr-2 h-4 w-4" />
                    Reset Filter
                </button>
            </div>

            <!-- Property Grid -->
            <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <Link
                    v-for="property in properties.data"
                    :key="property.id"
                    :href="`/properti/${property.slug || property.id}`"
                    class="group"
                >
                    <div class="h-full overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm transition-shadow group-hover:shadow-md">
                        <!-- Image -->
                        <div class="relative aspect-[4/3] overflow-hidden bg-neutral-100">
                            <img
                                v-if="property.media && property.media.length > 0"
                                :src="property.media[0].original_url"
                                :alt="property.title"
                                class="h-full w-full object-cover transition-transform group-hover:scale-105"
                                loading="lazy"
                            />
                            <div v-else class="flex h-full items-center justify-center">
                                <Building2 class="h-12 w-12 text-neutral-300" />
                            </div>
                            <!-- Listing Type Badge -->
                            <span
                                class="absolute left-3 top-3 rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="property.listing_type === 'dijual' ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white'"
                            >
                                {{ listingTypeLabel[property.listing_type] ?? property.listing_type }}
                            </span>
                            <!-- Property Type Badge -->
                            <span class="absolute right-3 top-3 rounded-full border border-white/30 bg-white/80 px-2.5 py-1 text-xs font-medium text-neutral-700 backdrop-blur-sm">
                                {{ propertyTypeLabel[property.property_type] ?? property.property_type }}
                            </span>
                        </div>

                        <div class="p-4">
                            <!-- Price -->
                            <p class="text-lg font-bold text-emerald-600">
                                {{ formatCurrency(property.price) }}
                            </p>

                            <!-- Title -->
                            <h3 class="mt-1 line-clamp-1 text-sm font-semibold text-neutral-800 group-hover:underline">
                                {{ property.title }}
                            </h3>

                            <!-- Location -->
                            <p class="mt-1 line-clamp-1 text-xs text-neutral-500">
                                {{ property.district }}, {{ property.city }}
                            </p>

                            <!-- Specs -->
                            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-neutral-500">
                                <span v-if="property.land_area" class="flex items-center gap-1">
                                    <Ruler class="h-3.5 w-3.5" />
                                    {{ property.land_area }} m&sup2;
                                </span>
                                <span v-if="property.building_area" class="flex items-center gap-1">
                                    <Home class="h-3.5 w-3.5" />
                                    {{ property.building_area }} m&sup2;
                                </span>
                                <span v-if="property.bedrooms" class="flex items-center gap-1">
                                    <BedDouble class="h-3.5 w-3.5" />
                                    {{ property.bedrooms }}
                                </span>
                                <span v-if="property.bathrooms" class="flex items-center gap-1">
                                    <Bath class="h-3.5 w-3.5" />
                                    {{ property.bathrooms }}
                                </span>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="properties.last_page > 1" class="mt-8 flex items-center justify-center gap-2">
                <template v-for="link in properties.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="inline-flex items-center rounded-md border px-3 py-1.5 text-sm font-medium transition-colors"
                        :class="link.active ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-50'"
                        v-html="link.label"
                        preserve-state
                    />
                    <span
                        v-else
                        class="inline-flex items-center rounded-md border border-neutral-200 bg-neutral-50 px-3 py-1.5 text-sm text-neutral-400"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </PublicLayout>
</template>
