<template>
    <AppLayout title="Mon Tableau de Bord">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tableau de bord - {{ user.name }}
          </h2>
          <div class="flex space-x-2">
            <button @click="refreshData" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Actualiser
            </button>
          </div>
        </div>
      </template>

      <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Message de bienvenue et résumé journalier -->
          <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 mr-4">
                <div class="h-16 w-16 rounded-full bg-white/20 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
              <div>
                <p class="text-white/80">{{ greeting }}</p>
                <h3 class="text-2xl font-bold">{{ dashboardData.dayMessage }}</h3>
                <div class="flex mt-2">
                  <p class="text-white/90">{{ formatDate(new Date()) }}</p>
                  <span class="mx-2 text-white/60">•</span>
                  <p class="text-white/90">
                    <span v-if="dashboardData.tasksDueToday > 0">{{ dashboardData.tasksDueToday }} tâche{{ dashboardData.tasksDueToday > 1 ? 's' : '' }} pour aujourd'hui</span>
                    <span v-else>Pas de tâches urgentes pour aujourd'hui</span>
                  </p>
                </div>
              </div>
            </div>
          </div>

        <!-- Alerte de productivité (si des tâches sont en retard) -->
        <ProductivityAlert
            v-if="dashboardData.hasDelayedTasks"
            :tasks="dashboardData.delayedTasks"
            :threshold="dashboardData.alertThreshold"
          />

          <!-- Cartes de résumé -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Mes tâches -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-b-4 border-blue-500">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Mes tâches</div>
                  <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ dashboardData.totalTasks }}</div>
                </div>
              </div>
              <div class="mt-4">
                <Link :href="route('collaborateur.tasks')" class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                  Voir toutes mes tâches →
                </Link>
              </div>
            </div>

            <!-- Tâches en cours -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-b-4 border-yellow-500">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-400">En cours</div>
                  <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ dashboardData.inProgressTasks }}</div>
                </div>
              </div>
              <div class="mt-4">
                <Link :href="route('collaborateur.tasks', { status: 'in_progress' })" class="text-sm text-yellow-500 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300">
                  Voir les tâches en cours →
                </Link>
              </div>
            </div>

            <!-- Tâches terminées -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-b-4 border-green-500">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Terminées</div>
                  <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ dashboardData.completedTasks }}</div>
                </div>
              </div>
              <div class="mt-4">
                <Link :href="route('collaborateur.tasks', { status: 'completed' })" class="text-sm text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                  Voir les tâches terminées →
                </Link>
              </div>
            </div>

            <!-- Taux de complétion -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-b-4 border-purple-500">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Mon taux</div>
                  <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ dashboardData.completionRate }}%</div>
                </div>
              </div>
              <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div class="h-2.5 rounded-full" :class="getProgressBarColor(dashboardData.completionRate)" :style="{ width: `${dashboardData.completionRate}%` }"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Tâches à traiter en priorité -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tâches prioritaires</h3>
                <Link :href="route('collaborateur.tasks', { status: 'pending' })" class="text-sm text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                  Voir toutes →
                </Link>
              </div>
              <div class="space-y-4">
                <div v-for="task in dashboardData.priorityTasks" :key="task.id" class="border border-l-4 rounded-lg" :class="getPriorityBorderColor(task.daysPending)">
                  <Link :href="route('tasks.show', task.id)" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex justify-between">
                      <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ task.title }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                          Projet: {{ task.project }}
                        </p>
                      </div>
                      <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getPriorityBadgeColor(task.daysPending)">
                          {{ getPriorityLabel(task.daysPending) }}
                        </span>
                      </div>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                      <span class="text-sm text-gray-500 dark:text-gray-400">
                        En attente depuis {{ task.daysPending }} jour{{ task.daysPending > 1 ? 's' : '' }}
                      </span>
                      <button @click.prevent="startTask(task.id)" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium hover:bg-blue-200 dark:bg-blue-700 dark:text-blue-100 dark:hover:bg-blue-600">
                        Démarrer
                        <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                      </button>
                    </div>
                  </Link>
                </div>
                <div v-if="!dashboardData.priorityTasks.length" class="text-center py-6 text-gray-500 dark:text-gray-400">
                  Aucune tâche en attente 🎉
                </div>
              </div>
            </div>

            <!-- Tâches en cours -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tâches en cours</h3>
                <Link :href="route('collaborateur.tasks', { status: 'in_progress' })" class="text-sm text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                  Voir toutes →
                </Link>
              </div>
              <div class="space-y-4">
                <div v-for="task in dashboardData.inProgressTaskList" :key="task.id" class="border border-l-4 border-l-blue-500 rounded-lg">
                  <Link :href="route('tasks.show', task.id)" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex justify-between">
                      <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ task.title }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                          Projet: {{ task.project }}
                        </p>
                      </div>
                      <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100">
                          {{ formatDuration(task.duration) }}
                        </span>
                      </div>
                    </div>
                    <div class="mt-3 flex justify-between items-center">
                      <span class="text-sm text-gray-500 dark:text-gray-400">
                        Démarré {{ formatTimeAgo(task.startTime) }}
                      </span>
                      <button @click.prevent="completeTask(task.id)" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium hover:bg-green-200 dark:bg-green-700 dark:text-green-100 dark:hover:bg-green-600">
                        Terminer
                        <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </button>
                    </div>
                  </Link>
                </div>
                <div v-if="!dashboardData.inProgressTaskList.length" class="text-center py-6 text-gray-500 dark:text-gray-400">
                  Aucune tâche en cours actuellement
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Mes projets -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Mes projets</h3>
              </div>
              <div class="space-y-4">
                <div v-for="project in dashboardData.projects" :key="project.id" class="border rounded-lg">
                  <Link :href="route('projects.show', project.id)" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex justify-between items-start">
                      <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ project.name }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                          {{ project.description || 'Aucune description' }}
                        </p>
                      </div>
                      <div class="flex-shrink-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusBadgeColor(project.status)">
                          {{ getStatusLabel(project.status) }}
                        </span>
                      </div>
                    </div>
                    <div class="mt-3">
                      <div class="flex justify-between items-center text-sm mb-1">
                        <span class="text-gray-500 dark:text-gray-400">Progression</span>
                        <span class="font-medium">{{ project.tasksCompleted }} / {{ project.totalTasks }}</span>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full" :class="getProgressBarColor(project.completionRate)" :style="{ width: `${project.completionRate}%` }"></div>
                      </div>
                    </div>
                  </Link>
                </div>
                <div v-if="!dashboardData.projects.length" class="text-center py-6 text-gray-500 dark:text-gray-400">
                  Vous n'êtes assigné(e) à aucun projet pour le moment
                </div>
              </div>
            </div>

            <!-- Statistiques personnelles -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Mes statistiques</h3>

              <!-- Temps moyen par tâche -->
              <div class="mb-6">
                <div class="flex justify-between items-center mb-1">
                  <span class="text-sm text-gray-500 dark:text-gray-400">Temps moyen par tâche</span>
                  <span class="text-sm font-medium">{{ formatDuration(dashboardData.stats.averageTimePerTask) }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="bg-purple-500 h-2 rounded-full" :style="{ width: getAverageTimeBarWidth(dashboardData.stats.averageTimePerTask) }"></div>
                </div>
              </div>

              <!-- Tâches par jour -->
              <div class="mb-6">
                <div class="flex justify-between items-center mb-1">
                  <span class="text-sm text-gray-500 dark:text-gray-400">Tâches par jour (cette semaine)</span>
                  <span class="text-sm font-medium">{{ dashboardData.stats.tasksPerDay.toFixed(1) }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="bg-indigo-500 h-2 rounded-full" :style="{ width: `${Math.min(dashboardData.stats.tasksPerDay / 5 * 100, 100)}%` }"></div>
                </div>
              </div>

              <!-- Taux de complétion hebdomadaire -->
              <div class="mb-6">
                <div class="flex justify-between items-center mb-1">
                  <span class="text-sm text-gray-500 dark:text-gray-400">Taux de complétion (cette semaine)</span>
                  <span class="text-sm font-medium">{{ dashboardData.stats.weeklyCompletionRate }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="h-2 rounded-full" :class="getProgressBarColor(dashboardData.stats.weeklyCompletionRate)" :style="{ width: `${dashboardData.stats.weeklyCompletionRate}%` }"></div>
                </div>
              </div>

              <!-- Évolution du temps par tâche -->
              <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Évolution du temps par tâche (en minutes)</h4>
                <div class="h-48">
                  <performance-chart :data="dashboardData.performanceChartData" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <ProductivityAlertModal
      v-if="dashboardData.hasDelayedTasks"
      :tasks="dashboardData.delayedTasks"
      :threshold="dashboardData.alertThreshold"
      auto-show
    />
    </AppLayout>
  </template>

  <script>
  import { Link } from '@inertiajs/vue3';
  import { router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import PerformanceChart from '@/Components/Charts/PerformanceChart.vue';
  import ProductivityAlert from '@/Components/Collaborateur/ProductivityAlert.vue';
  import ProductivityAlertModal from '@/Components/Collaborateur/ProductivityAlertModal.vue';

  export default {
    components: {
      AppLayout,
      Link,
      PerformanceChart,
      ProductivityAlert,
      ProductivityAlertModal,
    },
    props: {
      user: Object,
      dashboardData: Object,
    },
    computed: {
      greeting() {
        const hour = new Date().getHours();
        if (hour < 12) return 'Bonjour';
        if (hour < 18) return 'Bon après-midi';
        return 'Bonsoir';
      }
    },
    methods: {
      refreshData() {
        // Reload the page or fetch new data
        window.location.reload();
      },
      formatDate(date) {
        return new Intl.DateTimeFormat('fr-FR', {
          weekday: 'long',
          day: 'numeric',
          month: 'long'
        }).format(date);
      },
      formatTimeAgo(timestamp) {
        const now = new Date();
        const date = new Date(timestamp);
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffDays > 0) {
          return `il y a ${diffDays} jour${diffDays > 1 ? 's' : ''}`;
        }
        if (diffHours > 0) {
          return `il y a ${diffHours} heure${diffHours > 1 ? 's' : ''}`;
        }
        if (diffMins > 0) {
          return `il y a ${diffMins} minute${diffMins > 1 ? 's' : ''}`;
        }
        return "à l'instant";
      },
      formatDuration(minutes) {
        if (!minutes) return '0min';

        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;

        if (hours > 0) {
          return `${hours}h ${mins > 0 ? mins + 'min' : ''}`;
        }
        return `${mins}min`;
      },
      getProgressBarColor(percentage) {
        if (percentage >= 80) return 'bg-green-500';
        if (percentage >= 50) return 'bg-yellow-500';
        return 'bg-red-500';
      },
      getStatusBadgeColor(status) {
        const colors = {
          'active': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
          'on_hold': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
          'completed': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        };
        return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
      },
      getStatusLabel(status) {
        const labels = {
          'active': 'Actif',
          'on_hold': 'En pause',
          'completed': 'Terminé',
        };
        return labels[status] || status;
      },
      getPriorityBorderColor(days) {
        if (days >= 7) return 'border-l-red-500';
        if (days >= 3) return 'border-l-orange-500';
        return 'border-l-yellow-500';
      },
      getPriorityBadgeColor(days) {
        if (days >= 7) return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        if (days >= 3) return 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400';
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
      },
      getPriorityLabel(days) {
        if (days >= 7) return 'Urgent';
        if (days >= 3) return 'Prioritaire';
        return 'À faire';
      },
      getAverageTimeBarWidth(minutes) {
        // Considère que 120 minutes (2h) est "normal", 100% après ça
        const percentage = Math.min((minutes / 120) * 100, 100);
        return `${percentage}%`;
      },
      startTask(taskId) {
        router.post(route('tasks.start', taskId), {}, {
          onSuccess: () => {
            // Reload ou mettre à jour l'interface
            this.refreshData();
          }
        });
      },
      completeTask(taskId) {
        router.post(route('tasks.complete', taskId), {}, {
          onSuccess: () => {
            // Reload ou mettre à jour l'interface
            this.refreshData();
          }
        });
      }
    }
  };
  </script>

  <style>
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  </style>
