<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Building2,
    ClipboardList,
    FileCheck,
    FileText,
    Heart,
    Home,
    LayoutGrid,
    MessageSquare,
    Settings,
    Users,
    BarChart3,
    Scale,
} from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Admin navigation
const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Properti',
        href: '/admin/properti',
        icon: Building2,
    },
    {
        title: 'Transaksi',
        href: '/admin/transaksi',
        icon: FileCheck,
    },
    {
        title: 'Pengguna',
        href: '/admin/pengguna',
        icon: Users,
    },
    {
        title: 'Inquiry',
        href: '/admin/inquiry',
        icon: MessageSquare,
    },
    {
        title: 'Notaris',
        href: '/admin/notaris',
        icon: Scale,
    },
    {
        title: 'Dokumen',
        href: '/admin/dokumen/pending',
        icon: FileText,
    },
    {
        title: 'Laporan',
        href: '/admin/laporan/transaksi',
        icon: BarChart3,
    },
    {
        title: 'Pengaturan',
        href: '/admin/pengaturan',
        icon: Settings,
    },
];

// Buyer navigation
const buyerNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/buyer/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Cari Properti',
        href: '/properti',
        icon: Building2,
    },
    {
        title: 'Favorit',
        href: '/buyer/favorit',
        icon: Heart,
    },
    {
        title: 'Kebutuhan Saya',
        href: '/buyer/kebutuhan',
        icon: ClipboardList,
    },
    {
        title: 'Transaksi',
        href: '/buyer/transaksi',
        icon: FileCheck,
    },
];

// Seller navigation
const sellerNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/seller/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Properti Saya',
        href: '/seller/properti',
        icon: Building2,
    },
    {
        title: 'Inquiry',
        href: '/seller/inquiry',
        icon: MessageSquare,
    },
    {
        title: 'Transaksi',
        href: '/seller/transaksi',
        icon: FileCheck,
    },
];

const mainNavItems = computed(() => {
    const role = user.value?.role;
    switch (role) {
        case 'admin':
            return adminNavItems;
        case 'seller':
            return sellerNavItems;
        case 'buyer':
        default:
            return buyerNavItems;
    }
});

const footerNavItems: NavItem[] = [
    {
        title: 'Beranda',
        href: '/',
        icon: Home,
    },
];

const dashboardHref = computed(() => {
    const role = user.value?.role;
    switch (role) {
        case 'admin':
            return '/admin/dashboard';
        case 'seller':
            return '/seller/dashboard';
        default:
            return '/buyer/dashboard';
    }
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardHref">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
