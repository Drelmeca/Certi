<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogClose } from '@/components/ui/dialog';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';
import { LoaderCircle } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';

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
    status: number;
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'My Request', href: '/CertRequest' }];

const props = defineProps<{ CertRequest: CertRequestItem[], guestRequests?: CertRequestItem[] }>();

const page = usePage();
const isAdmin = computed(() => {
    const auth = (page.props as any).auth;
    return auth?.user?.role === 2;
});

const certRequests = ref<CertRequestItem[]>(props.CertRequest ?? []);

const isOpen = ref(false);
const editingRow = ref<CertRequestItem | null>(null);
const isLoading = ref(false);

const form = ref({
    id: null as number | null,
    first_name: '',
    middle_name: '',
    last_name: '',
    suffix: '',
    email: '',
    contact_number: '',
    request_type: '',
    request_purpose: '',
});

const errors = ref<Record<string, string>>({});

const resetForm = () => {
    form.value = {
        id: null,
        first_name: '',
        middle_name: '',
        last_name: '',
        suffix: '',
        email: '',
        contact_number: '',
        request_type: '',
        request_purpose: '',
    };
    errors.value = {};
};

const openCreate = () => {
    resetForm();
    editingRow.value = null;
    isOpen.value = true;
};

const openEdit = (row: CertRequestItem) => {
    editingRow.value = row;
    form.value = {
        id: row.id,
        first_name: row.first_name,
        middle_name: row.middle_name || '',
        last_name: row.last_name,
        suffix: row.suffix || '',
        email: row.email,
        contact_number: row.contact_number,
        request_type: row.request_type,
        request_purpose: row.request_purpose,
    };
    isOpen.value = true;
};

const submit = async () => {
    isLoading.value = true;
    errors.value = {};
    try {
        const response = await axios.post('/CertRequest/store', form.value);
        alert(response.data.message);
        router.reload({ only: ['CertRequest'] });
    } catch (error: any) {
        errors.value = error.response?.data?.errors || {};
        alert('Error: ' + (error.response?.data?.message || 'Unknown error'));
    } finally {
        isLoading.value = false;
    }
};

const onApprove = (id: number) => {
    if (confirm('Approve this request?')) {
        axios.post(`/CertRequest/${id}/approve`).then(() => router.reload({ only: ['CertRequest'] }));
    }
};

const onReject = (id: number) => {
    if (confirm('Reject this request?')) {
        axios.post(`/CertRequest/${id}/reject`).then(() => router.reload({ only: ['CertRequest'] }));
    }
};
</script>

<template>
    <Head title="My Requests" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">My Requests</h1>
            <Dialog v-model="isOpen">
                <DialogTrigger as-child>
                    <Button>
                        Create Request
                    </Button>
                </DialogTrigger>
                <DialogContent class="max-w-2xl max-h-[85vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>{{ editingRow ? 'Edit Request' : 'Create Request' }}</DialogTitle>
                        <DialogDescription>Enter the details below.</DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label>First Name</Label>
                                <Input v-model="form.first_name" required />
                                <InputError v-if="errors.first_name" :message="errors.first_name[0]" />
                            </div>
                            <div class="space-y-2">
                                <Label>Middle Name</Label>
                                <Input v-model="form.middle_name" />
                            </div>
                            <div class="space-y-2">
                                <Label>Last Name</Label>
                                <Input v-model="form.last_name" required />
                                <InputError v-if="errors.last_name" :message="errors.last_name[0]" />
                            </div>
                            <div class="space-y-2">
                                <Label>Suffix</Label>
                                <Input v-model="form.suffix" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label>Email</Label>
                                <Input type="email" v-model="form.email" required />
                                <InputError v-if="errors.email" :message="errors.email[0]" />
                            </div>
                            <div class="space-y-2">
                                <Label>Contact Number</Label>
                                <Input v-model="form.contact_number" placeholder="11 digits" maxlength="11" required />
                                <InputError v-if="errors.contact_number" :message="errors.contact_number[0]" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label>Request Type</Label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <Button size="sm" variant="outline" :class="{'bg-blue-500 text-white': form.request_type === 'Certificate'}" type="button" @click="form.request_type = 'Certificate'">
                                        Certificate
                                    </Button>
                                    <Button size="sm" variant="outline" :class="{'bg-green-500 text-white': form.request_type === 'Goodmoral'}" type="button" @click="form.request_type = 'Goodmoral'">
                                        Good Moral
                                    </Button>
                                    <Button size="sm" variant="outline" :class="{'bg-yellow-500 text-white': form.request_type === 'Form 137'}" type="button" @click="form.request_type = 'Form 137'">
                                        Form 137
                                    </Button>
                                    <Button size="sm" variant="outline" :class="{'bg-purple-500 text-white': form.request_type === 'Clearance'}" type="button" @click="form.request_type = 'Clearance'">
                                        Clearance
                                    </Button>
                                    <Button size="sm" variant="outline" :class="{'bg-gray-500 text-white': form.request_type === 'Other'}" type="button" @click="form.request_type = 'Other'">
                                        Other
                                    </Button>
                                </div>
                                <InputError v-if="errors.request_type" :message="errors.request_type[0]" />
                            </div>
                            <div class="space-y-2">
                                <Label>Request Purpose</Label>
                                <Input v-model="form.request_purpose" required />
                                <InputError v-if="errors.request_purpose" :message="errors.request_purpose[0]" />
                            </div>
                        </div>
                        <DialogFooter>
                            <DialogClose as-child>
                                <Button type="button" variant="outline">Cancel</Button>
                            </DialogClose>
                            <Button type="submit" :disabled="isLoading">
                                <LoaderCircle v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
                                {{ editingRow ? 'Update' : 'Create' }} Request
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

        <div class="mt-6 overflow-hidden rounded-lg border bg-card shadow-md">
            <table class="w-full table-auto">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-muted-foreground">First</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Middle</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Last</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Suffix</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Purpose</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Status</th>
                        <th v-if="isAdmin" class="px-4 py-3 text-left text-xs font-medium uppercase text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in certRequests" :key="row.id" class="border-b hover:bg-muted/50">
                        <td class="px-4 py-4">{{ row.first_name }}</td>
                        <td class="px-4 py-4">{{ row.middle_name || '-' }}</td>
                        <td class="px-4 py-4">{{ row.last_name }}</td>
                        <td class="px-4 py-4">{{ row.suffix || '-' }}</td>
                        <td class="px-4 py-4">{{ row.email }}</td>
                        <td class="px-4 py-4">{{ row.contact_number }}</td>
                        <td class="px-4 py-4">{{ row.request_type }}</td>
                        <td class="px-4 py-4 max-w-xs truncate">{{ row.request_purpose }}</td>
                        <td class="px-4 py-4">
                            <span v-if="row.status === 1" class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Approved</span>
                            <span v-else-if="row.status === 0" class="px-2.5 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                            <span v-else class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Rejected</span>
                        </td>
                        <td v-if="isAdmin" class="px-4 py-4 space-x-1">
                            <Button size="sm" variant="outline" @click="router.visit(`/CertRequest/edit/${row.id}`)">Edit</Button>
                            <Button size="sm" class="bg-green-500" @click="onApprove(row.id)">✔</Button>
                            <Button size="sm" variant="destructive" @click="onReject(row.id)">✘</Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
