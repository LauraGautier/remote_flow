<template>
    <AppLayout title="Tableau de bord Manager">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tableau de bord de l'équipe {{ team.name }}
          </h2>
          <div class="flex space-x-2">
            <Link :href="route('manager.team.kpi')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
              KPIs détaillés
            </Link>
            <button @click="refreshData" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
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
          <!-- Vue d'ensemble -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Projets actifs -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-blue-500">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Projets actifs</div>
                  <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ dashboardData.activeProjects }}</div>
                </div>
              </div>
              <div class="mt-4">
                <Link :href="route('projects.index')" class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                  Voir tous les projets →
                </Link>
              </div>
            </div>

            <!-- Tâches en cours -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-yellow-500">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Tâches en cours</div>
                  <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ dashboardData.inProgressTasks }}</div>
                </div>
              </div>
              <div class="mt-4">
                <Link :href="route('manager.tasks')" class="text-sm text-yellow-500 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300">
                  Voir toutes les tâches →
                </Link>
              </div>
            </div>

            <!-- Objectifs à atteindre -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-purple-500">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Objectifs à atteindre</div>
                  <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ dashboardData.pendingObjectives }}</div>
                </div>
              </div>
              <div class="mt-4">
                <Link :href="route('projects.index')" class="text-sm text-purple-500 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300">
                  Voir tous les objectifs →
                </Link>
              </div>
            </div>

            <!-- Taux de réussite -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-green-500">
              <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </div>
                <div>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Taux de réussite</div>
                  <div class="text-2xl font-semibold text-gray-900 dark:text-white">{{ dashboardData.completionRate }}%</div>
                </div>
              </div>
              <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="bg-green-500 h-2 rounded-full" :style="{ width: `${dashboardData.completionRate}%` }"></div>
                </div>
              </div>
            </div>
          </div>

          <presence-section :presenceData="dashboardData.presenceData" />

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Projets récents -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Projets récents</h3>
                <Link :href="route('projects.create')" class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  Nouveau projet
                </Link>
              </div>
              <div class="space-y-4">
                <div v-for="project in dashboardData.recentProjects" :key="project.id" class="border-l-4 pl-4" :class="getBorderColor(project.status)">
                  <Link :href="route('projects.show', project.id)" class="block hover:bg-gray-50 dark:hover:bg-gray-700 -ml-4 pl-4 py-2">
                    <div class="flex justify-between items-start">
                      <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ project.name }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusBadgeColor(project.status)">
                            {{ getStatusLabel(project.status) }}
                          </span>
                          <span class="ml-2">{{ project.objectivesStats.completed }} / {{ project.objectivesStats.total }} objectifs</span>
                        </p>
                      </div>
                      <div class="flex-shrink-0">
                        <div class="relative h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                          <span class="text-xs font-medium">{{ getProjectInitials(project.name) }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                      <div class="h-1.5 rounded-full" :class="getProgressBarColor(project.objectivesStats.percentage)" :style="{ width: `${project.objectivesStats.percentage}%` }"></div>
                    </div>
                  </Link>
                </div>
                <div v-if="!dashboardData.recentProjects.length" class="text-center py-4 text-gray-500 dark:text-gray-400">
                  Aucun projet récent à afficher
                </div>
              </div>
            </div>

            <!-- Activité récente -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Activité récente</h3>
                <div class="text-sm text-gray-500 dark:text-gray-400">Dernières 24 heures</div>
              </div>
              <div class="relative">
                <div class="border-l-2 border-gray-200 dark:border-gray-700 ml-3 py-2">
                  <div v-for="(activity, index) in dashboardData.recentActivities" :key="index" class="relative mb-6 ml-6">
                    <div class="absolute -left-10 mt-1.5">
                      <div class="h-5 w-5 rounded-full" :class="getActivityIconColor(activity.type)"></div>
                    </div>
                    <div>
                      <p class="text-sm text-gray-900 dark:text-white font-medium">{{ activity.title }}</p>
                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ activity.description }}</p>
                      <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ formatTime(activity.time) }}</p>
                    </div>
                  </div>
                  <div v-if="!dashboardData.recentActivities.length" class="text-center py-4 text-gray-500 dark:text-gray-400 ml-3">
                    Aucune activité récente à afficher
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Performance des collaborateurs -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Performance des collaborateurs</h3>
              <div class="space-y-4">
                <div v-for="member in dashboardData.teamPerformance" :key="member.id" class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="relative h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                      <span class="text-sm font-medium">{{ getMemberInitials(member.name) }}</span>
                    </div>
                  </div>
                  <div class="ml-4 flex-grow">
                    <div class="flex justify-between mb-1">
                      <span class="text-sm font-medium text-gray-900 dark:text-white">{{ member.name }}</span>
                      <span class="text-sm font-medium" :class="getCompletionTextColor(member.completionRate)">{{ member.completionRate }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div class="h-2 rounded-full" :class="getProgressBarColor(member.completionRate)" :style="{ width: `${member.completionRate}%` }"></div>
                    </div>
                  </div>
                </div>
                <div v-if="!dashboardData.teamPerformance.length" class="text-center py-4 text-gray-500 dark:text-gray-400">
                  Aucune donnée de performance disponible
                </div>
              </div>
              <div class="mt-4">
                <Link :href="route('manager.team.kpi')" class="text-sm text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                  Voir les statistiques détaillées →
                </Link>
              </div>
            </div>

            <!-- Tâches urgentes -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tâches urgentes</h3>
              <div class="space-y-3">
                <div v-for="task in dashboardData.urgentTasks" :key="task.id" class="flex items-start p-3 rounded-lg" :class="{'bg-red-50 dark:bg-red-900/20': task.isVeryUrgent, 'bg-orange-50 dark:bg-orange-900/20': !task.isVeryUrgent}">
                  <div class="flex-shrink-0">
                    <div class="p-2 rounded-full" :class="task.isVeryUrgent ? 'bg-red-100 dark:bg-red-900/30 text-red-500' : 'bg-orange-100 dark:bg-orange-900/30 text-orange-500'">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                  </div>
                  <div class="ml-3 flex-grow">
                    <Link :href="route('tasks.show', task.id)" class="block">
                      <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ task.title }}</h4>
                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Assignée à: {{ task.assignedTo }}</p>
                      <p class="text-xs font-medium mt-1" :class="task.isVeryUrgent ? 'text-red-500' : 'text-orange-500'">
                        {{ task.dueDate }}
                      </p>
                    </Link>
                  </div>
                </div>
                <div v-if="!dashboardData.urgentTasks.length" class="text-center py-4 text-gray-500 dark:text-gray-400">
                  Aucune tâche urgente à afficher
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>

  <script>
  import { Link } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import PresenceSection from '../../Components/Dashboard/PresenceSection.vue';

  export default {
    components: {
      AppLayout,
      Link,
      PresenceSection,
    },
    props: {
      team: Object,
      dashboardData: Object,
    },
    methods: {
      refreshData() {
        // Reload the page or fetch new data
        window.location.reload();
      },
      getProjectInitials(name) {
        return name
          .split(' ')
          .map(word => word[0])
          .join('')
          .substring(0, 2)
          .toUpperCase();
      },
      getMemberInitials(name) {
        return name
          .split(' ')
          .map(word => word[0])
          .join('')
          .substring(0, 2)
          .toUpperCase();
      },
      getBorderColor(status) {
        const colors = {
          'active': 'border-green-500',
          'on_hold': 'border-yellow-500',
          'completed': 'border-blue-500',
        };
        return colors[status] || 'border-gray-300';
      },
      getStatusLabel(status) {
        const labels = {
          'active': 'Actif',
          'on_hold': 'En pause',
          'completed': 'Terminé',
        };
        return labels[status] || status;
      },
      getStatusBadgeColor(status) {
        const colors = {
          'active': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
          'on_hold': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
          'completed': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        };
        return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
      },
      getProgressBarColor(percentage) {
        if (percentage >= 80) return 'bg-green-500';
        if (percentage >= 50) return 'bg-yellow-500';
        return 'bg-red-500';
      },
      getCompletionTextColor(percentage) {
        if (percentage >= 80) return 'text-green-500 dark:text-green-400';
        if (percentage >= 50) return 'text-yellow-500 dark:text-yellow-400';
        return 'text-red-500 dark:text-red-400';
      },
      getActivityIconColor(type) {
        const colors = {
          'task_completed': 'bg-green-500',
          'task_created': 'bg-blue-500',
          'objective_completed': 'bg-purple-500',
          'project_created': 'bg-indigo-500',
        };
        return colors[type] || 'bg-gray-500';
      },
      formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      },
    },
  };
  </script>
