<!-- Section de présence pour le dashboard du manager (Tailwind CSS) -->
<template>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Heures de présence (7 derniers jours)</h3>
      </div>

      <!-- Statistiques de présence -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Moyenne quotidienne équipe</div>
          <div class="text-2xl font-semibold mt-1" :class="getAverageClass(averageHours)">
            {{ averageHours }}h
          </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total heures équipe</div>
          <div class="text-2xl font-semibold mt-1 text-gray-900 dark:text-white">
            {{ totalHours }}h
          </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Collaborateurs présents</div>
          <div class="text-2xl font-semibold mt-1 text-gray-900 dark:text-white">
            {{ activeUsers }}/{{ totalUsers }}
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
                  Total
                </th>
                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Moyenne/jour
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
                <td class="px-3 py-2 whitespace-nowrap text-center text-sm text-gray-900 dark:text-white">
                  {{ user.totalHours }}h
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-center text-sm font-medium" :class="getAverageClass(user.averageDailyHours)">
                  {{ user.averageDailyHours }}h
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusBadge(user.averageDailyHours)">
                    {{ getStatusText(user.averageDailyHours) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
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

      // Total des heures pour l'équipe
      const totalHours = computed(() => {
        if (!props.presenceData.summary || props.presenceData.summary.length === 0) {
          return '0.0';
        }

        return props.presenceData.summary
          .reduce((sum, user) => sum + parseFloat(user.totalHours), 0)
          .toFixed(1);
      });

      // Moyenne des heures quotidiennes pour l'équipe
      const averageHours = computed(() => {
        if (!props.presenceData.summary || props.presenceData.summary.length === 0) {
          return '0.0';
        }

        const totalAvg = props.presenceData.summary
          .reduce((sum, user) => sum + parseFloat(user.averageDailyHours), 0);

        return (totalAvg / props.presenceData.summary.length).toFixed(1);
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

      // Récupérer les initiales d'un nom
      const getMemberInitials = (name) => {
        return name
          .split(' ')
          .map(word => word[0])
          .join('')
          .substring(0, 2)
          .toUpperCase();
      };

      // Classes pour la mise en forme
      const getAverageClass = (hours) => {
        const value = parseFloat(hours);
        if (value < 4) return 'text-red-500 dark:text-red-400';
        if (value < 7) return 'text-yellow-500 dark:text-yellow-400';
        return 'text-green-500 dark:text-green-400';
      };

      const getStatusBadge = (hours) => {
        const value = parseFloat(hours);
        if (value < 4) return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        if (value < 7) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
      };

      const getStatusText = (hours) => {
        const value = parseFloat(hours);
        if (value < 4) return 'Insuffisant';
        if (value < 7) return 'Partiel';
        return 'Conforme';
      };

      return {
        totalUsers,
        activeUsers,
        totalHours,
        averageHours,
        topCollaborateurs,
        getMemberInitials,
        getAverageClass,
        getStatusBadge,
        getStatusText
      };
    }
  };
  </script>
