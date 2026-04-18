<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle, Eye, EyeOff } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { useForm } from '@inertiajs/vue3';

const page = usePage();
const activeTab = ref<'login' | 'register'>('login');

// Login form
const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});
const loginShowPassword = ref(false);
const submitLogin = () => {
    loginForm.post(route('login'), {
        onFinish: () => loginForm.reset('password'),
    });
};

// Register form
const registerForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});
const registerShowPassword = ref(false);
const registerShowConfirm = ref(false);
const submitRegister = () => {
    registerForm.post(route('register'), {
        onFinish: () => registerForm.reset('password', 'password_confirmation'),
    });
};

const setTab = (tab: 'login' | 'register') => {
    activeTab.value = tab;
};


</script>

<template>
    <Head title="Welcome" />
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />

    <div class="min-h-screen bg-gradient-to-br from-violet-50 to-pink-50 dark:from-slate-900 dark:to-slate-800 flex flex-col md:flex-row">
        <!-- Left Side: Welcome/Brand Hero -->
        <div class="md:w-1/2 flex flex-col items-center justify-center p-8 md:p-12 text-center md:text-left bg-gradient-to-t from-green-500 via-blue-400 to-yellow-400 text-black rounded-r-3xl md:rounded-r-none shadow-2xl">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6 drop-shadow-lg">
                Student Certificate<br class="hidden md:block"/>Online Request System
            </h1>
            <p class="text-xl md:text-2xl mb-8 font-semibold opacity-90 drop-shadow">Request your certificates online quickly and easily.</p>
            <img src="/images/beatriz.jpg" alt="Student Illustration" class="w-64 md:w-80 lg:w-96 mx-auto md:mx-0 rounded-2xl shadow-2xl drop-shadow-2xl" />
            <p class="mt-8 text-lg opacity-80">For future development and enhancing student needs. All rights reserved S.Y 2026-2027</p>
        </div>

        <!-- Right Side: Login/Register Forms -->
        <div class="md:w-1/2 flex items-center justify-center p-8 md:p-12">
            <div class="w-full max-w-md space-y-6">
                <!-- Tab Buttons -->
                <div class="flex rounded-lg border bg-card p-1">
                    <Button :variant="activeTab === 'login' ? 'default' : 'ghost'" class="flex-1" @click="setTab('login')">
                        Log In
                    </Button>
                    <Button :variant="activeTab === 'register' ? 'default' : 'ghost'" class="flex-1" @click="setTab('register')">
                        Sign Up
                    </Button>
                </div>

                <!-- Login Form -->
                <form v-if="activeTab === 'login'" @submit.prevent="submitLogin" class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="login-email">Email address</Label>
                        <Input id="login-email" type="email" required autofocus v-model="loginForm.email" placeholder="email@example.com" />
                        <InputError :message="loginForm.errors.email" />
                    </div>
                    <div class="grid gap-2">
                        <div class="flex items-center justify-between">
                            <Label for="login-password">Password</Label>
                            <TextLink v-if="page.props.canResetPassword as any" href="/forgot-password">Forgot password?</TextLink>
                        </div>
                        
                        <div class="relative">
                            <Input 
                                id="login-password" 
                                :type="loginShowPassword ? 'text' : 'password'" 
                                required 
                                v-model="loginForm.password" 
                                placeholder="Password"
                                class="pr-10" 
                            />
                            <Button type="button" variant="ghost" size="sm" class="absolute right-2 top-1/2 -translate-y-1/2 h-6 w-6 p-0"
                                @click="loginShowPassword = !loginShowPassword">
                                <Eye v-if="!loginShowPassword" class="h-4 w-4" />
                                <EyeOff v-else class="h-4 w-4" />
                            </Button>
                        </div>
                        <InputError :message="loginForm.errors.password" />
                    </div>
                    <div class="flex items-center space-x-2">
                        <input id="remember" type="checkbox" v-model="loginForm.remember" class="rounded border-gray-300" />
                        <Label for="remember">Remember me</Label>
                    </div>
                    <Button type="submit" :disabled="loginForm.processing" class="w-full">
                        <LoaderCircle v-if="loginForm.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Log In
                    </Button>
                    <p class="text-center text-sm text-muted-foreground pt-2">
                        Don't have an account? 
                        <button @click="setTab('register')" class="font-medium underline underline-offset-4 hover:no-underline">
                            Sign up
                        </button>
                    </p>
                </form>

                <!-- Register Form -->
                <form v-else @submit.prevent="submitRegister" class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="reg-name">Name</Label>
                        <Input id="reg-name" type="text" required v-model="registerForm.name" placeholder="Full name" />
                        <InputError :message="registerForm.errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="reg-email">Email address</Label>
                        <Input id="reg-email" type="email" required v-model="registerForm.email" placeholder="email@example.com" />
                        <InputError :message="registerForm.errors.email" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="reg-password">Password</Label>
                        <div class="relative">
                            <Input 
                                id="reg-password" 
                                :type="registerShowPassword ? 'text' : 'password'" 
                                required 
                                v-model="registerForm.password" 
                                placeholder="Password"
                                class="pr-10" 
                            />
                            <Button type="button" variant="ghost" size="sm" class="absolute right-2 top-1/2 -translate-y-1/2 h-6 w-6 p-0"
                                @click="registerShowPassword = !registerShowPassword">
                                <Eye v-if="!registerShowPassword" class="h-4 w-4" />
                                <EyeOff v-else class="h-4 w-4" />
                            </Button>
                        </div>
                        <InputError :message="registerForm.errors.password" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="reg-confirm">Confirm Password</Label>
                        <div class="relative">
                            <Input 
                                id="reg-confirm" 
                                :type="registerShowConfirm ? 'text' : 'password'" 
                                required 
                                v-model="registerForm.password_confirmation" 
                                placeholder="Confirm Password"
                                class="pr-10" 
                            />
                            <Button type="button" variant="ghost" size="sm" class="absolute right-2 top-1/2 -translate-y-1/2 h-6 w-6 p-0"
                                @click="registerShowConfirm = !registerShowConfirm">
                                <Eye v-if="!registerShowConfirm" class="h-4 w-4" />
                                <EyeOff v-else class="h-4 w-4" />
                            </Button>
                        </div>
                        <InputError :message="registerForm.errors.password_confirmation" />
                    </div>
                    <Button type="submit" :disabled="registerForm.processing" class="w-full">
                        <LoaderCircle v-if="registerForm.processing" class="mr-2 h-4 w-4 animate-spin" />
                        Create Account
                    </Button>
                    <p class="text-center text-sm text-muted-foreground pt-2">
                        Already have an account? 
                        <button @click="setTab('login')" class="font-medium underline underline-offset-4 hover:no-underline">
                            Log in
                        </button>
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>

