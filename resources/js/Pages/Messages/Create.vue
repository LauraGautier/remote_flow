<template>
    <AppLayout title="Nouveau message">
        <template #header>
            <div class="flex items-center">
                <Link :href="route('messages.index')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Nouveau message
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Alerte de confidentialité -->
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 dark:border-yellow-600 p-4 mb-4 rounded-md shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                Rappel
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>Cette messagerie est réservée aux communications professionnelles.</p>
                                <p class="mt-1">Pour toute communication personnelle, veuillez utiliser d'autres moyens de communication.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="mb-4">
                            <label for="recipient" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Destinataire
                            </label>
                            <select
                                id="recipient"
                                v-model="form.recipient_id"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50"
                                :class="{ 'border-red-500 dark:border-red-500': form.errors.recipient_id }"
                                required
                            >
                                <option value="">Sélectionnez un destinataire</option>
                                <option
                                    v-for="user in users"
                                    :key="user.id"
                                    :value="user.id"
                                >
                                    {{ user.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.recipient_id" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.recipient_id }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Sujet (optionnel)
                            </label>
                            <input
                                id="subject"
                                v-model="form.subject"
                                type="text"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50"
                                :class="{ 'border-red-500 dark:border-red-500': form.errors.subject }"
                            >
                            <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.subject }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Message
                            </label>
                            <textarea
                                id="content"
                                v-model="form.content"
                                rows="5"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50"
                                :class="{ 'border-red-500 dark:border-red-500': form.errors.content }"
                                required
                            ></textarea>
                            <p v-if="form.errors.content" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.content }}
                            </p>
                            <!-- Rappel sous le champ message -->
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Rappel : Les messages envoyés sont stockés en base de données et peuvent être consultés par l'administration.
                            </p>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Link :href="route('messages.index')" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-500 focus:outline-none focus:border-gray-500 dark:focus:border-gray-400 focus:ring focus:ring-gray-300 dark:focus:ring-gray-700 disabled:opacity-25 transition">
                                Annuler
                            </Link>
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-800 focus:outline-none focus:border-blue-900 dark:focus:border-blue-800 focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 disabled:opacity-25 transition"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Envoi en cours...
                                </span>
                                <span v-else class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Envoyer
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    users: Array,
    selectedUser: Object
});

const form = useForm({
    recipient_id: props.selectedUser?.id || '',
    subject: '',
    content: ''
});

const submit = () => {
    form.post(route('messages.store'));
};

onMounted(() => {
    if (props.selectedUser) {
        form.recipient_id = props.selectedUser.id;
    }
});
</script>
