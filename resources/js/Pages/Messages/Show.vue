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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-5 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center">
                        <p class="font-medium text-gray-600 dark:text-gray-300">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Conversation privée</span>
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
                                <input
                                    v-model="form.content"
                                    type="text"
                                    class="flex-1 rounded-full border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-600 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300"
                                    placeholder="Message"
                                    :disabled="form.processing"
                                >
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
