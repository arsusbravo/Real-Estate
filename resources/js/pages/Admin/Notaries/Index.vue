<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type NotaryPartner, type Paginated } from '@/types';
import {
    Search,
    Scale,
    Plus,
    MoreHorizontal,
    Pencil,
    Trash2,
} from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { ref, watch } from 'vue';

type Props = {
    notaries: Paginated<NotaryPartner>;
    filters?: {
        search?: string;
        active?: string;
    };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Notaris', href: '/admin/notaris' },
];

const search = ref(props.filters?.search ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/notaris', { search: val || undefined, active: props.filters?.active }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

function deleteNotary(id: number) {
    if (confirm('Apakah Anda yakin ingin menghapus notaris ini?')) {
        router.delete(`/admin/notaris/${id}`);
    }
}
</script>

<template>
    <Head title="Kelola Notaris" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Kelola Notaris</h1>
                    <p class="text-muted-foreground text-sm">{{ notaries.total }} total notaris</p>
                </div>
                <Button as-child>
                    <Link href="/admin/notaris/buat">
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Notaris
                    </Link>
                </Button>
            </div>

            <!-- Search -->
            <div class="relative max-w-sm">
                <Search class="text-muted-foreground absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" />
                <Input
                    v-model="search"
                    type="search"
                    placeholder="Cari notaris..."
                    class="pl-10"
                />
            </div>

            <!-- Empty State -->
            <div v-if="notaries.data.length === 0" class="text-muted-foreground py-12 text-center">
                <Scale class="mx-auto mb-4 h-12 w-12 opacity-50" />
                <p>Tidak ada notaris ditemukan</p>
            </div>

            <!-- Notaries Table -->
            <div v-else class="overflow-x-auto rounded-lg border">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-muted/50 border-b">
                            <th class="px-4 py-3 text-left font-medium">Nama</th>
                            <th class="px-4 py-3 text-left font-medium">No. Lisensi</th>
                            <th class="px-4 py-3 text-left font-medium">Kantor</th>
                            <th class="px-4 py-3 text-left font-medium">Kota</th>
                            <th class="px-4 py-3 text-left font-medium">Telepon</th>
                            <th class="px-4 py-3 text-center font-medium">Status</th>
                            <th class="px-4 py-3 text-center font-medium">Transaksi</th>
                            <th class="px-4 py-3 text-center font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="notary in notaries.data"
                            :key="notary.id"
                            class="border-b last:border-0 hover:bg-muted/30"
                        >
                            <td class="px-4 py-3 font-medium">{{ notary.name }}</td>
                            <td class="px-4 py-3">{{ notary.license_number }}</td>
                            <td class="px-4 py-3">{{ notary.office_name }}</td>
                            <td class="px-4 py-3">{{ notary.city }}</td>
                            <td class="px-4 py-3">{{ notary.phone }}</td>
                            <td class="px-4 py-3 text-center">
                                <Badge :variant="notary.is_active ? 'default' : 'secondary'">
                                    {{ notary.is_active ? 'Aktif' : 'Nonaktif' }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ notary.transactions_count ?? 0 }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" size="icon">
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem as-child>
                                            <Link :href="`/admin/notaris/${notary.id}/edit`">
                                                <Pencil class="mr-2 h-4 w-4" />
                                                Edit
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem
                                            class="text-red-600 focus:text-red-600"
                                            @click="deleteNotary(notary.id)"
                                        >
                                            <Trash2 class="mr-2 h-4 w-4" />
                                            Hapus
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="notaries.last_page > 1" class="flex items-center justify-center gap-2 py-4">
                <template v-for="link in notaries.links" :key="link.label">
                    <Button
                        v-if="link.url"
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        as-child
                    >
                        <Link :href="link.url" v-html="link.label" />
                    </Button>
                    <Button
                        v-else
                        variant="outline"
                        size="sm"
                        disabled
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
