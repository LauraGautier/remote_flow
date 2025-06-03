<template>
    <AppLayout title="Mes Tâches">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Mes Tâches
        </h2>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div v-if="tasks.length === 0" class="text-center py-8">
              <p class="text-gray-600 dark:text-gray-400">Vous n'avez aucune tâche assignée.</p>
            </div>

            <div v-else class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="border-b dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-gray-800 dark:text-gray-200">Titre</th>
                    <th class="px-4 py-3 text-left text-gray-800 dark:text-gray-200">Équipe</th>
                    <th class="px-4 py-3 text-left text-gray-800 dark:text-gray-200">Statut</th>
                    <th class="px-4 py-3 text-left text-gray-800 dark:text-gray-200">Durée</th>
                    <th class="px-4 py-3 text-left text-gray-800 dark:text-gray-200">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="task in tasks" :key="task.id" class="border-b dark:border-gray-700">
                    <td class="px-4 py-3">
                      <Link :href="route('tasks.show', task.id)" class="text-blue-600 dark:text-blue-400 hover:underline">
                        {{ task.title }}
                      </Link>
                    </td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                      {{ task.team ? task.team.name : 'N/A' }}
                    </td>
                    <td class="px-4 py-3">
                      <span
                        :class="{
                          'bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200': task.status === 'pending',
                          'bg-blue-200 dark:bg-blue-600 text-blue-800 dark:text-blue-200': task.status === 'in_progress',
                          'bg-green-200 dark:bg-green-600 text-green-800 dark:text-green-200': task.status === 'completed'
                        }"
                        class="px-2 py-1 rounded text-sm"
                      >
                        {{ getStatusLabel(task.status) }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ formatDuration(task) }}</td>
                    <td class="px-4 py-3">
                      <Link
                        v-if="task.status === 'pending'"
                        :href="route('tasks.start', task.id)"
                        method="post"
                        as="button"
                        class="mr-2 px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm transition-colors duration-150"
                      >
                        Démarrer
                      </Link>
                      <Link
                        v-if="task.status !== 'completed'"
                        :href="route('tasks.complete', task.id)"
                        method="post"
                        as="button"
                        class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-sm transition-colors duration-150"
                      >
                        Terminer
                      </Link>
                      <Link
                        :href="route('tasks.show', task.id)"
                        class="ml-2 px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded text-sm transition-colors duration-150"
                      >
                        Détails
                      </Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>

  <script>
  // Importation pour Laravel 11
  import { Link } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';

  export default {
    components: {
      AppLayout,
      Link,
    },

    props: {
      tasks: Array,
      teams: Array
    },

    methods: {
      getStatusLabel(status) {
        const labels = {
          'pending': 'En attente',
          'in_progress': 'En cours',
          'completed': 'Terminée'
        };
        return labels[status] || status;
      },

      formatDuration(task) {
        if (task.status === 'pending') {
          return 'Non démarrée';
        }

        if (task.status === 'in_progress') {
          return 'En cours...';
        }

        if (task.start_time && task.end_time) {
          // Calculer la durée en minutes
          const startTime = new Date(task.start_time);
          const endTime = new Date(task.end_time);
          const durationMinutes = Math.floor((endTime - startTime) / (1000 * 60));

          if (durationMinutes < 60) {
            return `${durationMinutes} min`;
          } else {
            const hours = Math.floor(durationMinutes / 60);
            const minutes = durationMinutes % 60;
            return `${hours}h ${minutes}min`;
          }
        }

        return '-';
      }
    }
  }
  </script>
