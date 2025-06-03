<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Ajouter un membre à l'équipe "{{ team.name }}"
        </h2>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form @submit.prevent="submit">
              <div class="mb-6">
                <label class="block text-gray-700">Utilisateur</label>
                <select v-model="form.user_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                  <option value="">Sélectionner un utilisateur</option>
                  <option v-for="user in availableUsers" :key="user.id" :value="user.id">
                    {{ user.name }} ({{ user.email }}) - {{ user.role }}
                  </option>
                </select>
                <div v-if="form.errors.user_id" class="text-red-500 mt-1">{{ form.errors.user_id }}</div>
              </div>

              <div class="mb-6">
                <label class="block text-gray-700">Rôle dans l'équipe</label>
                <select v-model="form.role" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                  <option value="administrateur">Administrateur</option>
                  <option value="manager">Manager</option>
                  <option value="collaborateur">Collaborateur</option>
                </select>
                <div v-if="form.errors.role" class="text-red-500 mt-1">{{ form.errors.role }}</div>
              </div>

              <div class="flex justify-end">
                <inertia-link :href="route('admin.teams.show', team.id)" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                  Annuler
                </inertia-link>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150">
                  Ajouter le membre
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </app-layout>
  </template>

  <script>
  import AppLayout from '@/Layouts/AppLayout.vue'
  import { useForm } from '@inertiajs/vue3'

  export default {
    components: {
      AppLayout,
    },
    props: {
      team: Object,
      availableUsers: Array,
    },
    setup(props) {
      const form = useForm({
        user_id: '',
        role: 'collaborateur', // valeur par défaut
      })

      const submit = () => {
        form.post(route('admin.teams.members.store', props.team.id))
      }

      return { form, submit }
    }
  }
  </script>
