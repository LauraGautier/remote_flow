<template>
    <div class="relative">
        <Link :href="route('messages.index')"
              :class="[
                  'flex items-center justify-center w-9 h-9 rounded-full transition ease-in-out duration-150',
                  unreadCount > 0
                    ? 'bg-red-100 dark:bg-red-900 hover:bg-red-200 dark:hover:bg-red-800'
                    : 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600'
              ]">
            <svg :class="[
                     'h-5 w-5',
                     unreadCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-300'
                 ]"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </Link>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const unreadCount = ref(0);

const fetchUnreadCount = async () => {
    try {
        const response = await axios.get(route('messages.unreadCount'));
        unreadCount.value = response.data.count;
    } catch (error) {
        console.error('Erreur lors de la récupération du nombre de messages non lus:', error);
    }
};

onMounted(() => {
    fetchUnreadCount();

    // Rafraîchir toutes les 30 secondes
    setInterval(fetchUnreadCount, 30000);
});
</script>
