<template>
    <app-layout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Modifier l'équipe "{{ team.name }}"
        </h2>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form @submit.prevent="submit">
              <div class="mb-6">
                <label class="block text-gray-700">Nom de l'équipe</label>
                <input type="text" v-model="form.name" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                <div v-if="form.errors.name" class="text-red-500 mt-1">{{ form.errors.name }}</div>
              </div>

              <div class="mb-6" v-if="!team.personal_team">
                <label class="block text-gray-700">Propriétaire de l'équipe</label>
                <select v-model="form.owner_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }} ({{ user.email }}) - {{ user.role }}
                  </option>
                </select>
                <div v-if="form.errors.owner_id" class="text-red-500 mt-1">{{ form.errors.owner_id }}</div>
              </div>

              <div class="flex justify-end">
                <Link :href="route('admin.teams.show', team.id)" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                  Annuler
                </Link>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150">
                  Enregistrer les modifications
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
  import { useForm, Link } from '@inertiajs/vue3'

  export default {
    components: {
      AppLayout,
      Link
    },
    props: {
      team: Object,
      users: Array,
    },
    setup(props) {
      const form = useForm({
        name: props.team.name,
        owner_id: props.team.user_id,
      })

      const submit = () => {
        form.put(route('admin.teams.update', props.team.id))
      }

      return { form, submit }
    }
  }
  </script>
