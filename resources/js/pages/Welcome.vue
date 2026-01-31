<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Home,
    Building,
    Landmark,
    Store,
    Warehouse,
    Briefcase,
    Shield,
    Handshake,
    FileCheck,
    Eye,
    ChevronRight,
    MapPin,
    ArrowRight,
    BadgeCheck,
    Play,
    Pause,
} from 'lucide-vue-next';
import { ref, onMounted, onUnmounted } from 'vue';

withDefaults(
    defineProps<{
        canRegister?: boolean;
    }>(),
    {
        canRegister: true,
    },
);

// Video hero
const videos = ['/videos/video-1.mp4', '/videos/video-2.mp4', '/videos/video-3.mp4'];
const currentVideoIndex = ref(0);
const videoRef = ref<HTMLVideoElement | null>(null);
const isVideoPlaying = ref(true);

function onVideoEnded() {
    currentVideoIndex.value = (currentVideoIndex.value + 1) % videos.length;
}

function toggleVideo() {
    if (!videoRef.value) return;
    if (videoRef.value.paused) {
        videoRef.value.play();
        isVideoPlaying.value = true;
    } else {
        videoRef.value.pause();
        isVideoPlaying.value = false;
    }
}

// Property preference form
const propertyTypes = [
    { value: 'rumah', label: 'Rumah', icon: Home, description: 'Rumah tinggal keluarga' },
    { value: 'apartemen', label: 'Apartemen', icon: Building, description: 'Unit apartemen modern' },
    { value: 'tanah', label: 'Tanah', icon: Landmark, description: 'Kavling siap bangun' },
    { value: 'ruko', label: 'Ruko', icon: Store, description: 'Rumah toko strategis' },
    { value: 'gudang', label: 'Gudang', icon: Warehouse, description: 'Gudang & penyimpanan' },
    { value: 'kantor', label: 'Kantor', icon: Briefcase, description: 'Ruang perkantoran' },
];

const selectedTypes = ref<string[]>([]);
const selectedListing = ref<'dijual' | 'disewakan'>('dijual');
const budgetMin = ref('');
const budgetMax = ref('');
const preferredLocation = ref('');

function togglePropertyType(value: string) {
    const idx = selectedTypes.value.indexOf(value);
    if (idx >= 0) {
        selectedTypes.value.splice(idx, 1);
    } else {
        selectedTypes.value.push(value);
    }
}

function searchProperties() {
    const params = new URLSearchParams();
    if (selectedTypes.value.length) params.set('property_type', selectedTypes.value.join(','));
    if (selectedListing.value) params.set('listing_type', selectedListing.value);
    if (budgetMin.value) params.set('min_price', budgetMin.value);
    if (budgetMax.value) params.set('max_price', budgetMax.value);
    if (preferredLocation.value) params.set('city', preferredLocation.value);
    window.location.href = `/properti?${params.toString()}`;
}

// Animated counters
const statsVisible = ref(false);
const statsRef = ref<HTMLElement | null>(null);

const stats = [
    { label: 'Properti Terdaftar', value: 500, suffix: '+' },
    { label: 'Transaksi Berhasil', value: 120, suffix: '+' },
    { label: 'Klien Puas', value: 350, suffix: '+' },
    { label: 'Notaris Partner', value: 15, suffix: '+' },
];

const animatedValues = ref(stats.map(() => 0));
let observer: IntersectionObserver | null = null;

function animateCounters() {
    stats.forEach((stat, index) => {
        const duration = 2000;
        const steps = 60;
        const increment = stat.value / steps;
        let current = 0;
        const interval = setInterval(() => {
            current += increment;
            if (current >= stat.value) {
                animatedValues.value[index] = stat.value;
                clearInterval(interval);
            } else {
                animatedValues.value[index] = Math.floor(current);
            }
        }, duration / steps);
    });
}

onMounted(() => {
    observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting && !statsVisible.value) {
                statsVisible.value = true;
                animateCounters();
            }
        },
        { threshold: 0.3 },
    );
    if (statsRef.value) {
        observer.observe(statsRef.value);
    }
});

onUnmounted(() => {
    observer?.disconnect();
});

// Process steps
const steps = [
    {
        number: '01',
        title: 'Tentukan Preferensi',
        description: 'Beritahu kami jenis properti impian Anda — tipe, lokasi, dan anggaran yang sesuai.',
    },
    {
        number: '02',
        title: 'Temukan Properti',
        description: 'Tim kami menyajikan pilihan properti terverifikasi yang sesuai dengan kebutuhan Anda.',
    },
    {
        number: '03',
        title: 'Viewing & Negosiasi',
        description: 'Jadwalkan kunjungan properti dan kami bantu proses negosiasi terbaik.',
    },
    {
        number: '04',
        title: 'Transaksi Aman',
        description: 'Proses transaksi end-to-end dengan notaris terpercaya hingga serah terima sertifikat.',
    },
];

