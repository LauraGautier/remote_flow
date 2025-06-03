<template>
  <AppLayout title="Gestion des équipes">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Gestion des équipes
        </h2>
        <Link :href="route('admin.teams.create')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 dark:focus:ring-green-700 disabled:opacity-25 transition">
          <i class="fas fa-users mr-2"></i> Nouvelle équipe
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Filtres -->
          <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1">
              <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rechercher</label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                </div>
                <input
                  type="text"
                  name="search"
                  id="search"
                  v-model="form.search"
                  class="focus:ring-green-500 focus:border-green-500 dark:focus:ring-green-600 dark:focus:border-green-600 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md"
                  placeholder="Nom de l'équipe"
                  @keyup.enter="search"
                >
              </div>
            </div>
            <div class="w-full md:w-auto md:self-end">
              <button
                type="button"
                class="w-full md:w-auto inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-green-500"
                @click="resetFilters"
              >
                <i class="fas fa-times mr-2"></i> Réinitialiser
              </button>
            </div>
          </div>

          <!-- Tableau des équipes -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Propriétaire</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Membres</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Création</th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="team in teams.data" :key="team.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ team.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ team.owner ? team.owner.name : 'N/A' }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ team.owner ? team.owner.email : '' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ team.users_count || 0 }} membres
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="team.personal_team ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'"
                    >
                      {{ team.personal_team ? 'Personnelle' : 'Organisation' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDate(team.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <Link
                      :href="route('admin.teams.details', team.id)"
                      class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-3"
                    >
                      Détails
                    </Link>

                    <button
                      v-if="!team.personal_team"
                      type="button"
                      class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                      @click="confirmTeamDeletion(team)"
                    >
                      Supprimer
                    </button>
                  </td>
                </tr>
                <tr v-if="teams.data.length === 0">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center" colspan="6">
                    Aucune équipe trouvée
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination simple -->
          <div v-if="teams.data.length > 0" class="mt-6 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
              <Link
                v-if="teams.prev_page_url"
                :href="teams.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
              >
                Précédent
              </Link>
              <Link
                v-if="teams.next_page_url"
                :href="teams.next_page_url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
              >
                Suivant
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                  Affichage de
                  <span class="font-medium">{{ teams.from || 0 }}</span>
                  à
                  <span class="font-medium">{{ teams.to || 0 }}</span>
                  sur
                  <span class="font-medium">{{ teams.total || 0 }}</span>
                  résultats
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <Link
                    v-if="teams.prev_page_url"
                    :href="teams.prev_page_url"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600"
                  >
                    <span class="sr-only">Précédent</span>
                    <i class="fas fa-chevron-left"></i>
                  </Link>
                  <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Page {{ teams.current_page }}
                  </span>
                  <Link
                    v-if="teams.next_page_url"
                    :href="teams.next_page_url"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600"
                  >
                    <span class="sr-only">Suivant</span>
                    <i class="fas fa-chevron-right"></i>
                  </Link>
                </nav>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistiques des équipes -->
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                  <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                      Total d'équipes
                    </dt>
                    <dd>
                      <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ teams.total || 0 }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                  <i class="fas fa-user text-white text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                      Équipes personnelles
                    </dt>
                    <dd>
                      <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ personalTeamsCount }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                  <i class="fas fa-building text-white text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                      Équipes d'organisation
                    </dt>
                    <dd>
                      <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ organizationTeamsCount }}
                      </div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div v-if="confirmingTeamDeletion" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
          Supprimer l'équipe
        </h2>

        <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">
          Êtes-vous sûr de vouloir supprimer l'équipe <span class="font-semibold">{{ teamToDelete ? teamToDelete.name : '' }}</span> ?
        </p>

        <p class="mb-6 text-sm text-red-600 dark:text-red-400">
          Cette action est irréversible. Tous les projets, tâches et données associés à cette équipe seront supprimés.
        </p>

        <div class="flex justify-end">
          <button
            type="button"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-600 mr-3"
            @click="closeModal"
          >
            Annuler
          </button>

          <button
            type="button"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-red-500 dark:focus:ring-red-600"
            :disabled="processing"
            @click="deleteTeam"
          >
            <i class="fas fa-trash-alt mr-2"></i> Supprimer
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { defineComponent } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

export default defineComponent({
  components: {
    AppLayout,
    Link
  },

  props: {
    teams: Object,
    filters: Object
  },

  data() {
    return {
      form: {
        search: this.filters?.search || ''
      },
      confirmingTeamDeletion: false,
      teamToDelete: null,
      processing: false
    }
  },

  computed: {
    personalTeamsCount() {
      return this.teams.data ? this.teams.data.filter(team => team.personal_team).length : 0;
    },

    organizationTeamsCount() {
      return this.teams.data ? this.teams.data.filter(team => !team.personal_team).length : 0;
    }
  },

  methods: {
    search() {
      router.get(route('admin.teams.list'), this.form, {
        preserveState: true,
        replace: true
      });
    },

    resetFilters() {
      this.form = {
        search: ''
      };
      this.search();
    },

    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      }).format(date);
    },

    confirmTeamDeletion(team) {
      this.teamToDelete = team;
      this.confirmingTeamDeletion = true;
    },

    closeModal() {
      this.confirmingTeamDeletion = false;
      setTimeout(() => {
        this.teamToDelete = null;
      }, 300);
    },

    deleteTeam() {
      this.processing = true;
      router.delete(route('admin.teams.destroy', this.teamToDelete.id), {
        onSuccess: () => {
          this.closeModal();
          this.processing = false;
        },
        onError: () => {
          this.processing = false;
        }
      });
    }
  }
})
</script>
