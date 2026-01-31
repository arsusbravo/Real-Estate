<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Building2, ShoppingBag } from 'lucide-vue-next';
import { ref } from 'vue';

const selectedRole = ref('buyer');
</script>

<template>
    <AuthBase
        title="Daftar Akun"
        description="Buat akun untuk mulai mencari atau menjual properti"
    >
        <Head title="Daftar" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <!-- Role Selection -->
                <div class="grid gap-2">
                    <Label>Daftar sebagai</Label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="flex cursor-pointer flex-col items-center gap-2 rounded-lg border-2 p-4 transition-colors"
                            :class="selectedRole === 'buyer'
                                ? 'border-primary bg-primary/5'
                                : 'border-border hover:border-primary/50'"
                        >
                            <input
                                type="radio"
                                name="role"
                                value="buyer"
                                v-model="selectedRole"
                                class="sr-only"
                            />
                            <ShoppingBag class="h-6 w-6" :class="selectedRole === 'buyer' ? 'text-primary' : 'text-muted-foreground'" />
                            <span class="text-sm font-medium" :class="selectedRole === 'buyer' ? 'text-primary' : ''">Pembeli</span>
                            <span class="text-muted-foreground text-xs text-center">Cari & beli properti</span>
                        </label>
                        <label
                            class="flex cursor-pointer flex-col items-center gap-2 rounded-lg border-2 p-4 transition-colors"
                            :class="selectedRole === 'seller'
                                ? 'border-primary bg-primary/5'
                                : 'border-border hover:border-primary/50'"
                        >
                            <input
                                type="radio"
                                name="role"
                                value="seller"
                                v-model="selectedRole"
                                class="sr-only"
                            />
                            <Building2 class="h-6 w-6" :class="selectedRole === 'seller' ? 'text-primary' : 'text-muted-foreground'" />
                            <span class="text-sm font-medium" :class="selectedRole === 'seller' ? 'text-primary' : ''">Penjual</span>
                            <span class="text-muted-foreground text-xs text-center">Jual properti Anda</span>
                        </label>
                    </div>
                    <InputError :message="errors.role" />
                </div>

                <div class="grid gap-2">
                    <Label for="name">Nama Lengkap</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Nama lengkap Anda"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@contoh.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="phone">No. Telepon / WhatsApp</Label>
                    <Input
                        id="phone"
                        type="tel"
                        :tabindex="3"
                        autocomplete="tel"
                        name="phone"
                        placeholder="08xxxxxxxxxx"
                    />
                    <InputError :message="errors.phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Konfirmasi Password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="5"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Konfirmasi password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="6"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" />
                    Daftar
                </Button>
            </div>

            <div class="text-muted-foreground text-center text-sm">
                Sudah punya akun?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="7"
                    >Masuk</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
