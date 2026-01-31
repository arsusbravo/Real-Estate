<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type User, type Paginated } from '@/types';
import {
    Search,
    Users,
    Eye,
    Shield,
    ShoppingCart,
    Home,
} from 'lucide-vue-next';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ref, watch } from 'vue';

type Props = {
    users: Paginated<User>;
    filters: {
        role?: string;
        search?: string;
    };
    counts: {
        all: number;
        admin: number;
        buyer: number;
        seller: number;
    };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboard' },
    { title: 'Pengguna', href: '/admin/pengguna' },
];

const search = ref(props.filters.search ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/pengguna', { search: val || undefined, role: props.filters.role }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

function filterByRole(role?: string) {
    router.get('/admin/pengguna', { role, search: search.value || undefined }, {
        preserveState: true,
        replace: true,
    });
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

const roleLabel: Record<string, string> = {
    admin: 'Admin',
    buyer: 'Pembeli',
    seller: 'Penjual',
};

const roleColor: Record<string, string> = {
    admin: 'default',
    buyer: 'secondary',
    seller: 'outline',
};

const roleIcon: Record<string, any> = {
    admin: Shield,
    buyer: ShoppingCart,
    seller: Home,
};
</script>

<template>
    <Head title="Kelola Pengguna" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div>
                <h1 class="text-2xl font-bold">Kelola Pengguna</h1>
                <p class="text-muted-foreground text-sm">{{ counts.all }} total pengguna terdaftar</p>
            </div>

            <!-- Role Tabs -->
            <div class="flex flex-wrap gap-2">
                <Button
                    :variant="!filters.role ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByRole(undefined)"
                >
                    Semua ({{ counts.all }})
                </Button>
                <Button
                    :variant="filters.role === 'admin' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByRole('admin')"
                >
                    <Shield class="mr-1 h-3 w-3" />
                    Admin ({{ counts.admin }})
                </Button>
                <Button
                    :variant="filters.role === 'buyer' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByRole('buyer')"
                >
                    <ShoppingCart class="mr-1 h-3 w-3" />
                    Pembeli ({{ counts.buyer }})
                </Button>
                <Button
                    :variant="filters.role === 'seller' ? 'default' : 'outline'"
                    size="sm"
                    @click="filterByRole('seller')"
                >
                    <Home class="mr-1 h-3 w-3" />
                    Penjual ({{ counts.seller }})
                </Button>
            </div>

            <!-- Search -->
            <div class="relative max-w-sm">
                <Search class="text-muted-foreground absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" />
                <Input
                    v-model="search"
                    type="search"
                    placeholder="Cari nama atau email..."
                    class="pl-10"
                />
            </div>

            <!-- Empty State -->
            <div v-if="users.data.length === 0" class="text-muted-foreground py-12 text-center">
                <Users class="mx-auto mb-4 h-12 w-12 opacity-50" />
                <p>Tidak ada pengguna ditemukan</p>
            </div>

            <!-- Users Table -->
            <div v-else class="overflow-x-auto rounded-lg border">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-muted/50 border-b">
                            <th class="px-4 py-3 text-left font-medium">Nama</th>
                            <th class="px-4 py-3 text-left font-medium">Email</th>
                            <th class="px-4 py-3 text-left font-medium">Role</th>
                            <th class="px-4 py-3 text-left font-medium">Terdaftar</th>
                            <th class="px-4 py-3 text-center font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="user in users.data"
                            :key="user.id"
                            class="border-b last:border-0 hover:bg-muted/30"
                        >
                            <td class="px-4 py-3">
                                <Link
                                    :href="`/admin/pengguna/${user.id}`"
                                    class="font-medium hover:underline"
                                >
                                    {{ user.name }}
                                </Link>
                            </td>
                            <td class="text-muted-foreground px-4 py-3">{{ user.email }}</td>
                            <td class="px-4 py-3">
                                <Badge :variant="(roleColor[user.role] as any) ?? 'secondary'">
                                    {{ roleLabel[user.role] ?? user.role }}
                                </Badge>
                            </td>
                            <td class="text-muted-foreground px-4 py-3">
                                {{ formatDate(user.created_at) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <Button variant="ghost" size="icon" as-child>
                                    <Link :href="`/admin/pengguna/${user.id}`">
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.last_page > 1" class="flex items-center justify-center gap-2 py-4">
                <template v-for="link in users.links" :key="link.label">
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
