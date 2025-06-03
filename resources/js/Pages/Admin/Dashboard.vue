<template>
  <AppLayout title="Tableau de bord administrateur">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Tableau d'administration
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Statistiques générales en cartes -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center space-x-4">
              <div class="bg-blue-500 rounded-full p-3">
                <i class="fas fa-users text-white text-xl"></i>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Utilisateurs</div>
                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ userStats.total }}</div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center space-x-4">
              <div class="bg-green-500 rounded-full p-3">
                <i class="fas fa-users-group text-white text-xl"></i>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Équipes</div>
                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ teamStats.total }}</div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center space-x-4">
              <div class="bg-yellow-500 rounded-full p-3">
                <i class="fas fa-clipboard text-white text-xl"></i>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Projets</div>
                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ projectStats.total }}</div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center space-x-4">
              <div class="bg-purple-500 rounded-full p-3">
                <i class="fas fa-check-square text-white text-xl"></i>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Tâches</div>
                <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ taskStats.total }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Vue d'ensemble des utilisateurs -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Répartition des utilisateurs</h3>
            <Link href="/admin/users" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
              Voir tous les utilisateurs
            </Link>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <h4 class="text-md font-medium mb-2 text-gray-700 dark:text-gray-300">Par rôle</h4>
              <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                <div class="flex justify-between mb-2">
                  <span class="text-gray-700 dark:text-gray-300">Administrateurs:</span>
                  <span class="font-semibold text-gray-800 dark:text-gray-200">{{ userStats.byRole.administrateur }}</span>
                </div>
                <div class="flex justify-between mb-2">
                  <span class="text-gray-700 dark:text-gray-300">Managers:</span>
                  <span class="font-semibold text-gray-800 dark:text-gray-200">{{ userStats.byRole.manager }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-700 dark:text-gray-300">Collaborateurs:</span>
                  <span class="font-semibold text-gray-800 dark:text-gray-200">{{ userStats.byRole.collaborateur }}</span>
                </div>
              </div>
            </div>

            <div class="md:col-span-2">
              <h4 class="text-md font-medium mb-2 text-gray-700 dark:text-gray-300">Utilisateurs récemment enregistrés</h4>
              <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                <div v-if="userStats.recentlyRegistered.length === 0" class="text-gray-500 dark:text-gray-400 text-center py-4">
                  Aucun utilisateur récemment inscrit
                </div>
                <div v-else class="space-y-2">
                  <div v-for="user in userStats.recentlyRegistered" :key="user.id" class="flex justify-between">
                    <div>
                      <span class="font-medium text-gray-800 dark:text-gray-200">{{ user.name }}</span>
                      <span class="text-gray-500 dark:text-gray-400 text-sm"> ({{ user.email }})</span>
                    </div>
                    <div>
                      <span class="px-2 py-1 text-xs rounded-full"
                            :class="getRoleBadgeClass(user.role)">
                        {{ user.role }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Vue d'ensemble des équipes -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Équipes</h3>
            <Link href="/admin/teams" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
              Voir les détails
            </Link>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
            <div>
              <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                <div v-if="teamStats.recentTeams.length === 0" class="text-gray-500 dark:text-gray-400 text-center py-4">
                  Aucune équipe enregistrée
                </div>
                <div v-else class="space-y-2">
                  <div v-for="team in teamStats.recentTeams" :key="team.id" class="flex justify-between">
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ team.name }}</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ team.members_count }} membres</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Vue d'ensemble des projets -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Projets</h3>
            <div class="text-sm text-gray-500 dark:text-gray-400">
              {{ projectStats.total }} projets au total
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-green-100 dark:bg-green-900 p-4 rounded-lg text-center">
              <div class="text-2xl font-bold text-green-700 dark:text-green-300">{{ projectStats.byStatus.active }}</div>
              <div class="text-sm text-green-600 dark:text-green-400">Actifs</div>
            </div>
            <div class="bg-blue-100 dark:bg-blue-900 p-4 rounded-lg text-center">
              <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ projectStats.byStatus.completed }}</div>
              <div class="text-sm text-blue-600 dark:text-blue-400">Terminés</div>
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg text-center">
              <div class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ projectStats.byStatus.on_hold }}</div>
              <div class="text-sm text-gray-600 dark:text-gray-400">En pause</div>
            </div>
          </div>

          <h4 class="text-md font-medium mb-2 text-gray-700 dark:text-gray-300">Projets récents</h4>
          <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
            <div v-if="projectStats.recentProjects.length === 0" class="text-gray-500 dark:text-gray-400 text-center py-4">
              Aucun projet récent
            </div>
            <div v-else>
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead>
                  <tr>
                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nom</th>
                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Équipe</th>
                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Créateur</th>
                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="project in projectStats.recentProjects" :key="project.id">
                    <td class="px-2 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ project.name }}</td>
                    <td class="px-2 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ project.team }}</td>
                    <td class="px-2 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ project.creator }}</td>
                    <td class="px-2 py-4 whitespace-nowrap">
                      <span class="px-2 py-1 text-xs rounded-full"
                            :class="getStatusBadgeClass(project.status)">
                        {{ project.status }}
                      </span>
                    </td>
                    <td class="px-2 py-4 whitespace-nowrap text-gray-800 dark:text-gray-200">{{ project.created_at }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Tâches et objectifs -->
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-6">
          <!-- Statistiques des tâches -->
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Tâches</h3>

            <div class="mb-4">
              <div class="flex justify-between mb-1">
                <span class="text-gray-700 dark:text-gray-300">En attente:</span>
                <span class="text-gray-700 dark:text-gray-300">{{ taskStats.byStatus.pending }} / {{ taskStats.total }}</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5">
                <div class="bg-yellow-400 dark:bg-yellow-500 h-2.5 rounded-full"
                     :style="`width: ${(taskStats.byStatus.pending / taskStats.total) * 100}%`"></div>
              </div>
            </div>

            <div class="mb-4">
              <div class="flex justify-between mb-1">
                <span class="text-gray-700 dark:text-gray-300">En cours:</span>
                <span class="text-gray-700 dark:text-gray-300">{{ taskStats.byStatus.in_progress }} / {{ taskStats.total }}</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5">
                <div class="bg-blue-500 h-2.5 rounded-full"
                     :style="`width: ${(taskStats.byStatus.in_progress / taskStats.total) * 100}%`"></div>
              </div>
            </div>

            <div class="mb-4">
              <div class="flex justify-between mb-1">
                <span class="text-gray-700 dark:text-gray-300">Terminées:</span>
                <span class="text-gray-700 dark:text-gray-300">{{ taskStats.byStatus.completed }} / {{ taskStats.total }}</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5">
                <div class="bg-green-500 h-2.5 rounded-full"
                     :style="`width: ${(taskStats.byStatus.completed / taskStats.total) * 100}%`"></div>
              </div>
            </div>

            <h4 class="text-md font-medium mt-6 mb-2 text-gray-700 dark:text-gray-300">Tâches récemment terminées</h4>
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
              <div v-if="taskStats.recentlyCompleted.length === 0" class="text-gray-500 dark:text-gray-400 text-center py-4">
                Aucune tâche récemment terminée
              </div>
              <div v-else class="space-y-2">
                <div v-for="task in taskStats.recentlyCompleted" :key="task.id" class="flex justify-between">
                  <div>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ task.title }}</span>
                    <span class="text-gray-500 dark:text-gray-400 text-sm"> par {{ task.assigned_to }}</span>
                  </div>
                  <span class="text-sm text-gray-500 dark:text-gray-400">{{ task.completed_at }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Activité récente -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Activité récente</h3>

          <div v-if="recentActivity.length === 0" class="text-gray-500 dark:text-gray-400 text-center py-4">
            Aucune activité récente
          </div>
          <div v-else class="space-y-4">
            <div v-for="(activity, index) in recentActivity" :key="index" class="flex space-x-3">
              <div :class="getActivityIconClass(activity.type)" class="flex items-center justify-center h-10 w-10 rounded-full flex-shrink-0">
                <i :class="['fas', getActivityIcon(activity.type), 'text-white']"></i>
              </div>
              <div class="flex-1 overflow-hidden">
                <div class="flex space-x-3">
                  <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ activity.title }}</h4>
                  <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(activity.time) }}</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ activity.description }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Actions rapides</h3>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Link :href="route('admin.users.list')" class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
              <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-blue-500 text-white rounded-md">
                <i class="fas fa-users text-xl"></i>
              </div>
              <div class="ml-4">
                <h4 class="text-lg font-medium text-blue-700 dark:text-blue-300">Gestion des utilisateurs</h4>
                <p class="text-sm text-blue-600 dark:text-blue-400">Ajouter, modifier ou supprimer des utilisateurs</p>
              </div>
            </Link>

            <Link :href="route('admin.teams.list')" class="flex items-center p-4 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-800 hover:bg-green-100 dark:hover:bg-green-900/50 transition">
              <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-green-500 text-white rounded-md">
                <i class="fas fa-user-friends text-xl"></i>
              </div>
              <div class="ml-4">
                <h4 class="text-lg font-medium text-green-700 dark:text-green-300">Gestion des équipes</h4>
                <p class="text-sm text-green-600 dark:text-green-400">Ajouter, modifier ou supprimer des équipes</p>
              </div>
            </Link>

            <Link :href="route('admin.system.logs')" class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/30 rounded-lg border border-purple-200 dark:border-purple-800 hover:bg-purple-100 dark:hover:bg-purple-900/50 transition">
              <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-purple-500 text-white rounded-md">
                <i class="fas fa-cogs text-xl"></i>
              </div>
              <div class="ml-4">
                <h4 class="text-lg font-medium text-purple-700 dark:text-purple-300">Informations système</h4>
                <p class="text-sm text-purple-600 dark:text-purple-400">Consulter la configuration de l'application</p>
              </div>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { defineComponent } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

