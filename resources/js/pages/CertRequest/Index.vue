<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';
import { type BreadcrumbItem } from '@/types';
const breadcrumbs = [
     
    { title: 'My Request', 
    href: '/CertRequest'
    },   
];

// Define the type for a CertRequest item
interface CertRequestItem {
    id: number;
    first_name: string;
    middle_name: string;
    last_name: string;
    suffix: string;
    email: string;
    contact_number: string;
    request_type: string;
    request_purpose: string;
    request_date: string;
   
}

const props = defineProps<{ CertRequest: CertRequestItem[], guestRequests?: CertRequestItem[] }>();

const page = usePage();
const isAdmin = computed(() => page.props?.value?.auth?.user?.role === 2);

const certRequests = ref<CertRequestItem[]>(props.CertRequest ?? []);
const guestRequests = ref<CertRequestItem[]>(props.guestRequests ?? []);

const onEdit = (id) => {
    // Redirect to the edit page for the selected person
    //inertia visit(`/CertRequest/${id}/edit`);
    // or use window.location.href  
    router.visit(`/CertRequest/edit/${id}`);
};

const approve = async (id: number) => {
    try {
        await axios.post(`/CertRequest/${id}/approve`);
        const idx = guestRequests.value.findIndex(r => r.id === id);
        if (idx !== -1) {
            const item = { ...guestRequests.value[idx], status: 1 } as CertRequestItem & { status: number };
            guestRequests.value.splice(idx, 1);
            certRequests.value.unshift(item);
        }
    } catch (e) {
        console.error(e);
        alert('Failed to approve request');
    }
};

const reject = async (id: number) => {
    try {
        await axios.post(`/CertRequest/${id}/reject`);
        const idx = guestRequests.value.findIndex(r => r.id === id);
        if (idx !== -1) {
            const item = { ...guestRequests.value[idx], status: 2 } as CertRequestItem & { status: number };
            guestRequests.value.splice(idx, 1);
            certRequests.value.unshift(item);
        }
    } catch (e) {
        console.error(e);
        alert('Failed to reject request');
    }
};
</script> 

<template>

    <Head title="My Requests" />

   <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">My Request</h1>
            <Link href="/CertRequest/create" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Create Request
            </Link>
        </div>
        <div class="overflow-hidden rounded-lg border bg-card shadow-md dark:border-slate-700 dark:bg-slate-800">
            <table class="w-full table-auto text-left text-sm text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">First Name</th>
                        <th class="px-6 py-3">Middle Name</th>
                        <th class="px-6 py-3">Last Name</th>
                        <th class="px-6 py-3">Suffix</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Contact Number</th>
                        <th class="px-6 py-3">Request Type</th>
                        <th class="px-6 py-3">Request Purpose</th>
                        <th class="px-6 py-3">Request Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in certRequests" :key="row.id" class="border-b bg-white dark:border-gray-700 dark:bg-gray-800">
                        <td class="px-6 py-4">{{ row.first_name }}</td>
                        <td class="px-6 py-4">{{ row.middle_name }}</td>
                        <td class="px-6 py-4">{{ row.last_name }}</td>
                        <td class="px-6 py-4">{{ row.suffix }}</td>
                        <td class="px-6 py-4">{{ row.email }}</td>
                        <td class="px-6 py-4">{{ row.contact_number }}</td>
                        <td class="px-6 py-4">{{ row.request_type }}</td>
                        <td class="px-6 py-4">{{ row.request_purpose }}</td>
                        <td class="px-6 py-4">{{ row.request_date }}</td>
                        
                        <td class="px-6 py-4">
                            <button @click="onEdit(row.id)" class="text-blue-600 hover:text-blue-800">
                                <span v-if="row.status === 1" class="text-green-600">Approved</span>
                                <span v-else-if="row.status === 0" class="text-yellow-600">Pending</span>
                                <span v-else class="text-red-600">Rejected</span>
                            </button>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Admin-only: Guest Requests -->
        <div v-if="isAdmin" class="mt-8">
            <h2 class="text-xl font-bold mb-2">Guest Requests (Admin only)</h2>
            <div class="overflow-hidden rounded-lg border bg-card shadow-md dark:border-slate-700 dark:bg-slate-800">
                <table class="w-full table-auto text-left text-sm text-gray-500 dark:text-gray-400">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">First Name</th>
                            <th class="px-6 py-3">Middle Name</th>
                            <th class="px-6 py-3">Last Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Request Purpose</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in guestRequests" :key="row.id" class="border-b bg-white dark:border-gray-700 dark:bg-gray-800">
                            <td class="px-6 py-4">{{ row.first_name }}</td>
                            <td class="px-6 py-4">{{ row.middle_name }}</td>
                            <td class="px-6 py-4">{{ row.last_name }}</td>
                            <td class="px-6 py-4">{{ row.email }}</td>
                            <td class="px-6 py-4">{{ row.request_purpose }}</td>
                            <td class="px-6 py-4">{{ row.request_date }}</td>
                            <td class="px-6 py-4">
                                <button @click="approve(row.id)" class="mr-2 inline-flex items-center px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">Accept</button>
                                <button @click="reject(row.id)" class="inline-flex items-center px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Reject</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
