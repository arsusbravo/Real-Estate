<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    Search,
    Eye,
    FileCheck,
    Handshake,
    ScrollText,
    Home,
    ArrowRight,
} from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <Head title="Layanan Kami - PropertiKu" />

    <PublicLayout>
        <!-- Hero -->
        <section class="bg-gradient-to-br from-emerald-600 to-teal-700 py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">Layanan Kami</h1>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-emerald-100">
                    PropertiKu menyediakan layanan fasilitasi properti end-to-end, dari pencarian hingga serah terima sertifikat.
                </p>
            </div>
        </section>

        <!-- Alur Transaksi -->
        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-center text-2xl font-bold text-neutral-800">Alur Transaksi Properti</h2>
                <p class="mt-2 text-center text-neutral-500">Kami mendampingi Anda di setiap tahap transaksi</p>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                <Search class="h-5 w-5" />
                            </div>
                            <div>
                                <span class="text-xs font-medium text-emerald-600">Tahap 1</span>
                                <h3 class="font-semibold text-neutral-800">Pencarian Properti</h3>
                            </div>
                        </div>
                        <p class="text-sm leading-relaxed text-neutral-500">Cari properti sesuai kebutuhan Anda melalui listing terverifikasi kami, atau sampaikan kriteria Anda dan kami carikan yang terbaik.</p>
                    </div>

                    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                <Eye class="h-5 w-5" />
                            </div>
                            <div>
                                <span class="text-xs font-medium text-emerald-600">Tahap 2</span>
                                <h3 class="font-semibold text-neutral-800">Viewing & Survey</h3>
                            </div>
                        </div>
                        <p class="text-sm leading-relaxed text-neutral-500">Kami atur jadwal kunjungan ke properti pilihan Anda. Tim kami akan mendampingi dan memberikan informasi detail lokasi.</p>
                    </div>

                    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                <Handshake class="h-5 w-5" />
                            </div>
                            <div>
                                <span class="text-xs font-medium text-emerald-600">Tahap 3</span>
                                <h3 class="font-semibold text-neutral-800">Negosiasi & Kesepakatan</h3>
                            </div>
                        </div>
                        <p class="text-sm leading-relaxed text-neutral-500">Kami fasilitasi negosiasi harga dan syarat antara pembeli dan penjual hingga tercapai kesepakatan yang adil.</p>
                    </div>

                    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                <FileCheck class="h-5 w-5" />
                            </div>
                            <div>
                                <span class="text-xs font-medium text-emerald-600">Tahap 4</span>
                                <h3 class="font-semibold text-neutral-800">Verifikasi Dokumen</h3>
                            </div>
                        </div>
                        <p class="text-sm leading-relaxed text-neutral-500">Kami bantu kumpulkan dan verifikasi semua dokumen yang diperlukan: KTP, KK, NPWP, SHM, PBB, IMB, dan lainnya.</p>
                    </div>

                    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                <ScrollText class="h-5 w-5" />
                            </div>
                            <div>
                                <span class="text-xs font-medium text-emerald-600">Tahap 5</span>
                                <h3 class="font-semibold text-neutral-800">Proses Notaris & AJB</h3>
                            </div>
                        </div>
                        <p class="text-sm leading-relaxed text-neutral-500">Kami koordinasikan dengan notaris rekanan untuk penyusunan PPJB, penandatanganan AJB, pembayaran pajak (BPHTB & PPh), dan balik nama.</p>
                    </div>

                    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                <Home class="h-5 w-5" />
                            </div>
                            <div>
                                <span class="text-xs font-medium text-emerald-600">Tahap 6</span>
                                <h3 class="font-semibold text-neutral-800">Serah Terima</h3>
                            </div>
                        </div>
                        <p class="text-sm leading-relaxed text-neutral-500">Setelah semua proses selesai, kami pastikan serah terima kunci dan sertifikat berjalan lancar. Properti resmi menjadi milik Anda.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Layanan Tambahan -->
        <section class="bg-emerald-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-center text-2xl font-bold text-neutral-800">Layanan untuk Penjual</h2>
                <p class="mt-2 text-center text-neutral-500">Kami juga membantu penjual dalam memasarkan properti mereka</p>
                <div class="mt-12 grid gap-8 lg:grid-cols-3">
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                            <Search class="h-5 w-5 text-emerald-600" />
                        </div>
                        <h3 class="font-semibold text-neutral-800">Listing Properti</h3>
                        <p class="mt-2 text-sm text-neutral-500">Daftarkan properti Anda secara gratis. Tim kami akan bantu review dan optimalkan listing Anda agar cepat terjual.</p>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                            <Eye class="h-5 w-5 text-emerald-600" />
                        </div>
                        <h3 class="font-semibold text-neutral-800">Manajemen Inquiry</h3>
                        <p class="mt-2 text-sm text-neutral-500">Kelola semua pertanyaan dan permintaan viewing dari calon pembeli dalam satu dashboard.</p>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100">
                            <Handshake class="h-5 w-5 text-emerald-600" />
                        </div>
                        <h3 class="font-semibold text-neutral-800">Pendampingan Transaksi</h3>
                        <p class="mt-2 text-sm text-neutral-500">Kami dampingi seluruh proses transaksi hingga selesai, termasuk koordinasi dengan notaris dan pengurusan dokumen.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-emerald-700 py-16">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-white">Mulai Sekarang</h2>
                <p class="mt-3 text-emerald-100">Daftar gratis dan nikmati kemudahan transaksi properti bersama PropertiKu</p>
                <div class="mt-8 flex justify-center gap-4">
                    <Link
                        href="/register"
                        class="inline-flex items-center rounded-lg bg-white px-6 py-3 text-sm font-semibold text-emerald-700 shadow-sm transition-colors hover:bg-emerald-50"
                    >
                        Daftar Sekarang
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Link>
                    <Link
                        href="/kontak"
                        class="inline-flex items-center rounded-lg border border-white/30 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-white/10"
                    >
                        Hubungi Kami
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
