<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { defineProps } from 'vue';

defineProps({
    users: Object,
});

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
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Ad Soyad</th>
                            <th scope="col" class="px-6 py-3">E-posta</th>
                            <th scope="col" class="px-6 py-3">Rol</th>
                            <th scope="col" class="px-6 py-3">
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
                            <td class="px-6 py-4">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4">
                                <span v-for="role in user.roles" :key="role.id"
                                    class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{
                                    role.name }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="route('admin.users.edit', user.id)"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Düzenle</Link>
                                <Link :href="route('admin.users.destroy', user.id)" method="delete" as="button"
                                    class="ml-4 font-medium text-red-600 dark:text-red-500 hover:underline">Sil</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
