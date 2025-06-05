<template>
    <AppLayout title="Messages">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Messagerie
                </h2>
                <Link :href="route('messages.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-800 focus:outline-none focus:border-blue-900 dark:focus:border-blue-800 focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 disabled:opacity-25 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nouveau message
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Alerte de confidentialité -->
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 dark:border-yellow-600 p-4 mb-4 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                Information de confidentialité
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>Cette messagerie est réservée aux communications professionnelles.<br> Tous les messages sont conservés dans nos systèmes et peuvent être consultés par les administrateurs et la direction.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div v-if="conversations.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucune conversation</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Commencez par envoyer un message à un collaborateur.</p>
                        <div class="mt-6">
                            <Link :href="route('messages.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-blue-500 dark:focus:ring-blue-600">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Commencer une conversation
                            </Link>
                        </div>
                    </div>

                    <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                        <li v-for="conversation in conversations" :key="conversation.id">
                            <Link
                                :href="route('messages.show', conversation.id)"
                                class="block hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-4 sm:px-6"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0">
                                        <img
                                            :src="conversation.other_user.profile_photo_url"
                                            :alt="conversation.other_user.name"
                                            class="h-12 w-12 rounded-full"
                                        >
                                        <div class="ml-4 flex-1">
                                            <div class="flex items-center">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                    {{ conversation.other_user.name }}
                                                </p>
                                                <span v-if="conversation.unread_count > 0" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300">
                                                    {{ conversation.unread_count }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                <span v-if="conversation.last_message">
                                                    <span v-if="conversation.last_message.sender_id === $page.props.auth.user.id" class="font-medium">Vous:</span>
                                                    {{ conversation.last_message.content }}
                                                </span>
                                                <span v-else>Aucun message</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="ml-6 flex-shrink-0">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            <time-ago :datetime="conversation.last_message_at" />
                                        </p>
                                    </div>
                                </div>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TimeAgo from '@/Components/TimeAgo.vue';

const props = defineProps({
    conversations: Array,
    unreadCount: Number
});
</script>
