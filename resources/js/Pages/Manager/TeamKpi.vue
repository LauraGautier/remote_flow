<template>
    <AppLayout title="KPIs de l'équipe">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            KPIs de l'équipe : {{ team.name }}
          </h2>
          <a
            :href="route('manager.team.kpi.pdf')"
            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            target="_blank"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exporter en PDF
          </a>
        </div>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Cartes de résumé pour l'équipe -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total des tâches -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total des tâches</div>
              <div class="text-3xl font-bold mt-2 dark:text-white">{{ teamKpiData.totalTasks }}</div>
            </div>

            <!-- Tâches terminées -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">Tâches terminées</div>
              <div class="text-3xl font-bold mt-2 text-green-600 dark:text-green-400">{{ teamKpiData.completedTasks }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Taux de complétion: {{ teamKpiData.completionRate }}%
              </div>
            </div>

            <!-- Tâches en cours -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">Tâches en cours</div>
              <div class="text-3xl font-bold mt-2 text-blue-600 dark:text-blue-400">{{ teamKpiData.inProgressTasks }}</div>
            </div>

            <!-- Tâches en attente -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">Tâches en attente</div>
              <div class="text-3xl font-bold mt-2 text-orange-600 dark:text-orange-400">{{ teamKpiData.pendingTasks }}</div>
            </div>
          </div>

          <!-- Graphique d'évolution des tâches terminées -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Évolution des tâches terminées</h3>
            <weekly-chart :data="weeklyChartData" />
          </div>

          <!-- Tableau des KPIs par collaborateur -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Performance des collaborateurs</h3>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tâches totales</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tâches terminées</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Taux</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Temps moyen</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="kpi in collaborateurKpis" :key="kpi.collaborateur.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ kpi.collaborateur.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ kpi.collaborateur.email }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ kpi.totalTasks }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ kpi.completedTasks }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium" :class="getCompletionRateColor(kpi.completionRate)">
                      {{ kpi.completionRate }}%
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ formatTime(kpi.averageTimeMinutes) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <Link :href="route('collaborateur.kpi', kpi.collaborateur.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                      Voir détails
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Dernières tâches terminées -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Dernières tâches terminées</h3>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Titre</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Assigné à</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date de fin</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Durée</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="task in teamKpiData.recentCompletedTasks" :key="task.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ task.title }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ task.assigned_to ? task.assigned_to.name : 'Non assigné' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(task.end_time) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ formatTime(calculateDuration(task.start_time, task.end_time)) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <Link :href="route('tasks.show', task.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                      Voir détails
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

            <!-- Derniers objectifs atteints -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Derniers objectifs atteints</h3>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Titre</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Projet</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Créé par</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date d'achèvement</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="objective in teamKpiData.recentCompletedObjectives" :key="objective.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ objective.title }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ objective.project ? objective.project.name : 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ objective.creator ? objective.creator.name : 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(objective.completed_at) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                    <Link :href="route('projects.show', objective.project_id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                        Voir projet
                    </Link>
                    </td>
                </tr>
                </tbody>
            </table>
            </div>

            <!-- Performance des projets -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Performance des projets</h3>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Projet</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Objectifs totaux</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Objectifs atteints</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Taux de complétion</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="project in teamKpiData.projectsObjectivesStats" :key="project.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ project.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ project.totalObjectives }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ project.completedObjectives }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium" :class="getCompletionRateColor(project.completionRate)">
                        {{ project.completionRate }}%
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                        <div class="h-2 rounded-full"
                            :class="getProgressBarColor(project.completionRate)"
                            :style="{ width: `${project.completionRate}%` }">
                        </div>
                    </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                    <Link :href="route('projects.show', project.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                        Voir détails
                    </Link>
                    </td>
                </tr>
                </tbody>
            </table>
            </div>

        </div>
      </div>
    </AppLayout>
  </template>

  <script>
  import { defineComponent } from 'vue';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { Link } from '@inertiajs/vue3';
  import WeeklyChart from '@/Components/Charts/WeeklyChart.vue';

  export default defineComponent({
    components: {
      AppLayout,
      Link,
      WeeklyChart
    },

    props: {
      team: Object,
      collaborateurKpis: Array,
      teamKpiData: Object
    },

    computed: {
      weeklyChartData() {
        // Préparer les données pour le graphique hebdomadaire
        return {
          labels: this.teamKpiData.weeklyStats.map(stat => this.formatWeek(stat.week)),
          values: this.teamKpiData.weeklyStats.map(stat => stat.count)
        };
      },

      weeklyObjectivesChartData() {
        // Préparer les données pour le graphique hebdomadaire des objectifs
        return {
        labels: this.teamKpiData.weeklyObjectivesStats.map(stat => this.formatWeek(stat.week)),
        values: this.teamKpiData.weeklyObjectivesStats.map(stat => stat.count)
        };
    }
},

    methods: {
      formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('fr-FR', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        }).format(date);
      },

      formatTime(minutes) {
        if (!minutes) return 'N/A';
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return hours > 0 ? `${hours}h ${mins}min` : `${mins}min`;
      },

      calculateDuration(startTime, endTime) {
        if (!startTime || !endTime) return null;
        const start = new Date(startTime);
        const end = new Date(endTime);
        return Math.round((end - start) / (1000 * 60)); // En minutes
      },

      formatWeek(weekNumber) {
        // Convertit un numéro de semaine YEARWEEK en format lisible
        const year = Math.floor(weekNumber / 100);
        const week = weekNumber % 100;
        return `S${week}/${year}`;
      },

      getCompletionRateColor(rate) {
        if (rate >= 80) return 'text-green-600 dark:text-green-400';
        if (rate >= 50) return 'text-yellow-600 dark:text-yellow-400';
        return 'text-red-600 dark:text-red-400';
      },

      getProgressBarColor(rate) {
        if (rate >= 80) return 'bg-green-500';
        if (rate >= 50) return 'bg-yellow-500';
        return 'bg-red-500';
      },
    }
  });
  </script>
