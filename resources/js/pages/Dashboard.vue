<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps({totalClaims: Number, pendingRequests: Number, totalAdmins: Number});
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-blue-600 dark:bg-blue-700 flex items-center justify-between p-6 shadow">
                    <div>
                        <h2 class="text-lg font-semibold text-white mb-2">Total Released</h2>
                        <p class="text-3xl font-bold text-white">{{ totalClaims }}</p>
                       
                    </div>
                    <img src="/images/bag-check-svgrepo-com.svg" alt="Requests" class="h-24 w-24 object-contain ml-4" />
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div
                        class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-green-600 dark:bg-green-700 flex items-center justify-between p-6 shadow">
                        <div>
                            <h2 class="text-lg font-semibold text-white mb-2">Total Pendings</h2>
                            <p class="text-3xl font-bold text-white">{{ pendingRequests }}</p>
                            
                        </div>
                        <img src="/images/time-quarter-past-svgrepo-com.svg" alt="Requests" class="h-24 w-24 object-contain ml-4" />
                    </div>
                </div>
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <div
                        class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-red-600 dark:bg-red-700 flex items-center justify-between p-6 shadow">
                        <div>
                            <h2 class="text-lg font-semibold text-white mb-2">Total Admin</h2>
                            <p class="text-3xl font-bold text-white">{{ totalAdmins }}</p>
                            
                        </div>
                        <img src="/images/user-shield-alt-1-svgrepo-com.svg" alt="Requests" class="h-24 w-24 object-contain ml-4" />
                    </div>
                </div>
            </div>
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
