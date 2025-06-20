<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Yönetim Paneli',
        href: '/admin/dashboard',
    },
];
</script>

<template>

    <Head title="Yönetim Paneli" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Kartlar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Toplam Kullanıcı</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">1452</p>
                </div>
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Bugünkü Ziyaretler</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">238</p>
                </div>
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Yeni Kayıtlar</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">32</p>
                </div>
                <div
                    class="bg-white dark:bg-gray-900 rounded-xl p-4 shadow border border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">VCard Görüntülenme</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">4,890</p>
                </div>
            </div>

            <!-- İstatistik veya grafik bölümü -->
            <div class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow border border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ziyaret İstatistikleri</h2>
                <PlaceholderPattern class="h-[300px]" />
                <!-- Buraya grafik bileşeni (örneğin: ApexChart, Chart.js) yerleştirilebilir -->
            </div>
        </div>
    </AppLayout>
</template>
