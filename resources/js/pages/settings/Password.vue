<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Eye, EyeOff } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Password settings',
        href: '/settings/password',
    },
];

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const currentShowPassword = ref(false);
const newShowPassword = ref(false);
const confirmShowPassword = ref(false);

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: (errors: any) => {
            if (errors.password) {
                form.reset('password', 'password_confirmation');
                if (passwordInput.value instanceof HTMLInputElement) {
                    passwordInput.value.focus();
                }
            }

            if (errors.current_password) {
                form.reset('current_password');
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus();
                }
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Password settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Update password" description="Ensure your account is using a long, random password to stay secure" />

                <form @submit.prevent="updatePassword" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="current_password">Current password</Label>
                        <div class="relative">
                            <Input
                                id="current_password"
                                ref="currentPasswordInput"
                                v-model="form.current_password"
                                :type="currentShowPassword ? 'text' : 'password'"
                                class="pr-10"
                                autocomplete="current-password"
                                placeholder="Current password"
                            />
                            <Button type="button" variant="ghost" size="sm" class="absolute right-2 top-1/2 -translate-y-1/2 h-6 w-6 p-0"
                                @click="currentShowPassword = !currentShowPassword">
                                <Eye v-if="!currentShowPassword" class="h-4 w-4" />
                                <EyeOff v-else class="h-4 w-4" />
                            </Button>
                        </div>
                        <InputError :message="form.errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">New password</Label>
                        <div class="relative">
                            <Input
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                :type="newShowPassword ? 'text' : 'password'"
                                class="pr-10"
                                autocomplete="new-password"
                                placeholder="New password"
                            />
                            <Button type="button" variant="ghost" size="sm" class="absolute right-2 top-1/2 -translate-y-1/2 h-6 w-6 p-0"
                                @click="newShowPassword = !newShowPassword">
                                <Eye v-if="!newShowPassword" class="h-4 w-4" />
                                <EyeOff v-else class="h-4 w-4" />
                            </Button>
                        </div>
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">Confirm password</Label>
                        <div class="relative">
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="confirmShowPassword ? 'text' : 'password'"
                                class="pr-10"
                                autocomplete="new-password"
                                placeholder="Confirm password"
                            />
                            <Button type="button" variant="ghost" size="sm" class="absolute right-2 top-1/2 -translate-y-1/2 h-6 w-6 p-0"
                                @click="confirmShowPassword = !confirmShowPassword">
                                <Eye v-if="!confirmShowPassword" class="h-4 w-4" />
                                <EyeOff v-else class="h-4 w-4" />
                            </Button>
                        </div>
                        <InputError :message="form.errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save password</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>

