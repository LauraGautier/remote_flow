<!-- Section de présence pour le dashboard du manager (Tailwind CSS) -->
<template>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Présence des collaborateurs (7 derniers jours)</h3>
        <div class="text-sm text-gray-500 dark:text-gray-400">
          Maximum 10h/jour • 44h/semaine
        </div>
      </div>

      <!-- Statistiques de présence -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Moyenne quotidienne équipe</div>
          <div class="text-2xl font-semibold mt-1" :class="getDailyAverageClass(averageHours)">
            {{ averageHours }}h
          </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total heures équipe</div>
          <div class="text-2xl font-semibold mt-1" :class="getWeeklyTotalClass(totalHours)">
            {{ totalHours }}h
          </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Collaborateurs présents</div>
          <div class="text-2xl font-semibold mt-1 text-gray-900 dark:text-white">
            {{ activeUsers }}/{{ totalUsers }}
          </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Conformité équipe</div>
          <div class="text-2xl font-semibold mt-1" :class="getComplianceClass()">
            {{ getCompliancePercentage() }}%
          </div>
        </div>
      </div>

      <!-- Tableau résumé -->
      <div v-if="presenceData.summary && presenceData.summary.length > 0">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Collaborateur
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Total semaine
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Moyenne/jour
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Conformité
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Statut
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="user in topCollaborateurs" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-3 py-2 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                      <span class="text-xs font-medium">{{ getMemberInitials(user.name) }}</span>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  <div class="text-sm font-medium" :class="getWeeklyHoursClass(user.totalHours)">
                    {{ capWeeklyHours(user.totalHours) }}h
                  </div>
                  <div class="text-xs" :class="getWeeklyHoursClass(user.totalHours)">
                    {{ getWeeklyPercentage(user.totalHours) }}%
                  </div>
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  <div class="text-sm font-medium" :class="getDailyAverageClass(user.averageDailyHours)">
                    {{ capDailyHours(user.averageDailyHours) }}h
                  </div>
                  <div class="text-xs" :class="getDailyAverageClass(user.averageDailyHours)">
                    {{ getDailyPercentage(user.averageDailyHours) }}%
                  </div>
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div
                      class="h-2 rounded-full transition-all duration-300"
                      :class="getProgressBarClass(user.totalHours, user.averageDailyHours)"
                      :style="{ width: Math.min(100, getComplianceScore(user.totalHours, user.averageDailyHours)) + '%' }"
                    ></div>
                  </div>
                  <div class="text-xs mt-1" :class="getUserComplianceClass(user.totalHours, user.averageDailyHours)">
                    {{ getComplianceScore(user.totalHours, user.averageDailyHours) }}%
                  </div>
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusBadge(user.totalHours, user.averageDailyHours)">
                    {{ getStatusText(user.totalHours, user.averageDailyHours) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Légende -->
        <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400">
          <div class="flex items-center">
            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
            <span>Excellent</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
            <span>Moyen</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
            <span>Insuffisant</span>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-4 text-gray-500 dark:text-gray-400">
        Aucune donnée de présence disponible pour cette période.
      </div>
    </div>
  </template>

  <script>
  import { computed } from 'vue';
  import { Link } from '@inertiajs/vue3';

  export default {
    components: {
      Link
    },

    props: {
      presenceData: {
        type: Object,
        required: true
      }
    },

    setup(props) {
      // Constantes pour les limites
      const DAILY_LIMIT = 10; // heures par jour
      const WEEKLY_LIMIT = 44; // heures par semaine

      // Nombre total d'utilisateurs
      const totalUsers = computed(() => {
        return props.presenceData.summary ? props.presenceData.summary.length : 0;
      });

      // Nombre d'utilisateurs actifs (ayant au moins une connexion)
      const activeUsers = computed(() => {
        return props.presenceData.summary
          ? props.presenceData.summary.filter(user => parseFloat(user.totalHours) > 0).length
          : 0;
      });

      // Total des heures pour l'équipe (plafonné)
      const totalHours = computed(() => {
        if (!props.presenceData.summary || props.presenceData.summary.length === 0) {
          return '0.0';
        }

        return props.presenceData.summary
          .reduce((sum, user) => sum + Math.min(parseFloat(user.totalHours), WEEKLY_LIMIT), 0)
          .toFixed(1);
      });

      // Moyenne des heures quotidiennes pour l'équipe (plafonnée)
      const averageHours = computed(() => {
        if (!props.presenceData.summary || props.presenceData.summary.length === 0) {
          return '0.0';
        }

        const totalCappedAvg = props.presenceData.summary
          .reduce((sum, user) => sum + Math.min(parseFloat(user.averageDailyHours), DAILY_LIMIT), 0);

        return (totalCappedAvg / props.presenceData.summary.length).toFixed(1);
      });

      // Top 5 des collaborateurs par heures (pour le tableau)
      const topCollaborateurs = computed(() => {
        if (!props.presenceData.summary) {
          return [];
        }

        return [...props.presenceData.summary]
          .sort((a, b) => parseFloat(b.totalHours) - parseFloat(a.totalHours))
          .slice(0, 5);
      });

      // Fonctions utilitaires
      const getMemberInitials = (name) => {
        return name
          .split(' ')
          .map(word => word[0])
          .join('')
          .substring(0, 2)
          .toUpperCase();
      };

      // Fonctions de plafonnement
      const capDailyHours = (hours) => {
        return Math.min(parseFloat(hours), DAILY_LIMIT).toFixed(1);
      };

      const capWeeklyHours = (hours) => {
        return Math.min(parseFloat(hours), WEEKLY_LIMIT).toFixed(1);
      };

      // Calculs de pourcentages (basés sur les heures plafonnées)
      const getDailyPercentage = (hours) => {
        const cappedHours = Math.min(parseFloat(hours), DAILY_LIMIT);
        return Math.round((cappedHours / DAILY_LIMIT) * 100);
      };

      const getWeeklyPercentage = (hours) => {
        const cappedHours = Math.min(parseFloat(hours), WEEKLY_LIMIT);
        return Math.round((cappedHours / WEEKLY_LIMIT) * 100);
      };

      const getActivityPercentage = () => {
        return totalUsers.value > 0 ? Math.round((activeUsers.value / totalUsers.value) * 100) : 0;
      };

      // Classes CSS pour les couleurs (basées sur heures plafonnées)
      const getDailyAverageClass = (hours) => {
        const cappedHours = Math.min(parseFloat(hours), DAILY_LIMIT);
        const percentage = (cappedHours / DAILY_LIMIT) * 100;
        if (percentage < 50) return 'text-red-500 dark:text-red-400';
        if (percentage < 80) return 'text-yellow-500 dark:text-yellow-400';
        return 'text-green-500 dark:text-green-400';
      };

      const getWeeklyTotalClass = (hours) => {
        const weeklyTarget = totalUsers.value * WEEKLY_LIMIT;
        const cappedTeamTotal = props.presenceData.summary
          ? props.presenceData.summary.reduce((sum, user) => sum + Math.min(parseFloat(user.totalHours), WEEKLY_LIMIT), 0)
          : 0;
        const percentage = (cappedTeamTotal / weeklyTarget) * 100;
        if (percentage < 50) return 'text-red-500 dark:text-red-400';
        if (percentage < 80) return 'text-yellow-500 dark:text-yellow-400';
        return 'text-green-500 dark:text-green-400';
      };

      const getWeeklyHoursClass = (hours) => {
        const cappedHours = Math.min(parseFloat(hours), WEEKLY_LIMIT);
        const percentage = (cappedHours / WEEKLY_LIMIT) * 100;
        if (percentage < 50) return 'text-red-500 dark:text-red-400';
        if (percentage < 80) return 'text-yellow-500 dark:text-yellow-400';
        return 'text-green-500 dark:text-green-400';
      };

      // Score de conformité pour un utilisateur (basé sur heures plafonnées)
      const getComplianceScore = (totalHours, avgDaily) => {
        const cappedWeekly = Math.min(parseFloat(totalHours), WEEKLY_LIMIT);
        const cappedDaily = Math.min(parseFloat(avgDaily), DAILY_LIMIT);
        const weeklyScore = (cappedWeekly / WEEKLY_LIMIT) * 100;
        const dailyScore = (cappedDaily / DAILY_LIMIT) * 100;
        return Math.round((weeklyScore + dailyScore) / 2);
      };

      const getUserComplianceClass = (totalHours, avgDaily) => {
        const score = getComplianceScore(totalHours, avgDaily);
        if (score < 50) return 'text-red-500 dark:text-red-400';
        if (score < 80) return 'text-yellow-500 dark:text-yellow-400';
        return 'text-green-500 dark:text-green-400';
      };

      const getProgressBarClass = (totalHours, avgDaily) => {
        const score = getComplianceScore(totalHours, avgDaily);
        if (score < 50) return 'bg-red-500';
        if (score < 80) return 'bg-yellow-500';
        return 'bg-green-500';
      };

      // Badges de statut
      const getStatusBadge = (totalHours, avgDaily) => {
        const score = getComplianceScore(totalHours, avgDaily);
        if (score < 50) return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        if (score < 80) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
      };

      const getStatusText = (totalHours, avgDaily) => {
        const score = getComplianceScore(totalHours, avgDaily);
        if (score < 50) return 'Insuffisant';
        if (score < 80) return 'Partiel';
        return 'Conforme';
      };

      // Métriques d'équipe
      const getWeeklyTargetText = () => {
        const target = totalUsers.value * WEEKLY_LIMIT;
        return `Objectif: ${target}h`;
      };

      const getCompliancePercentage = () => {
        if (!props.presenceData.summary || props.presenceData.summary.length === 0) {
          return 0;
        }

        const compliantUsers = props.presenceData.summary.filter(user => {
          const cappedWeekly = Math.min(parseFloat(user.totalHours), WEEKLY_LIMIT);
          const cappedDaily = Math.min(parseFloat(user.averageDailyHours), DAILY_LIMIT);
          const weeklyScore = (cappedWeekly / WEEKLY_LIMIT) * 100;
          const dailyScore = (cappedDaily / DAILY_LIMIT) * 100;
          const avgScore = (weeklyScore + dailyScore) / 2;
          return avgScore >= 80;
        }).length;

        return Math.round((compliantUsers / props.presenceData.summary.length) * 100);
      };

      const getComplianceClass = () => {
        const compliance = getCompliancePercentage();
        if (compliance < 50) return 'text-red-500 dark:text-red-400';
        if (compliance < 80) return 'text-yellow-500 dark:text-yellow-400';
        return 'text-green-500 dark:text-green-400';
      };

      const getComplianceText = () => {
        const compliance = getCompliancePercentage();
        if (compliance < 50) return 'Critique';
        if (compliance < 80) return 'Moyen';
        return 'Excellent';
      };

      return {
        totalUsers,
        activeUsers,
        totalHours,
        averageHours,
        topCollaborateurs,
        getMemberInitials,
        capDailyHours,
        capWeeklyHours,
        getDailyPercentage,
        getWeeklyPercentage,
        getActivityPercentage,
        getDailyAverageClass,
        getWeeklyTotalClass,
        getWeeklyHoursClass,
        getComplianceScore,
        getUserComplianceClass,
        getProgressBarClass,
        getStatusBadge,
        getStatusText,
        getWeeklyTargetText,
        getCompliancePercentage,
        getComplianceClass,
        getComplianceText
      };
    }
  };
  </script>
