<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Property, type User } from '@/types';
import { ArrowLeft } from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Props = {
    properties: Property[];
    buyers: User[];
    sellers: User[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Transaksi', href: '/admin/transaksi' },
    { title: 'Buat Transaksi' },
];

const form = useForm({
    property_id: '',
    buyer_id: '',
    seller_id: '',
    status: 'inquiry',
    agreed_price: '',
    notes: '',
});

const statusOptions: { value: string; label: string }[] = [
    { value: 'inquiry', label: 'Inquiry' },
    { value: 'viewing_scheduled', label: 'Jadwal Viewing' },
    { value: 'negotiation', label: 'Negosiasi' },
    { value: 'agreement_reached', label: 'Kesepakatan' },
    { value: 'dp_pending', label: 'Menunggu DP' },
];

function submit() {
    form.post('/admin/transaksi');
}

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
}
</script>

<template>
    <Head title="Buat Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center gap-3">
                <Button variant="outline" size="icon" as-child>
                    <Link href="/admin/transaksi">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Buat Transaksi Baru</h1>
                    <p class="text-muted-foreground text-sm">Isi informasi untuk membuat transaksi baru</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="max-w-2xl space-y-4">
                <!-- Properti -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Transaksi</CardTitle>
                        <CardDescription>Pilih properti, pembeli, dan penjual</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <Label>Properti</Label>
                            <select
                                v-model="form.property_id"
                                class="border-input bg-background ring-offset-background focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                            >
                                <option value="">-- Pilih Properti --</option>
                                <option v-for="property in properties" :key="property.id" :value="property.id">
                                    {{ property.title }} - {{ formatCurrency(property.price) }} ({{ property.city }})
                                </option>
                            </select>
                            <p v-if="form.errors.property_id" class="mt-1 text-xs text-red-600">{{ form.errors.property_id }}</p>
                        </div>

                        <div>
                            <Label>Pembeli</Label>
                            <select
                                v-model="form.buyer_id"
                                class="border-input bg-background ring-offset-background focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                            >
                                <option value="">-- Pilih Pembeli --</option>
                                <option v-for="buyer in buyers" :key="buyer.id" :value="buyer.id">
                                    {{ buyer.name }} ({{ buyer.email }})
                                </option>
                            </select>
                            <p v-if="form.errors.buyer_id" class="mt-1 text-xs text-red-600">{{ form.errors.buyer_id }}</p>
                        </div>

                        <div>
                            <Label>Penjual</Label>
                            <select
                                v-model="form.seller_id"
                                class="border-input bg-background ring-offset-background focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                            >
                                <option value="">-- Pilih Penjual --</option>
                                <option v-for="seller in sellers" :key="seller.id" :value="seller.id">
                                    {{ seller.name }} ({{ seller.email }})
                                </option>
                            </select>
                            <p v-if="form.errors.seller_id" class="mt-1 text-xs text-red-600">{{ form.errors.seller_id }}</p>
                        </div>

                        <div>
                            <Label>Status Awal</Label>
                            <select
                                v-model="form.status"
                                class="border-input bg-background ring-offset-background focus-visible:ring-ring mt-1 w-full rounded-md border px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                            >
                                <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-xs text-red-600">{{ form.errors.status }}</p>
                        </div>

                        <div>
                            <Label>Harga Kesepakatan (Opsional)</Label>
                            <Input
                                v-model="form.agreed_price"
                                type="number"
                                placeholder="Masukkan harga kesepakatan"
                                class="mt-1"
                            />
                            <p v-if="form.errors.agreed_price" class="mt-1 text-xs text-red-600">{{ form.errors.agreed_price }}</p>
                        </div>

                        <div>
                            <Label>Catatan (Opsional)</Label>
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
                        {{ form.processing ? 'Menyimpan...' : 'Buat Transaksi' }}
                    </Button>
                    <Button variant="outline" as-child>
                        <Link href="/admin/transaksi">Batal</Link>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
