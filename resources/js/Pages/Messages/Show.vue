<template>
    <AppLayout title="Conversation">
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <Link :href="route('messages.index')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </Link>
                    <img
                        :src="otherUser.profile_photo_url"
                        :alt="otherUser.name"
                        class="h-10 w-10 rounded-full mr-3"
                    >
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ otherUser.name }}
                    </h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Alerte de confidentialité discrète -->
                <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 dark:border-yellow-600 p-3 mb-4 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400 dark:text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm text-yellow-700 dark:text-yellow-300">
                                Cette messagerie est réservée à un usage professionnel et peut être consultée par l'administration et la direction.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center">
                        <p class="font-medium text-gray-600 dark:text-gray-300">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Conversation professionnelle</span>
                        </p>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span v-if="orderedMessages.length > 0">
                                {{ orderedMessages.length }} message{{ orderedMessages.length > 1 ? 's' : '' }}
                            </span>
                            <span v-else>Nouvelle conversation</span>
                        </div>
                    </div>
                    <div class="flex flex-col h-[50vh]">
                        <!-- Messages area avec style Apple -->
                        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 flex flex-col bg-gray-100 dark:bg-gray-900">
                            <div class="space-y-3">
                                <div v-for="message in orderedMessages" :key="message.id"
                                    :class="[
                                        'flex',
                                        message.sender_id === $page.props.auth.user.id ? 'justify-end' : 'justify-start'
                                    ]">
                                    <div :class="[
                                        'max-w-sm px-4 py-2 rounded-2xl',
                                        message.sender_id === $page.props.auth.user.id
                                            ? 'bg-indigo-300 text-white'
                                            : 'bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-white',
                                        message.sender_id === $page.props.auth.user.id
                                            ? 'rounded-tr-sm'
                                            : 'rounded-tl-sm',
                                    ]">
                                        <p class="text-sm">{{ message.content }}</p>
                                        <p :class="[
                                            'text-xs mt-1 text-right',
                                            message.sender_id === $page.props.auth.user.id
                                                ? 'text-white text-opacity-80'
                                                : 'text-gray-600 dark:text-gray-400'
                                        ]">
                                            <time-ago :datetime="message.created_at" />
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Élément invisible pour servir d'ancrage au défilement -->
                            <div ref="messagesEnd"></div>
                        </div>

                        <!-- Message input avec style Apple -->
                        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 bg-white dark:bg-gray-800">
                            <form @submit.prevent="sendMessage" class="flex space-x-2">
                                <div class="flex-1 relative">
                                    <input
                                        v-model="form.content"
                                        type="text"
                                        class="w-full rounded-full border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300"
                                        placeholder="Message"
                                        :disabled="form.processing"
                                    >
                                </div>
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 disabled:opacity-50 transition"
                                    :disabled="form.processing || !form.content.trim()"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, nextTick, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import TimeAgo from '@/Components/TimeAgo.vue';

const props = defineProps({
    conversation: Object,
    messages: Array,
    otherUser: Object
});

const form = useForm({
    content: ''
});

const messagesContainer = ref(null);
const messagesEnd = ref(null);

// Trier les messages par date croissante (plus anciens en premier)
const orderedMessages = computed(() => {
    return [...props.messages].sort((a, b) =>
        new Date(a.created_at) - new Date(b.created_at)
    );
});

const sendMessage = () => {
    form.post(route('messages.reply', props.conversation.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            scrollToBottom();
        }
    });
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesEnd.value) {
            messagesEnd.value.scrollIntoView({ behavior: 'smooth' });
        }
    });
};

onMounted(() => {
    scrollToBottom();
});
</script>
