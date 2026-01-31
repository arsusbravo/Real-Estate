<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Users, Shield, Handshake, MapPin, ArrowRight, CheckCircle } from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';
</script>

<template>
    <Head title="Tentang Kami - PropertiKu" />

    <PublicLayout>
        <!-- Hero -->
        <section class="bg-gradient-to-br from-emerald-600 to-teal-700 py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">Tentang PropertiKu</h1>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-emerald-100">
                    Platform fasilitator properti terpercaya di Jakarta yang membantu Anda dari pencarian hingga serah terima sertifikat.
                </p>
            </div>
        </section>

        <!-- Visi & Misi -->
        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-2">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-8">
                        <div class="mb-4 inline-flex items-center justify-center rounded-lg bg-emerald-600 p-2">
                            <Shield class="h-6 w-6 text-white" />
                        </div>
                        <h2 class="text-2xl font-bold text-neutral-800">Visi Kami</h2>
                        <p class="mt-3 leading-relaxed text-neutral-600">
                            Menjadi platform fasilitator properti nomor satu di Jakarta yang memberikan pengalaman transaksi properti yang transparan, aman, dan efisien bagi semua pihak.
                        </p>
                    </div>
                    <div class="rounded-xl border border-neutral-200 bg-white p-8 shadow-sm">
                        <div class="mb-4 inline-flex items-center justify-center rounded-lg bg-emerald-600 p-2">
                            <Handshake class="h-6 w-6 text-white" />
                        </div>
                        <h2 class="text-2xl font-bold text-neutral-800">Misi Kami</h2>
                        <ul class="mt-3 space-y-3 text-neutral-600">
                            <li class="flex items-start gap-3">
                                <CheckCircle class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
                                Menyediakan listing properti yang terverifikasi dan berkualitas
                            </li>
                            <li class="flex items-start gap-3">
                                <CheckCircle class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
                                Memfasilitasi transaksi end-to-end dari viewing hingga balik nama
                            </li>
                            <li class="flex items-start gap-3">
                                <CheckCircle class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
                                Bermitra dengan notaris terpercaya untuk keamanan hukum
                            </li>
                            <li class="flex items-start gap-3">
                                <CheckCircle class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
                                Memberikan transparansi penuh dalam setiap tahap transaksi
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Kenapa PropertiKu -->
        <section class="bg-neutral-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-center text-2xl font-bold text-neutral-800">Mengapa Memilih PropertiKu?</h2>
                <p class="mx-auto mt-2 max-w-xl text-center text-neutral-500">Keunggulan yang membedakan kami dari platform properti lainnya</p>
                <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl bg-white p-6 text-center shadow-sm">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
                            <Shield class="h-7 w-7 text-emerald-600" />
                        </div>
                        <h3 class="font-semibold text-neutral-800">Aman & Terpercaya</h3>
                        <p class="mt-2 text-sm text-neutral-500">Setiap properti diverifikasi dan transaksi dilindungi secara hukum</p>
                    </div>
                    <div class="rounded-xl bg-white p-6 text-center shadow-sm">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
                            <Handshake class="h-7 w-7 text-emerald-600" />
                        </div>
                        <h3 class="font-semibold text-neutral-800">Fasilitasi Penuh</h3>
                        <p class="mt-2 text-sm text-neutral-500">Dari pencarian, negosiasi, hingga penandatanganan AJB dan balik nama</p>
                    </div>
                    <div class="rounded-xl bg-white p-6 text-center shadow-sm">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
                            <Users class="h-7 w-7 text-emerald-600" />
                        </div>
                        <h3 class="font-semibold text-neutral-800">Tim Profesional</h3>
                        <p class="mt-2 text-sm text-neutral-500">Didukung oleh tim berpengalaman di industri properti Jakarta</p>
                    </div>
                    <div class="rounded-xl bg-white p-6 text-center shadow-sm">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
                            <MapPin class="h-7 w-7 text-emerald-600" />
                        </div>
                        <h3 class="font-semibold text-neutral-800">Fokus Jakarta</h3>
                        <p class="mt-2 text-sm text-neutral-500">Pengetahuan mendalam tentang pasar properti Jakarta dan sekitarnya</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-emerald-700 py-16">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-white">Siap Memulai?</h2>
                <p class="mt-3 text-emerald-100">Bergabung dengan ribuan pengguna yang telah mempercayakan transaksi properti mereka kepada PropertiKu</p>
                <div class="mt-8 flex justify-center gap-4">
                    <Link
                        href="/register"
                        class="inline-flex items-center rounded-lg bg-white px-6 py-3 text-sm font-semibold text-emerald-700 shadow-sm transition-colors hover:bg-emerald-50"
                    >
                        Daftar Sekarang
                        <ArrowRight class="ml-2 h-4 w-4" />
                    </Link>
                    <Link
                        href="/properti"
                        class="inline-flex items-center rounded-lg border border-white/30 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-white/10"
                    >
                        Lihat Properti
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
