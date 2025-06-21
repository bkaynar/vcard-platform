<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

// Props tanımlaması
interface User {
    id: number;
    name: string;
    email: string;
    roles: Array<{
        id: number;
        name: string;
    }>;
}

interface Props {
    users: {
        data: User[];
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Yönetim Paneli',
        href: route('admin.dashboard'),
    },
    {
        title: 'Kullanıcılar',
        href: route('admin.users.index'),
    },
];
</script>

<template>

    <Head title="Kullanıcılar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Kullanıcılar</h1>
                <Link :href="route('admin.users.create')"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Yeni Kullanıcı
                </Link>
            </div>
            <div
                class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Responsive table wrapper -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3 min-w-[150px]">Ad Soyad</th>
                                <th scope="col" class="px-6 py-3 min-w-[200px]">E-posta</th>
                                <th scope="col" class="px-6 py-3 min-w-[100px]">Rol</th>
                                <th scope="col" class="px-6 py-3 min-w-[120px]">
                                    <span class="sr-only">İşlemler</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users.data" :key="user.id"
                                class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ user.name }}
                                </th>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4">
                                    <span v-for="role in user.roles" :key="role.id"
                                        class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 whitespace-nowrap">{{
                                            role.name }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('admin.users.edit', user.id)"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition-colors">
                                        Düzenle
                                        </Link>
                                        <Link :href="route('admin.users.destroy', user.id)" method="delete" as="button"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40 transition-colors">
                                        Sil
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
