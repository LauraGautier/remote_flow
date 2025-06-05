<template>
    <AppLayout title="Slack">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    <i class="fab fa-slack me-2 text-green-500"></i>
                    Bienvenue sur votre canal Slack
                </h2>
                <div class="flex items-center space-x-2">
                    <span v-if="hasWebhookUrl" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                        <i class="fas fa-check-circle me-1"></i>
                        Connecté
                    </span>
                    <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Déconnecté
                    </span>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Envoi de message -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600">
                        <h3 class="text-lg font-medium text-white">
                            <i class="fas fa-paper-plane me-2"></i>
                            Envoyer un message rapide
                        </h3>
                    </div>
                    <div class="p-6">
                        <form @submit.prevent="sendMessage" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Auteur du message
                                </label>
                                <div class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-600 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 font-medium">
                                    <i class="fas fa-user me-2 text-gray-500"></i>
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Votre identité est automatiquement attachée au message
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Canal (optionnel)
                                </label>
                                <input
                                    v-model="messageForm.channel"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Laissez vide pour utiliser #general
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Message *
                                </label>
                                <textarea
                                    v-model="messageForm.message"
                                    rows="4"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Tapez votre message ici..."
                                    required
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition disabled:opacity-50"
                                :disabled="sending || !hasWebhookUrl"
                            >
                                <span v-if="sending">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Envoi en cours...
                                </span>
                                <span v-else>
                                    <i class="fab fa-slack me-2"></i>
                                    Envoyer sur Slack
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Statut de l'intégration -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600">
                        <h3 class="text-lg font-medium text-white">
                            <i class="fas fa-info-circle me-2"></i>
                            Intégration Slack
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center py-6">
                            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                                <i class="fab fa-slack text-2xl text-green-500"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                Slack est intégré à votre application !
                            </h4>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Les notifications sont automatiquement envoyées lors des événements importants.
                            </p>

                            <div class="text-center max-w-md mx-auto">
                                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 inline-flex mb-3">
                                    <i class="fas fa-robot text-blue-500"></i>
                                </div>
                                <h5 class="font-medium text-gray-900 dark:text-white mb-2">Notifications automatiques</h5>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>✅ Nouvelle tâche assignée</li>
                                    <li>✅ Tâche terminée</li>
                                    <li>✅ Nouveau projet créé</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive } from 'vue';
import axios from 'axios';

export default {
    components: {
        AppLayout,
    },

    props: {
        defaultChannel: String,
        team: Object,
        hasWebhookUrl: Boolean,
        user: Object,
    },

    setup(props) {
        const sending = ref(false);

        const messageForm = reactive({
            channel: '',
            message: '',
        });

        const showNotification = (message, type = 'success') => {
            // Créer une notification simple style Tailwind
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg transition-all duration-300 ${
                type === 'success'
                    ? 'bg-green-500 text-white'
                    : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Animation d'apparition
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.opacity = '1';
            }, 10);

            // Suppression après 3 secondes
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        };

        const sendMessage = async () => {
            if (!props.hasWebhookUrl) {
                showNotification('Webhook Slack non configuré', 'error');
                return;
            }

            sending.value = true;

            try {
                const response = await axios.post('/slack/send-message', {
                    channel: messageForm.channel,
                    message: messageForm.message,
                });

                if (response.data.success) {
                    showNotification(response.data.message);
                    messageForm.message = '';
                    messageForm.channel = '';
                } else {
                    showNotification(response.data.message, 'error');
                }
            } catch (error) {
                showNotification('Erreur lors de l\'envoi du message', 'error');
                console.error(error);
            } finally {
                sending.value = false;
            }
        };

        return {
            messageForm,
            sending,
            sendMessage,
        };
    },
};
</script>

<style scoped>
/* Styles pour les notifications */
.notification-enter-active,
.notification-leave-active {
    transition: all 0.3s ease;
}
.notification-enter-from {
    transform: translateX(100%);
    opacity: 0;
}
.notification-leave-to {
    transform: translateX(100%);
    opacity: 0;
}
</style>