export default defineComponent({
  components: {
    AppLayout,
    Link
  },

  props: {
    userStats: Object,
    teamStats: Object,
    projectStats: Object,
    taskStats: Object,
    objectiveStats: Object,
    recentActivity: Array,
    timeChartData: Object
  },

  methods: {
    getRoleBadgeClass(role) {
      switch (role) {
        case 'administrateur':
          return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        case 'manager':
          return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'collaborateur':
          return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        default:
          return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
      }
    },

    getStatusBadgeClass(status) {
      switch (status) {
        case 'active':
          return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'completed':
          return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'on_hold':
          return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        default:
          return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
      }
    },

    getActivityIconClass(type) {
      switch (type) {
        case 'user_created':
          return 'bg-blue-500';
        case 'team_created':
          return 'bg-green-500';
        case 'project_created':
          return 'bg-yellow-500';
        case 'task_completed':
          return 'bg-purple-500';
        case 'objective_completed':
          return 'bg-indigo-500';
        default:
          return 'bg-gray-500';
      }
    },

    getActivityIcon(type) {
      switch (type) {
        case 'user_created':
          return 'fa-user-plus';
        case 'team_created':
          return 'fa-users';
        case 'project_created':
          return 'fa-folder-plus';
        case 'task_completed':
          return 'fa-check-circle';
        case 'objective_completed':
          return 'fa-bullseye';
        default:
          return 'fa-bell';
      }
    },

    formatDate(dateString) {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(date);
    }
  }
})
</script>
