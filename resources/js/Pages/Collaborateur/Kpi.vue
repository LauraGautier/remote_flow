<template>
    <AppLayout title="KPIs du collaborateur">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isManager ? `KPIs du collaborateur : ${collaborateur.name}` : 'Mes KPIs' }}
          </h2>
          <a :href="isManager ? route('collaborateur.kpi.pdf', collaborateur.id) : route('my.kpi.pdf')"
            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 focus:bg-gray-700 dark:focus:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
            target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exporter en PDF
            </a>
        </div>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Cartes de résumé -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total des tâches -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total des tâches</div>
              <div class="text-3xl font-bold mt-2 dark:text-white">{{ kpiData.totalTasks }}</div>
            </div>

            <!-- Tâches terminées -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">Tâches terminées</div>
              <div class="text-3xl font-bold mt-2 text-green-600 dark:text-green-400">{{ kpiData.completedTasks }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Taux de complétion: {{ kpiData.completionRate }}%
              </div>
            </div>

            <!-- Tâches en cours -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">Tâches en cours</div>
              <div class="text-3xl font-bold mt-2 text-blue-600 dark:text-blue-400">{{ kpiData.inProgressTasks }}</div>
            </div>

            <!-- Temps moyen par tâche -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <div class="text-lg font-semibold text-gray-700 dark:text-gray-300">Temps moyen par tâche</div>
              <div class="text-3xl font-bold mt-2 text-purple-600 dark:text-purple-400">
                {{ formatTime(kpiData.averageTimeMinutes) }}
              </div>
            </div>
          </div>

          <!-- Graphiques et tableaux -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Graphique d'évolution des tâches terminées -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Évolution des tâches terminées</h3>
              <weekly-chart :data="weeklyChartData" />
            </div>

            <!-- Distribution par équipe -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Tâches par équipe</h3>
              <team-distribution-chart :data="teamChartData" />
            </div>

            <!-- Dernières tâches terminées -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 col-span-1 lg:col-span-2">
              <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Dernières tâches terminées</h3>
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Titre</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Équipe</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date de fin</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Durée</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="task in kpiData.recentCompletedTasks" :key="task.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        <Link :href="route('tasks.show', task.id)">{{ task.title }}</Link>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-500 dark:text-gray-400">{{ task.team.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(task.end_time) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ formatTime(calculateDuration(task.start_time, task.end_time)) }}
                      </div>
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
  import { defineComponent } from 'vue';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { Link } from '@inertiajs/vue3';
  import WeeklyChart from '@/Components/Charts/WeeklyChart.vue';
  import TeamDistributionChart from '@/Components/Charts/TeamDistributionChart.vue';

  export default defineComponent({
    components: {
      AppLayout,
      Link,
      WeeklyChart,
      TeamDistributionChart
    },

    props: {
      collaborateur: Object,
      kpiData: Object,
      isManager: Boolean
    },

    computed: {
      weeklyChartData() {
        // Préparer les données pour le graphique hebdomadaire
        return {
          labels: this.kpiData.weeklyStats.map(stat => this.formatWeek(stat.week)),
          values: this.kpiData.weeklyStats.map(stat => stat.count)
        };
      },

      teamChartData() {
        // Préparer les données pour le graphique de distribution par équipe
        return {
          labels: this.kpiData.teamStats.map(stat => stat.team_name),
          values: this.kpiData.teamStats.map(stat => stat.count)
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
      }
    }
  });
  </script>
