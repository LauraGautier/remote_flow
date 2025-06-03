<!--
  Alerte de productivité à afficher directement dans le dashboard (version Tailwind)
  À enregistrer dans: resources/js/Components/Collaborateur/ProductivityAlert.vue
-->
<template>
    <div v-if="show" class="mb-6">
      <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r shadow-md">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <div class="ml-3 flex-1">
            <h3 class="text-lg font-medium text-yellow-800">
              Attention à votre productivité !
            </h3>
            <div class="mt-2 text-yellow-700">
              <p>
                Vous avez <span class="font-bold">{{ tasks.length }}</span> tâche{{ tasks.length > 1 ? 's' : '' }} en cours depuis plus de {{ threshold }} jours.
              </p>

              <div class="mt-3 max-h-48 overflow-y-auto space-y-2 border-l-2 border-yellow-200 pl-3">
                <div v-for="task in tasks" :key="task.id" class="flex justify-between items-center">
                  <div>
                    <a :href="route('tasks.show', task.id)" class="text-yellow-800 hover:text-yellow-900 font-medium">
                      {{ task.title }}
                    </a>
                    <span v-if="task.project" class="text-sm text-yellow-600 ml-2">
                      ({{ task.project.name }})
                    </span>
                  </div>
                  <div class="text-red-600 font-bold">
                    {{ task.days_running }} jour{{ task.days_running > 1 ? 's' : '' }}
                  </div>
                </div>
              </div>

              <div class="mt-4 flex space-x-3">
                <a
                  :href="route('collaborateur.tasks')"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                >
                  Voir mes tâches
                </a>
                <button
                  @click="closeAlert"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                  J'ai compris
                </button>
              </div>
            </div>
          </div>

          <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
              <button
                @click="closeAlert"
                class="inline-flex bg-yellow-50 p-1.5 text-yellow-500 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 rounded-md"
              >
                <span class="sr-only">Fermer</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, defineProps } from 'vue';

  const props = defineProps({
    tasks: {
      type: Array,
      required: true
    },
    threshold: {
      type: Number,
      default: 5
    }
  });

  const show = ref(true);

  const closeAlert = () => {
    show.value = false;
    // Stocker dans localStorage pour ne pas montrer à nouveau pendant X heures
    localStorage.setItem('productivity_alert_dismissed', Date.now().toString());
  };

  // Option: Vérifier si l'alerte a été fermée récemment
  // (décommenter si vous souhaitez cette fonctionnalité)
  /*
  import { onMounted } from 'vue';

  onMounted(() => {
    const lastDismissed = localStorage.getItem('productivity_alert_dismissed');
    if (lastDismissed) {
      const hoursSinceDismissed = (Date.now() - parseInt(lastDismissed)) / (1000 * 60 * 60);
      if (hoursSinceDismissed < 8) { // Ne pas montrer si fermé il y a moins de 8h
        show.value = false;
      }
    }
  });
  */
  </script>
