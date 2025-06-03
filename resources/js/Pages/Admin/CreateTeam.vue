<template>
  <AppLayout title="Créer une équipe">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Créer une nouvelle équipe
        </h2>
        <Link :href="'/admin/teams'" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:border-gray-500 dark:focus:border-gray-400 focus:ring focus:ring-gray-300 dark:focus:ring-gray-700 disabled:opacity-25 transition">
          <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="createTeam">
            <div class="space-y-6">
              <!-- Nom de l'équipe -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom de l'équipe</label>
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300"
                  required
                >
                <div v-if="errors.name" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ errors.name }}</div>
              </div>

              <!-- Propriétaire de l'équipe -->
              <div>
                <label for="owner_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Propriétaire</label>
                <select
                  id="owner_id"
                  v-model="form.owner_id"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-300"
                  required
                >
                  <option value="" disabled>Sélectionnez un propriétaire</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }} ({{ user.email }}) - {{ user.role }}
                  </option>
                </select>
                <div v-if="errors.owner_id" class="text-red-500 dark:text-red-400 text-sm mt-1">{{ errors.owner_id }}</div>
              </div>
            </div>

            <div class="mt-8 border-t dark:border-gray-600 pt-6 flex justify-end">
              <Link
                href="/admin/teams"
                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-600 mr-3"
              >
                Annuler
              </Link>

              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                :disabled="processing"
              >
                <i class="fas fa-save mr-2"></i> Créer l'équipe
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
    users: Array,
    errors: Object
  },

  data() {
    return {
      form: {
        name: '',
        owner_id: '',
        personal_team: false
      },
      processing: false
    }
  },

  methods: {
    createTeam() {
      this.processing = true;
      router.post('/admin/teams', this.form, {
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
