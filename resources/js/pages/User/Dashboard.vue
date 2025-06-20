<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profil Paneli',
        href: '/dashboard',
    },
];
</script>

<template>

    <Head title="Profil Paneli" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-6 text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Hoş geldiniz!</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    Profil bilgilerinizi güncelleyebilir, vCard istatistiklerinizi takip edebilirsiniz.
                </p>

                <!-- Kullanıcı istatistikleri -->
                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Toplam Ziyaret</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">834</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Bugünkü Görüntüleme</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">23</p>
                    </div>
                </div>

                <!-- Butonlar -->
                <div class="mt-8 flex flex-col gap-3">
                    <a href="/profile/edit"
                        class="inline-block w-full rounded-md bg-blue-600 text-white py-2 font-semibold hover:bg-blue-700 transition">
                        Profili Düzenle
                    </a>
                    <a href="/vcard/{{ $page.props.auth.user.id }}"
                        class="inline-block w-full rounded-md bg-gray-200 text-gray-900 py-2 font-semibold dark:bg-gray-700 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        vCard’ımı Görüntüle
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
