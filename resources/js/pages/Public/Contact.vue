<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Phone, Mail, MapPin, Clock, Send } from 'lucide-vue-next';
import PublicLayout from '@/layouts/PublicLayout.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

function submit() {
    form.post('/kontak', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

const inputClass = 'w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm shadow-sm placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500';
</script>

<template>
    <Head title="Kontak - PropertiKu" />

    <PublicLayout>
        <!-- Hero -->
        <section class="bg-gradient-to-br from-emerald-600 to-teal-700 py-20 sm:py-28">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">Hubungi Kami</h1>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-emerald-100">
                    Ada pertanyaan tentang properti atau layanan kami? Tim kami siap membantu Anda.
                </p>
            </div>
        </section>

        <!-- Content -->
        <section class="py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-3">
                    <!-- Contact Form -->
                    <div class="lg:col-span-2">
                        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm">
                            <div class="border-b border-neutral-200 px-6 py-4">
                                <h2 class="text-lg font-semibold text-neutral-800">Kirim Pesan</h2>
                            </div>
                            <div class="p-6">
                                <form @submit.prevent="submit" class="space-y-4">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="name" class="text-sm font-medium text-neutral-700">Nama Lengkap</label>
                                            <input id="name" v-model="form.name" placeholder="Nama Anda" :class="inputClass" class="mt-1" />
                                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                        </div>
                                        <div>
                                            <label for="email" class="text-sm font-medium text-neutral-700">Email</label>
                                            <input id="email" v-model="form.email" type="email" placeholder="email@contoh.com" :class="inputClass" class="mt-1" />
                                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                                        </div>
                                    </div>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="phone" class="text-sm font-medium text-neutral-700">No. Telepon</label>
                                            <input id="phone" v-model="form.phone" placeholder="08xx-xxxx-xxxx" :class="inputClass" class="mt-1" />
                                            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                                        </div>
                                        <div>
                                            <label for="subject" class="text-sm font-medium text-neutral-700">Subjek</label>
                                            <input id="subject" v-model="form.subject" placeholder="Perihal pesan Anda" :class="inputClass" class="mt-1" />
                                            <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">{{ form.errors.subject }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="message" class="text-sm font-medium text-neutral-700">Pesan</label>
                                        <textarea
                                            id="message"
                                            v-model="form.message"
                                            rows="5"
                                            class="mt-1 w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm placeholder:text-neutral-400 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                            placeholder="Tuliskan pesan Anda di sini..."
                                        />
                                        <p v-if="form.errors.message" class="mt-1 text-sm text-red-600">{{ form.errors.message }}</p>
                                    </div>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-700 disabled:opacity-50"
                                        :disabled="form.processing"
                                    >
                                        <Send class="mr-2 h-4 w-4" />
                                        Kirim Pesan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-4">
                        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                    <MapPin class="h-5 w-5" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-neutral-800">Alamat Kantor</h3>
                                    <p class="mt-1 text-sm text-neutral-500">
                                        Jl. Sudirman No. 123<br />
                                        Jakarta Selatan, 12190<br />
                                        Indonesia
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                    <Phone class="h-5 w-5" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-neutral-800">Telepon</h3>
                                    <p class="mt-1 text-sm text-neutral-500">(021) 1234-5678</p>
                                    <p class="text-sm text-neutral-500">WhatsApp: 0812-3456-7890</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                    <Mail class="h-5 w-5" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-neutral-800">Email</h3>
                                    <p class="mt-1 text-sm text-neutral-500">info@propertiku.id</p>
                                    <p class="text-sm text-neutral-500">support@propertiku.id</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                    <Clock class="h-5 w-5" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-neutral-800">Jam Operasional</h3>
                                    <p class="mt-1 text-sm text-neutral-500">Senin - Jumat: 09:00 - 17:00</p>
                                    <p class="text-sm text-neutral-500">Sabtu: 09:00 - 13:00</p>
                                    <p class="text-sm text-neutral-500">Minggu & Hari Libur: Tutup</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
