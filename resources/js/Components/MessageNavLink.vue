<template>
    <NavLink :href="route('messages.index')" :active="route().current('messages.*')" class="relative">
        Messages
        <span v-if="unreadCount > 0" class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 bg-red-600 rounded-full">
            {{ unreadCount > 99 ? '99+' : unreadCount }}
        </span>
    </NavLink>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import NavLink from '@/Components/NavLink.vue';
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
