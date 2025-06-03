<template>
  <AppLayout :title="`Équipe: ${team.name}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Détails de l'équipe: {{ team.name }}
        </h2>
        <Link :href="route('admin.teams.list')" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:border-gray-500 dark:focus:border-gray-400 focus:ring focus:ring-gray-300 dark:focus:ring-gray-700 disabled:opacity-25 transition">
          <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:items-stretch">
          <!-- Informations de l'équipe -->
          <div class="lg:col-span-1 flex">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 w-full flex flex-col">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2 mb-4">
                Informations de l'équipe
              </h3>

              <div class="space-y-4 flex-grow">
                <div>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Nom:</div>
                  <div class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ team.name }}</div>
                </div>

                <div>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Propriétaire:</div>
                  <div class="mt-1 text-sm text-gray-900 dark:text-gray-200 flex items-center">
                    <div class="h-8 w-8 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 mr-2">
                      <img v-if="team.owner && team.owner.profile_photo_url" :src="team.owner.profile_photo_url" :alt="team.owner.name" class="h-full w-full object-cover">
                      <div v-else class="h-full w-full flex items-center justify-center bg-blue-200 dark:bg-blue-800 text-blue-600 dark:text-blue-300">
                        <span class="text-sm font-bold">{{ team.owner ? team.owner.name.charAt(0) : '?' }}</span>
                      </div>
                    </div>
                    <div>
                      {{ team.owner ? team.owner.name : 'Non spécifié' }}
                      <div class="text-xs text-gray-500 dark:text-gray-400">{{ team.owner ? team.owner.email : '' }}</div>
                    </div>
                  </div>
                </div>

                <div>
                  <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de création:</div>
                  <div class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ formatDate(team.created_at) }}</div>
                </div>
              </div>

              <div class="mt-6 border-t dark:border-gray-700 pt-4">
                <div class="space-y-3">
                  <button
                    v-if="!team.personal_team"
                    type="button"
                    class="inline-flex w-full items-center justify-center px-4 py-2 bg-red-600 dark:bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 active:bg-red-900 dark:active:bg-red-800 focus:outline-none focus:border-red-900 dark:focus:border-red-800 focus:ring focus:ring-red-300 dark:focus:ring-red-700 disabled:opacity-25 transition"
                    @click="confirmTeamDeletion"
                  >
                    <i class="fas fa-trash-alt mr-2"></i> Supprimer l'équipe
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Membres de l'équipe -->
          <div class="lg:col-span-2 flex">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 w-full">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  Membres de l'équipe
                </h3>
                <button
                  type="button"
                  class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 active:bg-green-900 dark:active:bg-green-800 focus:outline-none focus:border-green-900 dark:focus:border-green-800 focus:ring focus:ring-green-300 dark:focus:ring-green-700 disabled:opacity-25 transition"
                  @click="addTeamMember"
                >
                  <i class="fas fa-user-plus mr-2"></i> Ajouter un membre
                </button>
              </div>

              <div class="overflow-x-auto bg-gray-50 dark:bg-gray-700 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                  <thead class="bg-gray-100 dark:bg-gray-600">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Utilisateur</th>
                      <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                    <tr v-if="members.length === 0">
                      <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                        Aucun membre dans cette équipe.
                      </td>
                    </tr>
                    <tr v-for="member in members" :key="member.id" :class="{'bg-gray-50 dark:bg-gray-600': member.id === team.owner?.id}">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                            <img v-if="member.profile_photo_url" :src="member.profile_photo_url" :alt="member.name" class="h-full w-full object-cover">
                            <div v-else class="h-full w-full flex items-center justify-center bg-blue-200 dark:bg-blue-800 text-blue-600 dark:text-blue-300">
                              <span class="font-bold">{{ member.name.charAt(0) }}</span>
                            </div>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                              {{ member.name }}
                              <span v-if="member.id === team.owner?.id" class="ml-2 text-xs text-gray-500 dark:text-gray-400">(Propriétaire)</span>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ member.email }}</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button
                          type="button"
                          class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3"
                          @click="editTeamMember(member)"
                        >
                          Modifier
                        </button>

                        <button
                          v-if="member.id !== team.owner?.id"
                          type="button"
                          class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                          @click="confirmRemoveMember(member)"
                        >
                          Retirer
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de suppression d'équipe -->
    <div v-if="confirmingTeamDelete" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
          Supprimer l'équipe
        </h2>

        <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">
          Êtes-vous sûr de vouloir supprimer l'équipe <span class="font-semibold">{{ team.name }}</span> ?
        </p>

        <p class="mb-6 text-sm text-red-600 dark:text-red-400">
          Cette action est irréversible. Tous les projets, tâches et données associés à cette équipe seront supprimés.
        </p>

        <div class="flex justify-end">
          <button
            type="button"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-600 mr-3"
            @click="confirmingTeamDelete = false"
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

    <!-- Modal de retrait de membre -->
    <div v-if="memberToRemove" class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center z-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
          Retirer le membre
        </h2>

        <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">
          Êtes-vous sûr de vouloir retirer <span class="font-semibold">{{ memberToRemove.name }}</span> de l'équipe ?
        </p>

        <div class="flex justify-end">
          <button
            type="button"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-600 mr-3"
            @click="memberToRemove = null"
          >
            Annuler
          </button>

          <button
            type="button"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-red-500 dark:focus:ring-red-600"
            :disabled="processing"
            @click="removeMember"
          >
            <i class="fas fa-user-minus mr-2"></i> Retirer
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
    team: Object,
    members: Array,
    projects: Array
  },

  data() {
    return {
      confirmingTeamDelete: false,
      memberToRemove: null,
      processing: false
    }
  },

  methods: {
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      }).format(date);
    },

    getRoleBadgeClass(role) {
      switch (role) {
        case 'administrateur':
          return 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300';
        case 'manager':
          return 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300';
        case 'collaborateur':
          return 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300';
        default:
          return 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
      }
    },

    getStatusBadgeClass(status) {
      switch (status) {
        case 'active':
          return 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300';
        case 'completed':
          return 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300';
        case 'on_hold':
          return 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300';
        default:
          return 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
      }
    },

    formatStatus(status) {
      switch (status) {
        case 'active':
          return 'Actif';
        case 'completed':
          return 'Terminé';
        case 'on_hold':
          return 'En pause';
        default:
          return status;
      }
    },

    confirmTeamDeletion() {
      this.confirmingTeamDelete = true;
    },

    deleteTeam() {
      this.processing = true;
      router.delete(route('admin.teams.destroy', this.team.id), {
        onSuccess: () => {
          // La redirection se fait automatiquement par le contrôleur
          this.processing = false;
        },
        onError: () => {
          this.processing = false;
          this.confirmingTeamDelete = false;
        }
      });
    },

    addTeamMember() {
      router.visit(route('admin.teams.members.create', this.team.id));
    },

    editTeamMember(member) {
      router.visit(route('admin.teams.members.edit', { team: this.team.id, user: member.id }));
    },

    confirmRemoveMember(member) {
      this.memberToRemove = member;
    },

    removeMember() {
      this.processing = true;
      router.delete(route('admin.teams.members.destroy', { team: this.team.id, user: this.memberToRemove.id }), {
        onSuccess: () => {
          this.processing = false;
          this.memberToRemove = null;
        },
        onError: () => {
          this.processing = false;
          this.memberToRemove = null;
        }
      });
    }
  }
})
</script>
