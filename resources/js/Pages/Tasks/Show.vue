<template>
    <AppLayout :title="task.title">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Détails de la tâche
        </h2>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="mb-6">
              <Link
                :href="$page.props.auth.user.role === 'manager' ? route('manager.tasks') : route('collaborateur.tasks')"
                class="text-blue-600 dark:text-blue-400 hover:underline"
              >
                &larr; Retour à la liste
              </Link>
            </div>

            <div class="mb-6">
              <h1 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">{{ task.title }}</h1>

              <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400 mt-3">
                <div>
                  <span class="font-medium">Statut:</span>
                  <span
                    :class="{
                      'bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200': task.status === 'pending',
                      'bg-blue-200 dark:bg-blue-600 text-blue-800 dark:text-blue-200': task.status === 'in_progress',
                      'bg-green-200 dark:bg-green-600 text-green-800 dark:text-green-200': task.status === 'completed'
                    }"
                    class="ml-1 px-2 py-1 rounded"
                  >
                    {{ getStatusLabel(task.status) }}
                  </span>
                </div>
                <div>
                  <span class="font-medium">Assignée à:</span>
                  <span class="ml-1">{{ task.assigned_to.name }}</span>
                </div>
                <div>
                  <span class="font-medium">Créée par:</span>
                  <span class="ml-1">{{ task.creator.name }}</span>
                </div>
                <div v-if="task.start_time">
                  <span class="font-medium">Démarrée le:</span>
                  <span class="ml-1">{{ formatDate(task.start_time) }}</span>
                </div>
                <div v-if="task.end_time">
                  <span class="font-medium">Terminée le:</span>
                  <span class="ml-1">{{ formatDate(task.end_time) }}</span>
                </div>
                <div v-if="task.start_time && task.end_time">
                  <span class="font-medium">Durée:</span>
                  <span class="ml-1">{{ formatDuration(task) }}</span>
                </div>
              </div>
            </div>

            <div class="mb-6">
              <h2 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Description</h2>
              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                <p v-if="task.description" class="text-gray-800 dark:text-gray-200">{{ task.description }}</p>
                <p v-else class="text-gray-500 dark:text-gray-400 italic">Aucune description fournie</p>
              </div>
            </div>

            <div class="flex space-x-4" v-if="$page.props.user && task.assigned_to && $page.props.user.id === task.assigned_to.id">
              <Link
                v-if="task.status === 'pending'"
                :href="route('tasks.start', task.id)"
                method="post"
                as="button"
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors duration-150"
              >
                Démarrer la tâche
              </Link>
              <Link
                v-if="task.status !== 'completed'"
                :href="route('tasks.complete', task.id)"
                method="post"
                as="button"
                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors duration-150"
              >
                Marquer comme terminée
              </Link>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>

  <script>
  // Importation corrigée pour Laravel 11
  import { Link } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';

  export default {
    components: {
      AppLayout,
      Link,
    },

    props: {
      task: Object,
      canEdit: Boolean,
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

      formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('fr-FR', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
      },

      formatDuration(task) {
        if (task.start_time && task.end_time) {
          const startTime = new Date(task.start_time);
          const endTime = new Date(task.end_time);
          const durationMinutes = Math.floor((endTime - startTime) / (1000 * 60));

          if (durationMinutes < 60) {
            return `${durationMinutes} minutes`;
          } else {
            const hours = Math.floor(durationMinutes / 60);
            const minutes = durationMinutes % 60;
            return `${hours} heure${hours > 1 ? 's' : ''} ${minutes > 0 ? `et ${minutes} minute${minutes > 1 ? 's' : ''}` : ''}`;
          }
        }

        return '-';
      }
    }
  }
  </script>
