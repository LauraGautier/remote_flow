<template>
    <footer class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center space-y-4">
                <!-- Liens de navigation -->
                <nav class="flex flex-wrap justify-center gap-x-8 gap-y-2">
                    <Link :href="route('terms.show')" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                        Conditions d'utilisation
                    </Link>
                    <Link :href="route('policy.show')" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                        Politique de confidentialité
                    </Link>
                </nav>

                <!-- Badge SSL -->
                <div class="flex justify-center">
                    <img src="https://www.positivessl.com/images/seals/positivessl_trust_seal_sm_124x32.png"
                         alt="SSL Certificate"
                         class="h-8 opacity-80 hover:opacity-100 transition-opacity">
                </div>

                <!-- Copyright -->
                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                    <p>&copy; {{ currentYear }} {{ appName }}. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const currentYear = computed(() => new Date().getFullYear());
const appName = computed(() => import.meta.env.VITE_APP_NAME || 'Mon Application');
</script>
