<template>
  <AppLayout title="Modifier l'utilisateur">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Modifier l'utilisateur
        </h2>
        <Link :href="route('admin.users.list')" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:border-gray-500 dark:focus:border-gray-400 focus:ring focus:ring-gray-300 dark:focus:ring-gray-700 disabled:opacity-25 transition">
          <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Formulaire de modification -->
          <form @submit.prevent="updateUser">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Informations utilisateur -->
              <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2">Informations de l'utilisateur</h3>

                <div>
                  <div class="flex items-center space-x-4 mb-4">
                    <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                      <img v-if="user.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" class="h-full w-full object-cover">
                      <div v-else class="h-full w-full flex items-center justify-center bg-blue-200 dark:bg-blue-800 text-blue-600 dark:text-blue-300">
                        <span class="text-2xl font-bold">{{ user.name.charAt(0) }}</span>
                      </div>
                    </div>
                    <div>
                      <div class="font-medium text-gray-900 dark:text-gray-100">
                        {{ user.name }}
                      </div>
                      <div class="text-gray-500 dark:text-gray-400">
                        {{ user.email }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        Inscrit le {{ formatDate(user.created_at) }}
                      </div>
                    </div>
                  </div>
                </div>

                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom</label>
                  <input
                    type="text"
                    id="name"
                    v-model="form.name"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300"
                    required
                  >
                  <div v-if="errors.name" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ errors.name }}</div>
                </div>

                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                  <input
                    type="email"
                    id="email"
                    v-model="form.email"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300"
                    required
                  >
                  <div v-if="errors.email" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ errors.email }}</div>
                </div>

                <div>
                  <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rôle</label>
                  <select
                    id="role"
                    v-model="form.role"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300"
                  >
                    <option value="administrateur">Administrateur</option>
                    <option value="manager">Manager</option>
                    <option value="collaborateur">Collaborateur</option>
                  </select>
                  <div v-if="errors.role" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ errors.role }}</div>
                </div>

                <div class="pt-4">
                  <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Options de sécurité</h4>

                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <div class="text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Réinitialiser le mot de passe</span>
                        <p class="text-gray-500 dark:text-gray-400">Envoyer un email de réinitialisation du mot de passe à l'utilisateur</p>
                      </div>
                      <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                        @click="sendPasswordReset"
                      >
                        <i class="fas fa-key mr-2"></i> Envoyer
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Gestion des équipes -->
              <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2">Équipes</h3>

                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                  <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-4">Appartenances aux équipes</h4>

                  <div v-if="teams.length === 0" class="text-gray-500 dark:text-gray-400 text-center py-4">
                    Aucune équipe disponible
                  </div>

                  <div v-else class="space-y-3">
                    <div v-for="team in teams" :key="team.id" class="flex items-center">
                      <input
                        type="checkbox"
                        :id="`team-${team.id}`"
                        :value="team.id"
                        v-model="form.teams"
                        class="h-4 w-4 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 border-gray-300 dark:border-gray-600 rounded"
                      >
                      <label :for="`team-${team.id}`" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ team.name }}
                      </label>
                    </div>
                  </div>

                  <div v-if="errors.teams" class="text-red-500 dark:text-red-400 text-sm mt-2">{{ errors.teams }}</div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                  <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Statistiques de l'utilisateur</h4>

                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-500 dark:text-gray-400">Tâches assignées:</span>
                      <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ userStats.tasksCount || 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-500 dark:text-gray-400">Tâches complétées:</span>
                      <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ userStats.completedTasksCount || 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-500 dark:text-gray-400">Taux de complétion:</span>
                      <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ userStats.completionRate || 0 }}%</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-500 dark:text-gray-400">Dernière connexion:</span>
                      <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ userStats.lastLogin || 'Jamais' }}</span>
                    </div>
                  </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                  <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Actions avancées</h4>

                  <div class="space-y-4">
                    <button
                      v-if="$page.props.auth.user.id !== user.id"
                      type="button"
                      class="inline-flex items-center px-4 py-2 border border-red-300 dark:border-red-700 text-sm font-medium rounded-md shadow-sm text-red-700 dark:text-red-400 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-red-500 dark:focus:ring-red-600"
                      @click="confirmUserDeletion"
                    >
                      <i class="fas fa-trash-alt mr-2"></i> Supprimer l'utilisateur
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-8 border-t dark:border-gray-700 pt-6 flex justify-end">
              <Link
                :href="route('admin.users.list')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-600 mr-3"
              >
                Annuler
              </Link>

              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                :disabled="processing"
              >
                <i class="fas fa-save mr-2"></i> Enregistrer les modifications
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <Modal :show="confirmingDeletion" @close="confirmingDeletion = false">
      <div class="p-6 dark:bg-gray-800">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
          Supprimer l'utilisateur
        </h2>

        <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">
          Êtes-vous sûr de vouloir supprimer l'utilisateur <span class="font-semibold">{{ user.name }}</span> ?
        </p>

        <p class="mb-6 text-sm text-red-600 dark:text-red-400">
          Cette action est irréversible. Toutes les données associées à cet utilisateur seront supprimées.
        </p>

        <div class="flex justify-end">
          <button
            type="button"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-600 mr-3"
            @click="confirmingDeletion = false"
          >
            Annuler
          </button>

          <button
            type="button"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-red-500 dark:focus:ring-red-600"
            :disabled="processing"
            @click="deleteUser"
          >
            <i class="fas fa-trash-alt mr-2"></i> Supprimer
          </button>
        </div>
      </div>
    </Modal>
  </AppLayout>
</template>

<script>
import { defineComponent } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'

export default defineComponent({
  components: {
    AppLayout,
    Link,
    Modal
  },

  props: {
    user: Object,
    teams: Array,
    userTeams: Array,
    errors: Object,
    userStats: {
      type: Object,
      default: () => ({})
    }
  },

  data() {
    return {
      form: {
        name: this.user.name,
        email: this.user.email,
        role: this.user.role,
        teams: this.userTeams || []
      },
      processing: false,
      confirmingDeletion: false
    }
  },

  methods: {
    updateUser() {
      this.processing = true;
      router.put(route('admin.users.update', this.user.id), this.form, {
        onSuccess: () => {
          this.processing = false;
        },
        onError: () => {
          this.processing = false;
        }
      });
    },

    sendPasswordReset() {
      if (confirm('Voulez-vous vraiment envoyer un email de réinitialisation de mot de passe à cet utilisateur ?')) {
        router.post(route('admin.users.send-password-reset', this.user.id), {}, {
          preserveScroll: true,
          onSuccess: () => {
            // Afficher un message de succès
          }
        });
      }
    },

    impersonateUser() {
      if (confirm(`Vous allez vous connecter en tant que ${this.user.name}. Continuer ?`)) {
        router.post(route('admin.users.impersonate', this.user.id));
      }
    },

    confirmUserDeletion() {
      this.confirmingDeletion = true;
    },

    deleteUser() {
      this.processing = true;
      router.delete(route('admin.users.destroy', this.user.id), {
        onSuccess: () => {
          this.processing = false;
          router.visit(route('admin.users.list'));
        },
        onError: () => {
          this.processing = false;
        }
      });
    },

    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      }).format(date);
    }
  }
})
</script>
