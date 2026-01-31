<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Building2, Menu, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';

const mobileMenuOpen = ref(false);

const navLinks = [
    { href: '/properti', label: 'Properti' },
    { href: '/tentang-kami', label: 'Tentang Kami' },
    { href: '/layanan', label: 'Layanan' },
    { href: '/kontak', label: 'Kontak' },
];
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-50 border-b border-neutral-200/60 bg-white/80 backdrop-blur-lg supports-[backdrop-filter]:bg-white/70">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-2.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-600">
                        <Building2 class="h-5 w-5 text-white" />
                    </div>
                    <span class="text-xl font-bold tracking-tight text-neutral-800">PropertiKu</span>
                </Link>

                <nav class="hidden items-center gap-1 md:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="rounded-lg px-3.5 py-2 text-sm font-medium text-neutral-600 transition-colors hover:bg-neutral-100 hover:text-emerald-700"
                    >
                        {{ link.label }}
                    </Link>
                </nav>

                <div class="hidden items-center gap-2 md:flex">
                    <template v-if="$page.props.auth?.user">
                        <Button size="sm" as-child>
                            <Link href="/dashboard">Dashboard</Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button variant="ghost" size="sm" as-child>
                            <Link href="/login">Masuk</Link>
                        </Button>
                        <Button size="sm" as-child>
                            <Link href="/register">Daftar</Link>
                        </Button>
                    </template>
                </div>

                <!-- Mobile menu button -->
                <button
                    class="inline-flex items-center justify-center rounded-lg p-2 text-neutral-600 hover:bg-neutral-100 md:hidden"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <X v-if="mobileMenuOpen" class="h-5 w-5" />
                    <Menu v-else class="h-5 w-5" />
                </button>
            </div>

            <!-- Mobile menu -->
            <div v-if="mobileMenuOpen" class="border-t border-neutral-200 bg-white px-4 pb-4 pt-2 md:hidden">
                <nav class="flex flex-col gap-1">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="rounded-lg px-3 py-2.5 text-sm font-medium text-neutral-600 hover:bg-neutral-100"
                        @click="mobileMenuOpen = false"
                    >
                        {{ link.label }}
                    </Link>
                </nav>
                <div class="mt-3 flex flex-col gap-2 border-t border-neutral-200 pt-3">
                    <template v-if="$page.props.auth?.user">
                        <Button size="sm" as-child class="w-full">
                            <Link href="/dashboard">Dashboard</Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button variant="outline" size="sm" as-child class="w-full">
                            <Link href="/login">Masuk</Link>
                        </Button>
                        <Button size="sm" as-child class="w-full">
                            <Link href="/register">Daftar</Link>
                        </Button>
                    </template>
                </div>
            </div>
        </header>

        <!-- Main content -->
        <main class="flex-1 pt-16">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-neutral-200 bg-neutral-50">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid gap-8 md:grid-cols-4">
                    <div class="md:col-span-1">
                        <Link href="/" class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600">
                                <Building2 class="h-4 w-4 text-white" />
                            </div>
                            <span class="text-lg font-bold text-neutral-800">PropertiKu</span>
                        </Link>
                        <p class="mt-3 text-sm leading-relaxed text-neutral-500">
                            Platform fasilitator properti terpercaya di Jakarta. Dari pencarian hingga serah terima sertifikat.
                        </p>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-neutral-800">Navigasi</h4>
                        <ul class="mt-3 space-y-2">
                            <li v-for="link in navLinks" :key="link.href">
                                <Link :href="link.href" class="text-sm text-neutral-500 hover:text-emerald-700">
                                    {{ link.label }}
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-neutral-800">Layanan</h4>
                        <ul class="mt-3 space-y-2">
                            <li><span class="text-sm text-neutral-500">Jual Beli Properti</span></li>
                            <li><span class="text-sm text-neutral-500">Sewa Properti</span></li>
                            <li><span class="text-sm text-neutral-500">Konsultasi Hukum</span></li>
                            <li><span class="text-sm text-neutral-500">Notaris Partner</span></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-neutral-800">Kontak</h4>
                        <ul class="mt-3 space-y-2">
                            <li><span class="text-sm text-neutral-500">Jakarta, Indonesia</span></li>
                            <li><span class="text-sm text-neutral-500">info@propertiku.id</span></li>
                            <li><span class="text-sm text-neutral-500">+62 21 1234 5678</span></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-neutral-200 pt-8">
                    <p class="text-center text-sm text-neutral-500">
                        &copy; {{ new Date().getFullYear() }} PropertiKu. Hak cipta dilindungi.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
