<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Props = {
    settings: Record<string, string>;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Pengaturan', href: '/admin/pengaturan' },
];

const form = useForm({
    commission_percentage: props.settings.commission_percentage ?? '',
    contact_email: props.settings.contact_email ?? '',
    contact_phone: props.settings.contact_phone ?? '',
    whatsapp_number: props.settings.whatsapp_number ?? '',
    office_address: props.settings.office_address ?? '',
});

function submit() {
    form.put('/admin/pengaturan');
}
</script>

<template>
    <Head title="Pengaturan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold">Pengaturan</h1>
                <p class="text-muted-foreground text-sm">Konfigurasi umum aplikasi</p>
            </div>

            <form @submit.prevent="submit" class="max-w-2xl space-y-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Pengaturan Umum</CardTitle>
                        <CardDescription>Konfigurasi komisi dan informasi kontak</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <Label>Persentase Komisi (%)</Label>
                            <Input
                                v-model="form.commission_percentage"
                                type="number"
                                step="0.1"
                                min="0"
                                max="100"
                                placeholder="Contoh: 2.5"
                                class="mt-1"
                            />
                            <p class="text-muted-foreground mt-1 text-xs">Persentase komisi untuk setiap transaksi</p>
                            <p v-if="form.errors.commission_percentage" class="mt-1 text-xs text-red-600">{{ form.errors.commission_percentage }}</p>
                        </div>

                        <div>
                            <Label>Email Kontak</Label>
                            <Input
                                v-model="form.contact_email"
                                type="email"
                                placeholder="admin@example.com"
                                class="mt-1"
                            />
                            <p v-if="form.errors.contact_email" class="mt-1 text-xs text-red-600">{{ form.errors.contact_email }}</p>
                        </div>

                        <div>
                            <Label>Telepon Kontak</Label>
                            <Input
                                v-model="form.contact_phone"
                                type="text"
                                placeholder="+62..."
                                class="mt-1"
                            />
                            <p v-if="form.errors.contact_phone" class="mt-1 text-xs text-red-600">{{ form.errors.contact_phone }}</p>
                        </div>

                        <div>
                            <Label>Nomor WhatsApp</Label>
                            <Input
                                v-model="form.whatsapp_number"
                                type="text"
                                placeholder="+62..."
                                class="mt-1"
                            />
                            <p v-if="form.errors.whatsapp_number" class="mt-1 text-xs text-red-600">{{ form.errors.whatsapp_number }}</p>
                        </div>

                        <div>
                            <Label>Alamat Kantor</Label>
                            <textarea
                                v-model="form.office_address"
                                rows="3"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                placeholder="Alamat lengkap kantor..."
                            />
                            <p v-if="form.errors.office_address" class="mt-1 text-xs text-red-600">{{ form.errors.office_address }}</p>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex gap-3">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                    </Button>
                    <Button
                        variant="outline"
                        type="button"
                        :disabled="!form.isDirty"
                        @click="form.reset()"
                    >
                        Reset
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
