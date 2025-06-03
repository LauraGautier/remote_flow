<template>
    <span class="text-gray-500">{{ timeAgo }}</span>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    datetime: {
        type: [String, Date],
        required: true
    }
});

const now = ref(new Date());
let timer = null;

const timeAgo = computed(() => {
    const date = new Date(props.datetime);
    const seconds = Math.floor((now.value - date) / 1000);

    if (seconds < 60) {
        return 'à l\'instant';
    }

    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) {
        return `il y a ${minutes} minute${minutes > 1 ? 's' : ''}`;
    }

    const hours = Math.floor(minutes / 60);
    if (hours < 24) {
        return `il y a ${hours} heure${hours > 1 ? 's' : ''}`;
    }

    const days = Math.floor(hours / 24);
    if (days < 30) {
        return `il y a ${days} jour${days > 1 ? 's' : ''}`;
    }

    const months = Math.floor(days / 30);
    if (months < 12) {
        return `il y a ${months} mois`;
    }

    const years = Math.floor(months / 12);
    return `il y a ${years} an${years > 1 ? 's' : ''}`;
});

onMounted(() => {
    timer = setInterval(() => {
        now.value = new Date();
    }, 60000); // Mise à jour toutes les minutes
});

onUnmounted(() => {
    if (timer) {
        clearInterval(timer);
    }
});
</script>
