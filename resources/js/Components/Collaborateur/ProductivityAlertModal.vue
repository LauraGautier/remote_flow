<!--
  Modal d'alerte de productivité qui s'affiche automatiquement (version Tailwind)
  À enregistrer dans: resources/js/Components/Collaborateur/ProductivityAlertModal.vue
-->
<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <!-- Overlay -->
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
          @click="closeModal"
        ></div>

        <!-- Modal -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <!-- Header -->
          <div class="bg-yellow-500 px-4 py-3 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              Attention à votre productivité
            </h3>
            <button
              @click="closeModal"
              class="text-white hover:text-gray-200 focus:outline-none"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>

          <!-- Body -->
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <p class="text-lg font-medium mb-3">
              Vous avez <span class="font-bold">{{ tasks.length }}</span> tâche{{ tasks.length > 1 ? 's' : '' }} en cours depuis plus de {{ threshold }} jours.
            </p>

            <div class="mt-4 space-y-3 max-h-60 overflow-y-auto">
              <div
                v-for="task in tasks"
                :key="task.id"
                class="border-l-4 border-yellow-400 pl-3 py-2 bg-yellow-50 rounded-r"
              >
                <div class="flex justify-between">
                  <div>
                    <p class="font-semibold">{{ task.title }}</p>
                    <p v-if="task.project" class="text-sm text-gray-500">Projet: {{ task.project.name }}</p>
                  </div>
                  <div class="text-red-600 font-bold">
                    {{ task.days_running }} jour{{ task.days_running > 1 ? 's' : '' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-4 bg-gray-50 p-3 text-sm text-gray-600 rounded">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Les tâches qui restent trop longtemps en cours peuvent impacter la productivité globale de l'équipe.
            </div>
          </div>

          <!-- Footer -->
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <a
              :href="route('collaborateur.tasks')"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-500 text-base font-medium text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
              Voir mes tâches
            </a>
            <button
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              @click="closeModal"
            >
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, onMounted, defineProps, defineExpose } from 'vue';

  const props = defineProps({
    tasks: {
      type: Array,
      required: true
    },
    threshold: {
      type: Number,
      default: 5
    },
    autoShow: {
      type: Boolean,
      default: true
    }
  });

  const isOpen = ref(false);

  onMounted(() => {
    // Si autoShow est true et qu'il y a des tâches en retard, afficher automatiquement
    if (props.autoShow && props.tasks.length > 0) {
      // Vérifier si l'alerte a déjà été montrée récemment
      const lastShown = localStorage.getItem('productivity_alert_shown');
      if (lastShown) {
        const hoursSinceShown = (Date.now() - parseInt(lastShown)) / (1000 * 60 * 60);
        // Ne pas montrer si déjà vu dans les 4 dernières heures
        if (hoursSinceShown < 4) {
          return;
        }
      }

      // Montrer après un court délai pour permettre au reste de la page de se charger
      setTimeout(() => {
        isOpen.value = true;
        localStorage.setItem('productivity_alert_shown', Date.now().toString());
      }, 500);
    }
  });

  const openModal = () => {
    isOpen.value = true;
  };

  const closeModal = () => {
    isOpen.value = false;
  };

  // Exposer des méthodes pour permettre le contrôle depuis l'extérieur
  defineExpose({
    open: openModal,
    close: closeModal
  });
  </script>
