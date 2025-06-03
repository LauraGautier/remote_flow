<template>
    <AppLayout title="Créer une tâche">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Créer une nouvelle tâche
        </h2>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form @submit.prevent="submit">
              <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Titre</label>
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                  required
                />
                <div v-if="form.errors.title" class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ form.errors.title }}</div>
              </div>

              <!-- Ajouter le champ pour sélectionner le projet -->
              <div class="mb-4">
                <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Projet</label>
                <select
                  id="project_id"
                  v-model="form.project_id"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                  required
                >
                  <option value="" disabled>Sélectionner un projet</option>
                  <option
                    v-for="project in projects"
                    :key="project.id"
                    :value="project.id"
                  >
                    {{ project.name }}
                  </option>
                </select>
                <div v-if="form.errors.project_id" class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ form.errors.project_id }}</div>
              </div>

              <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="4"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                ></textarea>
                <div v-if="form.errors.description" class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ form.errors.description }}</div>
              </div>

              <div class="mb-4">
                <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assigner à</label>
                <select
                  id="assigned_to"
                  v-model="form.assigned_to"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                  required
                >
                  <option value="" disabled>Sélectionner un collaborateur</option>
                  <option
                    v-for="member in teamMembers"
                    :key="member.id"
                    :value="member.id"
                  >
                    {{ member.name }} ({{ member.email }})
                  </option>
                </select>
                <div v-if="form.errors.assigned_to" class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ form.errors.assigned_to }}</div>
              </div>

              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('manager.tasks')"
                  class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors duration-150"
                >
                  Annuler
                </Link>
                <button
                  type="submit"
                  class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors duration-150"
                  :disabled="form.processing"
                >
                  Créer la tâche
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>

  <script>
  // Importations corrigées pour Laravel 11
  import { useForm, Link } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';

  export default {
    components: {
      AppLayout,
      Link,
    },

    props: {
      team: Object,
      teamMembers: Array,
      projects: Array, // Ajoutez cette prop pour recevoir la liste des projets
      selectedProjectId: [String, Number], // Pour pré-sélectionner un projet (optionnel)
    },

    setup(props) {
      const form = useForm({
        title: '',
        description: '',
        assigned_to: '',
        project_id: props.selectedProjectId || '', // Initialiser avec le projet sélectionné s'il existe
        return_to_project: !!props.selectedProjectId, // Option pour rediriger vers le projet après création
      });

      return { form };
    },

    methods: {
      submit() {
        this.form.post(route('tasks.store'));
      },
    },
  }
  </script>
