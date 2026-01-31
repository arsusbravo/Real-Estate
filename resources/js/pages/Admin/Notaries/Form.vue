<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type NotaryPartner } from '@/types';
import { ArrowLeft } from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { computed } from 'vue';

type Props = {
    notary?: NotaryPartner;
};

const props = defineProps<Props>();

const isEditing = computed(() => !!props.notary);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Notaris', href: '/admin/notaris' },
    { title: isEditing.value ? 'Edit Notaris' : 'Tambah Notaris' },
];

const form = useForm({
    name: props.notary?.name ?? '',
    license_number: props.notary?.license_number ?? '',
    office_name: props.notary?.office_name ?? '',
    address: props.notary?.address ?? '',
    phone: props.notary?.phone ?? '',
    email: props.notary?.email ?? '',
    city: props.notary?.city ?? '',
    specializations: props.notary?.specializations?.join(', ') ?? '',
    is_active: props.notary?.is_active ?? true,
    notes: props.notary?.notes ?? '',
});

function submit() {
    const data = {
        ...form.data(),
        specializations: form.specializations
            .split(',')
            .map((s: string) => s.trim())
            .filter((s: string) => s.length > 0),
    };

    if (isEditing.value) {
        form.transform(() => data).put(`/admin/notaris/${props.notary!.id}`);
    } else {
        form.transform(() => data).post('/admin/notaris');
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Edit Notaris' : 'Tambah Notaris'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center gap-3">
                <Button variant="outline" size="icon" as-child>
                    <Link href="/admin/notaris">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">{{ isEditing ? 'Edit Notaris' : 'Tambah Notaris Baru' }}</h1>
                    <p class="text-muted-foreground text-sm">
                        {{ isEditing ? 'Perbarui informasi notaris' : 'Isi informasi untuk menambahkan notaris baru' }}
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="max-w-2xl space-y-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Notaris</CardTitle>
                        <CardDescription>Data identitas dan kontak notaris</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <Label>Nama</Label>
                                <Input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Nama lengkap notaris"
                                    class="mt-1"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <Label>Nomor Lisensi</Label>
                                <Input
                                    v-model="form.license_number"
                                    type="text"
                                    placeholder="Nomor lisensi notaris"
                                    class="mt-1"
                                />
                                <p v-if="form.errors.license_number" class="mt-1 text-xs text-red-600">{{ form.errors.license_number }}</p>
                            </div>
                        </div>

                        <div>
                            <Label>Nama Kantor</Label>
                            <Input
                                v-model="form.office_name"
                                type="text"
                                placeholder="Nama kantor notaris"
                                class="mt-1"
                            />
                            <p v-if="form.errors.office_name" class="mt-1 text-xs text-red-600">{{ form.errors.office_name }}</p>
                        </div>

                        <div>
                            <Label>Alamat</Label>
                            <textarea
                                v-model="form.address"
                                rows="3"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                placeholder="Alamat lengkap kantor notaris"
                            />
                            <p v-if="form.errors.address" class="mt-1 text-xs text-red-600">{{ form.errors.address }}</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <Label>Telepon</Label>
                                <Input
                                    v-model="form.phone"
                                    type="text"
                                    placeholder="Nomor telepon"
                                    class="mt-1"
                                />
                                <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
                            </div>
                            <div>
                                <Label>Email</Label>
                                <Input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="Alamat email"
                                    class="mt-1"
                                />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                            </div>
                        </div>

                        <div>
                            <Label>Kota</Label>
                            <Input
                                v-model="form.city"
                                type="text"
                                placeholder="Kota tempat kantor"
                                class="mt-1"
                            />
                            <p v-if="form.errors.city" class="mt-1 text-xs text-red-600">{{ form.errors.city }}</p>
                        </div>

                        <div>
                            <Label>Spesialisasi</Label>
                            <Input
                                v-model="form.specializations"
                                type="text"
                                placeholder="Pisahkan dengan koma, contoh: AJB, Balik Nama, PPAT"
                                class="mt-1"
                            />
                            <p class="text-muted-foreground mt-1 text-xs">Pisahkan setiap spesialisasi dengan tanda koma</p>
                            <p v-if="form.errors.specializations" class="mt-1 text-xs text-red-600">{{ form.errors.specializations }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <Checkbox
                                :model-value="form.is_active"
                                @update:model-value="form.is_active = !!$event"
                                id="is_active"
                            />
                            <Label for="is_active" class="cursor-pointer">Aktif</Label>
                        </div>

                        <div>
                            <Label>Catatan</Label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                                placeholder="Catatan tambahan..."
                            />
                            <p v-if="form.errors.notes" class="mt-1 text-xs text-red-600">{{ form.errors.notes }}</p>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex gap-3">
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Menyimpan...' : (isEditing ? 'Perbarui Notaris' : 'Tambah Notaris') }}
                    </Button>
                    <Button variant="outline" as-child>
                        <Link href="/admin/notaris">Batal</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
