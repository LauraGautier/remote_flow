<template>
  <AppLayout title="Projets">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Projets</h2>
        <div v-if="$page.props.auth.user.role === 'manager'">
          <Link
            :href="route('projects.create')"
            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:border-gray-900 dark:focus:border-gray-800 focus:ring focus:ring-gray-300 dark:focus:ring-gray-700 transition"
          >
            Créer un nouveau projet
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Options de filtrage -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
          <div class="flex flex-wrap items-center">
            <div class="mr-4 mb-2">
              <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut</label>
              <select
                id="statusFilter"
                v-model="filters.status"
                class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
              >
                <option value="">Tous</option>
                <option value="active">Actifs</option>
                <option value="on_hold">En pause</option>
                <option value="completed">Terminés</option>
              </select>
            </div>

            <div class="mr-4 mb-2">
              <label for="searchFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recherche</label>
              <input
                id="searchFilter"
                v-model="filters.search"
                type="text"
                placeholder="Rechercher un projet..."
                class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
              >
            </div>

            <div class="mb-2 flex items-end">
              <button
                @click="resetFilters"
                class="px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 focus:outline-none"
              >
                Réinitialiser
              </button>
            </div>
          </div>
        </div>

        <!-- Liste des projets -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div v-if="filteredProjects.length" class="space-y-6">
            <div
              v-for="project in filteredProjects"
              :key="project.id"
              class="border dark:border-gray-700 p-6 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150"
            >
              <Link :href="route('projects.show', project.id)" class="block">
                <div class="flex justify-between items-start">
                  <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ project.name }}</h3>
                    <p v-if="project.description" class="mt-2 text-gray-600 dark:text-gray-400 line-clamp-2">
                      {{ project.description }}
                    </p>
                  </div>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="{
                        'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200': project.status === 'active',
                        'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200': project.status === 'on_hold',
                        'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200': project.status === 'completed'
                      }">
                    {{ getStatusLabel(project.status) }}
                  </span>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                  <!-- Statistiques des tâches -->
                  <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Tâches</div>
                    <div class="mt-1 flex items-center">
                      <div class="font-medium text-gray-900 dark:text-white">
                        {{ getProjectTaskStats(project).completed }} / {{ getProjectTaskStats(project).total }}
                      </div>
                      <div class="ml-2 flex-1">
                        <div class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                          <div class="h-full bg-green-500 rounded-full"
                              :style="{ width: `${getTaskCompletionPercentage(project)}%` }">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Objectifs -->
                  <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Objectifs</div>
                    <div class="mt-1 flex items-center">
                      <div class="font-medium text-gray-900 dark:text-white">
                        {{ getObjectivesStats(project).completed }} / {{ getObjectivesStats(project).total }}
                      </div>
                      <div class="ml-2 flex-1">
                        <div class="w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                          <div class="h-full"
                              :class="getProgressColorClass(getObjectivesCompletionPercentage(project))"
                              :style="{ width: `${getObjectivesCompletionPercentage(project)}%` }">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Dates -->
                  <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Période</div>
                    <div class="mt-1 text-sm text-gray-900 dark:text-white">
                      <span v-if="project.start_date">Début : {{ formatDate(project.start_date) }}</span>
                      <span v-if="project.start_date && project.end_date"> - </span>
                      <span v-if="project.end_date">Fin : {{ formatDate(project.end_date) }}</span>
                      <span v-if="!project.start_date && !project.end_date">Non définie</span>
                    </div>
                  </div>
                </div>

                <div class="mt-4 flex justify-between items-center text-sm">
                  <div>
                    <span class="text-gray-500 dark:text-gray-400">Créé par : </span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ project.creator.name }}</span>
                  </div>
                  <div>
                    <button
                      class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium focus:outline-none"
                      @click.prevent="navigateToProject(project.id)"
                    >
                      Voir les détails →
                    </button>
                  </div>
                </div>
              </Link>
            </div>
          </div>

          <!-- Message si aucun projet -->
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Aucun projet trouvé</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              {{ filters.status || filters.search ? 'Essayez de modifier vos filtres de recherche.' : 'Commencez par créer un nouveau projet.' }}
            </p>
            <div v-if="$page.props.auth.user.role === 'manager' && !filters.status && !filters.search" class="mt-6">
              <Link
                :href="route('projects.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-400"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nouveau projet
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  components: {
    AppLayout,
    Link
  },
  props: {
    projects: Array,
    team: Object
  },
  data() {
    return {
      filters: {
        status: '',
        search: ''
      }
    }
  },
  computed: {
    filteredProjects() {
      return this.projects.filter(project => {
        // Filtre par statut
        if (this.filters.status && project.status !== this.filters.status) {
          return false;
        }

        // Filtre par recherche
        if (this.filters.search) {
          const searchTerm = this.filters.search.toLowerCase();
          const nameMatch = project.name.toLowerCase().includes(searchTerm);
          const descMatch = project.description ? project.description.toLowerCase().includes(searchTerm) : false;

          if (!nameMatch && !descMatch) {
            return false;
          }
        }

        return true;
      });
    }
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return '';
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    getStatusLabel(status) {
      const labels = {
        'active': 'Actif',
        'completed': 'Terminé',
        'on_hold': 'En pause'
      }
      return labels[status] || status;
    },
    getProgressColorClass(percentage) {
      if (percentage < 33) return 'bg-red-500 dark:bg-red-600';
      if (percentage < 66) return 'bg-yellow-500 dark:bg-yellow-600';
      return 'bg-green-500 dark:bg-green-600';
    },
    getProjectTaskStats(project) {
      // On suppose que chaque projet contient un tableau de tâches
      // Si ce n'est pas le cas, il faudra ajuster cette logique
      const tasks = project.tasks || [];
      return {
        total: tasks.length,
        completed: tasks.filter(task => task.status === 'completed').length
      };
    },
    getTaskCompletionPercentage(project) {
      const stats = this.getProjectTaskStats(project);
      if (stats.total === 0) return 0;
      return Math.round((stats.completed / stats.total) * 100);
    },
    getObjectivesStats(project) {
      // Vérifier si le projet a des objectifs
      if (!project.objectives || !Array.isArray(project.objectives)) {
        return { total: 0, completed: 0 };
      }

      return {
        total: project.objectives.length,
        completed: project.objectives.filter(objective => objective.is_completed).length
      };
    },
    getObjectivesCompletionPercentage(project) {
      const stats = this.getObjectivesStats(project);

      if (stats.total === 0) {
          return 0;
      }

      return Math.round((stats.completed / stats.total) * 100);
    },
    resetFilters() {
      this.filters = {
        status: '',
        search: ''
      };
    },
    navigateToProject(projectId) {
      router.visit(route('projects.show', projectId));
    }
  }
};
</script>

<style scoped>
/* Pour tronquer la description à 2 lignes */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