// Budget formatter
function formatBudget(value: string): string {
    const num = parseInt(value.replace(/\D/g, ''));
    if (isNaN(num)) return '';
    return num.toLocaleString('id-ID');
}

function handleBudgetInput(field: 'min' | 'max', event: Event) {
    const input = event.target as HTMLInputElement;
    const raw = input.value.replace(/\D/g, '');
    if (field === 'min') {
        budgetMin.value = raw;
    } else {
        budgetMax.value = raw;
    }
    input.value = raw ? formatBudget(raw) : '';
}

function scrollToSearch() {
    document.getElementById('search')?.scrollIntoView({ behavior: 'smooth' });
}
</script>

<template>
    <Head title="PropertiKu - Fasilitator Properti Terpercaya di Jakarta">
        <meta name="description" content="Platform fasilitator properti terpercaya di Jakarta. Temukan rumah, apartemen, tanah, dan properti lainnya dengan transaksi yang aman dan transparan." />
    </Head>

    <PublicLayout>
        <!-- Hero Section with Video Background -->
        <section class="relative h-[90vh] min-h-[600px] overflow-hidden">
            <!-- Video Background -->
            <div class="absolute inset-0">
                <video
                    ref="videoRef"
                    :key="currentVideoIndex"
                    :src="videos[currentVideoIndex]"
                    autoplay
                    muted
                    playsinline
                    class="h-full w-full object-cover"
                    @ended="onVideoEnded"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/70" />
            </div>

            <!-- Video control -->
            <button
                @click="toggleVideo"
                class="absolute bottom-6 right-6 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white backdrop-blur-sm transition-all hover:bg-white/30"
            >
                <Pause v-if="isVideoPlaying" class="h-4 w-4" />
                <Play v-else class="h-4 w-4" />
            </button>

            <!-- Hero Content -->
            <div class="relative z-10 flex h-full items-center">
                <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl">
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 backdrop-blur-sm">
                            <BadgeCheck class="h-4 w-4 text-emerald-400" />
                            <span class="text-sm font-medium text-white">Fasilitator Properti Terpercaya</span>
                        </div>
                        <h1 class="text-4xl font-bold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                            Temukan Properti
                            <span class="bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">Impian Anda</span>
                            di Jakarta
                        </h1>
                        <p class="mt-4 max-w-xl text-lg leading-relaxed text-white/80 sm:text-xl">
                            Kami membantu Anda dari pencarian properti hingga serah terima sertifikat, dengan transparansi penuh dan keamanan hukum.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            <Button size="lg" class="bg-emerald-600 text-white hover:bg-emerald-700" @click="scrollToSearch">
                                Mulai Cari Properti
                                <ArrowRight class="ml-1 h-4 w-4" />
                            </Button>
                            <Button size="lg" variant="outline" class="border-white/30 bg-white/10 text-white backdrop-blur-sm hover:bg-white/20 hover:text-white" as-child>
                                <Link href="/tentang-kami">Tentang Kami</Link>
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 z-10 -translate-x-1/2 animate-bounce">
                <div class="flex h-8 w-5 items-start justify-center rounded-full border-2 border-white/40 pt-1.5">
                    <div class="h-1.5 w-1 rounded-full bg-white/60" />
                </div>
            </div>
        </section>

        <!-- Property Preference Form Section -->
        <section class="relative bg-white py-16 sm:py-24" id="search">
            <!-- Decorative background -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 right-0 h-80 w-80 rounded-full bg-emerald-50 opacity-50 blur-3xl" />
                <div class="absolute -bottom-40 left-0 h-80 w-80 rounded-full bg-cyan-50 opacity-50 blur-3xl" />
            </div>

            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-neutral-800 sm:text-4xl">
                        Apa yang Anda Cari?
                    </h2>
                    <p class="mt-3 text-lg text-neutral-500">
                        Beritahu kami preferensi Anda dan kami akan mencarikan properti terbaik untuk Anda.
                    </p>
                </div>

                <div class="mx-auto mt-12 max-w-4xl">
                    <!-- Listing type toggle -->
                    <div class="mb-8 flex justify-center">
                        <div class="inline-flex rounded-xl bg-neutral-100 p-1">
                            <button
                                @click="selectedListing = 'dijual'"
                                :class="[
                                    'rounded-lg px-6 py-2.5 text-sm font-semibold transition-all',
                                    selectedListing === 'dijual'
                                        ? 'bg-white text-neutral-800 shadow-sm'
                                        : 'text-neutral-500 hover:text-neutral-700',
                                ]"
                            >
                                Beli Properti
                            </button>
                            <button
                                @click="selectedListing = 'disewakan'"
                                :class="[
                                    'rounded-lg px-6 py-2.5 text-sm font-semibold transition-all',
                                    selectedListing === 'disewakan'
                                        ? 'bg-white text-neutral-800 shadow-sm'
                                        : 'text-neutral-500 hover:text-neutral-700',
                                ]"
                            >
                                Sewa Properti
                            </button>
                        </div>
                    </div>

                    <!-- Property type selection -->
                    <div class="mb-8">
                        <Label class="mb-3 block text-center text-sm font-medium text-neutral-700">Pilih Tipe Properti <span class="text-neutral-400">(bisa lebih dari satu)</span></Label>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                            <button
                                v-for="type in propertyTypes"
                                :key="type.value"
                                @click="togglePropertyType(type.value)"
                                :class="[
                                    'group relative flex flex-col items-center gap-2 rounded-xl border-2 p-4 transition-all hover:shadow-md',
                                    selectedTypes.includes(type.value)
                                        ? 'border-emerald-500 bg-emerald-50 shadow-sm'
                                        : 'border-neutral-200 bg-white hover:border-neutral-300',
                                ]"
                            >
                                <div
                                    :class="[
                                        'flex h-11 w-11 items-center justify-center rounded-lg transition-colors',
                                        selectedTypes.includes(type.value)
                                            ? 'bg-emerald-500 text-white'
                                            : 'bg-neutral-100 text-neutral-500 group-hover:bg-neutral-200',
                                    ]"
                                >
                                    <component :is="type.icon" class="h-5 w-5" />
                                </div>
                                <span
                                    :class="[
                                        'text-sm font-medium',
                                        selectedTypes.includes(type.value) ? 'text-emerald-700' : 'text-neutral-700',
                                    ]"
                                >
                                    {{ type.label }}
                                </span>
                                <!-- Check mark -->
                                <div
                                    v-if="selectedTypes.includes(type.value)"
                                    class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-white"
                                >
                                    <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none"><path d="M10 3L4.5 8.5L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Budget and Location -->
                    <div class="mb-8 grid gap-4 sm:grid-cols-3">
                        <div>
                            <Label class="mb-1.5 block text-sm font-medium text-neutral-700">Budget Minimum</Label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-neutral-400">Rp</span>
                                <Input
                                    type="text"
                                    placeholder="500.000.000"
                                    class="pl-9"
                                    @input="handleBudgetInput('min', $event)"
                                />
                            </div>
                        </div>
                        <div>
                            <Label class="mb-1.5 block text-sm font-medium text-neutral-700">Budget Maksimum</Label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-neutral-400">Rp</span>
                                <Input
                                    type="text"
                                    placeholder="2.000.000.000"
                                    class="pl-9"
                                    @input="handleBudgetInput('max', $event)"
                                />
                            </div>
                        </div>
                        <div>
                            <Label class="mb-1.5 block text-sm font-medium text-neutral-700">Lokasi Preferensi</Label>
                            <div class="relative">
                                <MapPin class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" />
                                <Input
                                    v-model="preferredLocation"
                                    type="text"
                                    placeholder="Jakarta Selatan"
                                    class="pl-9"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Search button -->
                    <div class="text-center">
                        <Button
                            size="lg"
                            class="bg-emerald-600 px-10 text-white hover:bg-emerald-700"
                            @click="searchProperties"
                        >
                            Cari Properti
                            <ChevronRight class="ml-1 h-4 w-4" />
                        </Button>
                        <p class="mt-3 text-sm text-neutral-400">
                            Kami hanya menampilkan properti yang sesuai dengan kebutuhan Anda
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="bg-neutral-50 py-16 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-neutral-800 sm:text-4xl">
                        Bagaimana Cara Kerjanya?
                    </h2>
                    <p class="mt-3 text-lg text-neutral-500">
                        Empat langkah mudah menuju properti impian Anda
                    </p>
                </div>

                <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="step in steps" :key="step.number" class="relative">
                        <div class="flex flex-col items-center text-center">
                            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-600 text-lg font-bold text-white shadow-lg shadow-emerald-600/20">
                                {{ step.number }}
                            </div>
                            <h3 class="mb-2 text-lg font-semibold text-neutral-800">{{ step.title }}</h3>
                            <p class="text-sm leading-relaxed text-neutral-500">{{ step.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trust & Showcase Section with Decorative Images -->
        <section class="relative overflow-hidden bg-white py-16 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <!-- Image collage -->
                    <div class="relative">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-4">
                                <div class="overflow-hidden rounded-2xl shadow-xl">
                                    <img
                                        src="/images/header-1.jpg"
                                        alt="Properti premium"
                                        class="h-48 w-full object-cover transition-transform duration-500 hover:scale-105 sm:h-64"
                                    />
                                </div>
                                <div class="overflow-hidden rounded-2xl shadow-xl">
                                    <img
                                        src="/images/header-3.jpg"
                                        alt="Interior modern"
                                        class="h-32 w-full object-cover transition-transform duration-500 hover:scale-105 sm:h-40"
                                    />
                                </div>
                            </div>
                            <div class="pt-8">
                                <div class="overflow-hidden rounded-2xl shadow-xl">
                                    <img
                                        src="/images/header-2.jpg"
                                        alt="Lingkungan asri"
                                        class="h-56 w-full object-cover transition-transform duration-500 hover:scale-105 sm:h-72"
                                    />
                                </div>
                            </div>
                        </div>
                        <!-- Floating badge -->
                        <div class="absolute -bottom-4 left-4 rounded-xl bg-white p-4 shadow-xl sm:left-8">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                                    <BadgeCheck class="h-5 w-5 text-emerald-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-neutral-800">100% Terverifikasi</p>
                                    <p class="text-xs text-neutral-500">Semua properti kami verifikasi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-neutral-800 sm:text-4xl">
                            Mengapa Memilih
                            <span class="text-emerald-600">PropertiKu</span>?
                        </h2>
                        <p class="mt-4 text-lg leading-relaxed text-neutral-500">
                            Kami bukan sekadar platform listing. Kami adalah fasilitator yang mendampingi Anda dari awal hingga akhir proses transaksi properti.
                        </p>

                        <div class="mt-8 space-y-6">
                            <div class="flex gap-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100">
                                    <Shield class="h-5 w-5 text-emerald-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-neutral-800">Transaksi Aman & Transparan</h3>
                                    <p class="mt-1 text-sm text-neutral-500">Setiap tahap transaksi dapat dipantau secara real-time dengan dokumentasi lengkap.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-100">
                                    <Handshake class="h-5 w-5 text-cyan-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-neutral-800">Notaris Partner Terpercaya</h3>
                                    <p class="mt-1 text-sm text-neutral-500">Bermitra dengan notaris berpengalaman untuk keamanan legalitas transaksi Anda.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100">
                                    <FileCheck class="h-5 w-5 text-amber-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-neutral-800">End-to-End Fasilitasi</h3>
                                    <p class="mt-1 text-sm text-neutral-500">Dari pencarian, negosiasi, AJB, hingga balik nama sertifikat — semua kami dampingi.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-100">
                                    <Eye class="h-5 w-5 text-violet-600" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-neutral-800">Properti Terverifikasi</h3>
                                    <p class="mt-1 text-sm text-neutral-500">Seluruh listing melalui proses verifikasi ketat untuk memastikan keaslian data.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section ref="statsRef" class="bg-emerald-700 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 lg:grid-cols-4">
                    <div v-for="(stat, index) in stats" :key="stat.label" class="text-center">
                        <div class="text-3xl font-bold text-white sm:text-4xl">
                            {{ animatedValues[index] }}{{ stat.suffix }}
                        </div>
                        <div class="mt-1 text-sm text-emerald-200">{{ stat.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative overflow-hidden bg-emerald-600 py-16 sm:py-20">
            <div class="absolute inset-0">
                <div class="absolute -left-20 -top-20 h-72 w-72 rounded-full bg-emerald-500 opacity-40 blur-3xl" />
                <div class="absolute -bottom-20 -right-20 h-72 w-72 rounded-full bg-cyan-500 opacity-30 blur-3xl" />
            </div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Siap Menemukan Properti Impian?
                    </h2>
                    <p class="mt-4 text-lg text-emerald-100">
                        Daftar sekarang dan biarkan kami bantu Anda menemukan properti yang tepat dengan proses yang aman dan transparan.
                    </p>
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <Button size="lg" class="bg-white text-emerald-700 hover:bg-emerald-50" as-child>
                            <Link href="/register">
                                Daftar Sekarang
                                <ArrowRight class="ml-1 h-4 w-4" />
                            </Link>
                        </Button>
                        <Button size="lg" variant="outline" class="border-white/30 text-white hover:bg-white/10 hover:text-white" as-child>
                            <Link href="/kontak">Hubungi Kami</Link>
                        </Button>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
