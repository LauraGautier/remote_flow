<script setup>
import { ref, watch, onMounted } from 'vue';

const isDark = ref(false);

// Fonction pour basculer entre les modes sombre et clair
const toggleDarkMode = () => {
  isDark.value = !isDark.value;

  if (isDark.value) {
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  } else {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme', 'light');
  }
};

// Initialisation du thème lors du chargement de la page
onMounted(() => {
  // Vérifier si un thème est enregistré dans le localStorage
  const theme = localStorage.getItem('theme');

  // Si un thème est défini dans le localStorage, l'appliquer
  if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true;
    document.documentElement.classList.add('dark');
  } else {
    isDark.value = false;
    document.documentElement.classList.remove('dark');
  }
});
</script>

<template>
  <button
    @click="toggleDarkMode"
    class="p-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors flex items-center space-x-2"
    aria-label="Toggle dark mode"
  >
    <template v-if="isDark">
      <svg class="w-5 h-5 text-gray-800 dark:text-gray-200" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
      </svg>
      <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Light Mode</span>
    </template>
    <template v-else>
      <svg class="w-5 h-5 text-gray-800 dark:text-gray-200" fill="currentColor" viewBox="0 0 20 20">
        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
      </svg>
      <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Dark Mode</span>
    </template>
  </button>
</template>

<style>
:root {
  --transition-duration: 0.5s;
}

* {
  transition: background-color var(--transition-duration) ease,
              color var(--transition-duration) ease,
              border-color var(--transition-duration) ease,
              box-shadow var(--transition-duration) ease;
}

body {
  transition: background-color var(--transition-duration) ease;
}

img, svg {
  transition: filter var(--transition-duration) ease;
}

.no-transition {
  transition: none !important;
}
</style>
