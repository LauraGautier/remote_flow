<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Créer un nouveau projet
        </h2>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form @submit.prevent="submit">
              <div class="mb-6">
                <label class="block text-gray-700">Nom du projet</label>
                <input type="text" v-model="form.name" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                <div v-if="form.errors.name" class="text-red-500 mt-1">{{ form.errors.name }}</div>
              </div>

              <div class="mb-6">
                <label class="block text-gray-700">Description</label>
                <textarea v-model="form.description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"></textarea>
                <div v-if="form.errors.description" class="text-red-500 mt-1">{{ form.errors.description }}</div>
              </div>

              <div class="mb-6">
                <label class="block text-gray-700">Équipe</label>
                <select v-model="form.team_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                  <option value="">Sélectionner une équipe</option>
                  <option v-for="team in teams" :key="team.id" :value="team.id">
                    {{ team.name }}
                  </option>
                </select>
                <div v-if="form.errors.team_id" class="text-red-500 mt-1">{{ form.errors.team_id }}</div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-gray-700">Date de début</label>
                  <input type="date" v-model="form.start_date" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                  <div v-if="form.errors.start_date" class="text-red-500 mt-1">{{ form.errors.start_date }}</div>
                </div>

                <div>
                  <label class="block text-gray-700">Date de fin prévue</label>
                  <input type="date" v-model="form.end_date" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                  <div v-if="form.errors.end_date" class="text-red-500 mt-1">{{ form.errors.end_date }}</div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                  <label class="block text-gray-700">Statut</label>
                  <select v-model="form.status" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="active">Actif</option>
                    <option value="on_hold">En pause</option>
                    <option value="completed">Terminé</option>
                  </select>
                  <div v-if="form.errors.status" class="text-red-500 mt-1">{{ form.errors.status }}</div>
                </div>

                <div>
                  <label class="block text-gray-700">Priorité</label>
                  <select v-model="form.priority" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                    <option value="low">Basse</option>
                    <option value="medium">Moyenne</option>
                    <option value="high">Haute</option>
                  </select>
                  <div v-if="form.errors.priority" class="text-red-500 mt-1">{{ form.errors.priority }}</div>
                </div>
              </div>

              <div class="mt-6">
                <label class="block text-gray-700">Utilisateurs assignés</label>
                <div class="mt-2 max-h-60 overflow-y-auto p-2 border border-gray-300 rounded-md">
                  <div v-for="user in users" :key="user.id" class="flex items-center mb-2">
                    <input type="checkbox"
                           :id="'user-' + user.id"
                           :value="user.id"
                           v-model="form.assigned_users"
                           class="mr-2">
                    <label :for="'user-' + user.id">{{ user.name }} ({{ user.email }}) - {{ user.role }}</label>
                  </div>
                </div>
                <div v-if="form.errors.assigned_users" class="text-red-500 mt-1">{{ form.errors.assigned_users }}</div>
              </div>

              <div class="flex justify-end mt-6">
                <inertia-link :href="route('admin.dashboard')" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                  Annuler
                </inertia-link>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150">
                  Créer le projet
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </app-layout>
  </template>

  <script>
  import AppLayout from '@/Layouts/AppLayout'
  import { useForm } from '@inertiajs/vue3'

  export default {
    components: {
      AppLayout,
    },
    props: {
      teams: Array,
      selectedTeamId: Number,
      users: Array,
    },
    setup(props) {
      const form = useForm({
        name: '',
        description: '',
        team_id: props.selectedTeamId || '',
        start_date: '',
        end_date: '',
        status: 'active',
        priority: 'medium',
        assigned_users: [],
      })

      const submit = () => {
        form.post(route('admin.projects.store'))
      }

      return { form, submit }
    }
  }
  </script>
