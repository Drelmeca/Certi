<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogClose } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { LoaderCircle, Plus } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';

interface User {
  id: number;
  name: string;
  email: string;
  status: number;
  role: number;
}

interface FormData {
  id: number | null;
  name: string;
  email: string;
  status: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Users',
        href: '/users',
    },
];

defineProps<{ users: User[] }> ();

const isOpen = ref(false);
const isLoading = ref(false);
const editId = ref<number | null>(null);
const formData = ref<FormData>({
    id: null,
    name: '',
    email: '',
    status: '1',
});

const errors = ref<Record<string, string>>({});

const resetForm = () => {
    formData.value = { id: null, name: '', email: '', status: '1' };
    errors.value = {};
};

const openCreate = () => {
    resetForm();
    editId.value = null;
    isOpen.value = true;
};

const openEdit = async (id: number) => {
    editId.value = id;
    isLoading.value = true;
    try {
        const response = await axios.get(`/users/${id}`);
        formData.value.id = response.data.id;
        formData.value.name = response.data.name;
        formData.value.email = response.data.email;
        formData.value.status = response.data.status.toString();
    } catch (error) {
        console.error('Error loading user:', error);
        alert('Error loading user data');
    } finally {
        isLoading.value = false;
        isOpen.value = true;
    }
};

const submit = async () => {
    isLoading.value = true;
    errors.value = {};
    try {
        const endpoint = formData.value.id ? `/users/${formData.value.id}` : '/users/store';
        const response = await axios.post(endpoint, formData.value);
        alert(response.data.message || 'Success!');
        window.location.reload();
    } catch (error: any) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        } else {
            alert('Error occurred.');
        }
    } finally {
        isLoading.value = false;
    }
};

const deleteUser = (id: number) => {
    if (confirm('Are you sure you want to delete this user?')) {
        axios.delete(`/users/${id}`).then(() => {
            window.location.reload();
        }).catch(() => alert('Delete failed'));
    }
};
</script>

<template>
    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold tracking-tight">Users</h1>
            <Dialog v-model="isOpen">
                <DialogTrigger as-child>
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Create User
                    </Button>
                </DialogTrigger>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{{ editId ? 'Edit User' : 'Create User' }}</DialogTitle>
                        <DialogDescription>
                            {{ editId ? 'Update the user details below.' : 'Add a new user account.' }}
                        </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="formData.name" placeholder="John Doe" />
                            <div v-if="errors.name" class="text-destructive text-sm">{{ errors.name[0] }}</div>
                        </div>
                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input id="email" type="email" v-model="formData.email" placeholder="john@example.com" />
                            <div v-if="errors.email" class="text-destructive text-sm">{{ errors.email[0] }}</div>
                        </div>
                        <div class="space-y-2">
                            <Label for="status">Status</Label>
                            <Button variant="outline" class="w-full justify-start" :class="{ 'bg-green-500 text-white': formData.status === '1' }" type="button" @click="formData.status = '1'">
                                Active
                            </Button>
                            <Button variant="outline" class="w-full justify-start" :class="{ 'bg-red-500 text-white': formData.status === '0' }" type="button" @click="formData.status = '0'">
                                Inactive
                            </Button>
                        </div>
                        <DialogFooter>
                            <DialogClose as-child>
                                <Button type="button" variant="outline">Cancel</Button>
                            </DialogClose>
                            <Button type="submit" :disabled="isLoading">
                                <LoaderCircle v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
                                {{ editId ? 'Update User' : 'Create User' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

        <div class="overflow-hidden rounded-lg border bg-card shadow-md">
            <table class="w-full table-auto">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" :key="user.id" class="border-b hover:bg-muted/50">
                        <td class="px-6 py-4 font-medium">{{ user.name }}</td>
                        <td class="px-6 py-4 text-muted-foreground">{{ user.email }}</td>
                        <td class="px-6 py-4">
                            <span v-if="user.status === 1" class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                            <span v-else class="px-3 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Inactive</span>
                        </td>
                        <td class="px-6 py-4">
                            <span v-if="user.role === 2" class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Admin</span>
                            <span v-else class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">User</span>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <Button variant="outline" size="sm" @click="openEdit(user.id)">Edit</Button>
                            <Button variant="destructive" size="sm" @click="deleteUser(user.id)">Delete</Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>

