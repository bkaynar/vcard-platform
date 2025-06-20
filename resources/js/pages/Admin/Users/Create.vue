<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';
import { UserPlus, User, Phone, Mail, Key, MapPin, FileText, Upload, ImageIcon } from 'lucide-vue-next';

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    address: '',
    bio: '',
    profile_photo: null as File | null,
    cover_photo: null as File | null,
    socials: {},
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
    {
        title: 'Yeni Kullanıcı',
        href: route('admin.users.create'),
    },
];

const profilePhotoPreview = ref<string | null>(null);
const coverPhotoPreview = ref<string | null>(null);

const handleProfilePhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.profile_photo = target.files[0];
        profilePhotoPreview.value = URL.createObjectURL(target.files[0]);
    }
};

const handleCoverPhotoChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.cover_photo = target.files[0];
        coverPhotoPreview.value = URL.createObjectURL(target.files[0]);
    }
};

const submit = () => {
    form.post(route('admin.users.store'));
};
</script>

<template>

    <Head title="Yeni Kullanıcı" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Yeni Kullanıcı</h1>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ad
                            Soyad</label>
                        <input v-model="form.name" type="text" id="name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">{{ form.errors.name }}</div>
                    </div>
                    <div>
                        <label for="username"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kullanıcı Adı</label>
                        <input v-model="form.username" type="text" id="username"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <div v-if="form.errors.username" class="text-sm text-red-600 mt-1">{{ form.errors.username }}
                        </div>
                    </div>
                    <div>
                        <label for="email"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-posta</label>
                        <input v-model="form.email" type="email" id="email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <div v-if="form.errors.email" class="text-sm text-red-600 mt-1">{{ form.errors.email }}</div>
                    </div>
                    <div>
                        <label for="phone"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefon</label>
                        <input v-model="form.phone" type="text" id="phone"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <div v-if="form.errors.phone" class="text-sm text-red-600 mt-1">{{ form.errors.phone }}</div>
                    </div>
                    <div>
                        <label for="password"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Şifre</label>
                        <input v-model="form.password" type="password" id="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        <div v-if="form.errors.password" class="text-sm text-red-600 mt-1">{{ form.errors.password }}
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Şifre Tekrar</label>
                        <input v-model="form.password_confirmation" type="password" id="password_confirmation"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label for="address"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adres</label>
                        <textarea v-model="form.address" id="address" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"></textarea>
                        <div v-if="form.errors.address" class="text-sm text-red-600 mt-1">{{ form.errors.address }}
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="bio"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Biyografi</label>
                        <textarea v-model="form.bio" id="bio" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white"></textarea>
                        <div v-if="form.errors.bio" class="text-sm text-red-600 mt-1">{{ form.errors.bio }}</div>
                    </div>
                    <div>
                        <label for="profile_photo"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profil Fotoğrafı</label>
                        <input @input="form.profile_photo = $event.target.files[0]" type="file" id="profile_photo"
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        <div v-if="form.errors.profile_photo" class="text-sm text-red-600 mt-1">{{
                            form.errors.profile_photo }}</div>
                    </div>
                    <div>
                        <label for="cover_photo"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kapak Fotoğrafı</label>
                        <input @input="form.cover_photo = $event.target.files[0]" type="file" id="cover_photo"
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        <div v-if="form.errors.cover_photo" class="text-sm text-red-600 mt-1">{{ form.errors.cover_photo
                            }}</div>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        :disabled="form.processing">Kaydet</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
