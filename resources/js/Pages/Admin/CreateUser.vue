<template>
  <AppLayout title="Créer un utilisateur">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Créer un nouvel utilisateur
        </h2>
        <Link :href="route('admin.users.list')" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:border-gray-500 dark:focus:border-gray-400 focus:ring focus:ring-gray-300 dark:focus:ring-gray-700 disabled:opacity-25 transition">
          <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="createUser">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Informations utilisateur -->
              <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2">Informations de l'utilisateur</h3>

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
                  <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mot de passe</label>
                  <input
                    type="password"
                    id="password"
                    v-model="form.password"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300"
                    required
                  >
                  <div v-if="errors.password" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ errors.password }}</div>
                </div>

                <div>
                  <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmation du mot de passe</label>
                  <input
                    type="password"
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300"
                    required
                  >
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
              </div>

              <!-- Gestion des équipes -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b dark:border-gray-700 pb-2">Équipes</h3>

                <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                  <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-4">Assignation aux équipes</h4>

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
                <i class="fas fa-save mr-2"></i> Créer l'utilisateur
              </button>
            </div>
          </form>
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
    teams: Array,
    errors: Object
  },

  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: 'collaborateur',
        teams: []
      },
      processing: false
    }
  },

  methods: {
    createUser() {
      this.processing = true;
      router.post(route('admin.users.store'), this.form, {
        onSuccess: () => {
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
