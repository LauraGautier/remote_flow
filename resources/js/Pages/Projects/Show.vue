<template>
    <AppLayout :title="project.name">
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ project.name }}
          </h2>
          <div v-if="canEdit" class="flex">
            <Link :href="route('projects.edit', project.id)" class="btn btn-secondary mr-2">
              Modifier
            </Link>
          </div>
        </div>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Carte des KPIs -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
            <h3 class="text-lg font-bold mb-4">Indicateurs clés de performance</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Temps passé sur le projet -->
              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-md font-semibold text-gray-700">Temps total passé</h4>
                <p class="text-2xl font-bold mt-2">{{ kpis.totalTimeSpent }}</p>
              </div>

              <!-- Objectifs atteints -->
              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-md font-semibold text-gray-700">Objectifs atteints</h4>
                <div class="flex items-center mt-2">
                  <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="h-4 rounded-full"
                         :class="getProgressColorClass(kpis.objectivesCompletionPercentage)"
                         :style="{ width: `${kpis.objectivesCompletionPercentage}%` }">
                    </div>
                  </div>
                  <span class="ml-2 font-bold">{{ kpis.objectivesCompletionPercentage }}%</span>
                </div>
              </div>
            </div>

            <!-- Statistiques des tâches -->
            <div class="mt-6">
              <h4 class="text-md font-semibold text-gray-700 mb-2">Progression des tâches</h4>
              <div class="grid grid-cols-4 gap-2 text-center">
                <div class="bg-gray-50 p-2 rounded">
                  <p class="text-sm">Total</p>
                  <p class="text-lg font-bold">{{ taskStats.total }}</p>
                </div>
                <div class="bg-yellow-50 p-2 rounded">
                  <p class="text-sm">En attente</p>
                  <p class="text-lg font-bold">{{ taskStats.pending }}</p>
                </div>
                <div class="bg-blue-50 p-2 rounded">
                  <p class="text-sm">En cours</p>
                  <p class="text-lg font-bold">{{ taskStats.in_progress }}</p>
                </div>
                <div class="bg-green-50 p-2 rounded">
                  <p class="text-sm">Terminées</p>
                  <p class="text-lg font-bold">{{ taskStats.completed }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Détails du projet -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
            <h3 class="text-lg font-bold mb-4">Détails du projet</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <p class="text-gray-600">{{ project.description || 'Aucune description fournie.' }}</p>
                <div class="mt-4">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="{
                          'bg-green-100 text-green-800': project.status === 'active',
                          'bg-gray-100 text-gray-800': project.status === 'on_hold',
                          'bg-blue-100 text-blue-800': project.status === 'completed'
                        }">
                    {{ getStatusLabel(project.status) }}
                  </span>
                </div>
              </div>

              <div>
                <p><span class="font-semibold">Créé par:</span> {{ project.creator.name }}</p>
                <p v-if="project.start_date"><span class="font-semibold">Date de début:</span> {{ formatDate(project.start_date) }}</p>
                <p v-if="project.end_date"><span class="font-semibold">Date de fin prévue:</span> {{ formatDate(project.end_date) }}</p>
              </div>
            </div>
          </div>

<!-- Section Objectifs -->
<div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
  <div class="flex justify-between items-center mb-4">
    <h3 class="text-lg font-bold">Objectifs du projet</h3>
    <div v-if="canEdit">
      <button @click="showObjectiveForm = true" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
        Ajouter un objectif
      </button>
    </div>
  </div>

  <!-- Formulaire d'ajout d'objectif -->
  <div v-if="showObjectiveForm && canEdit" class="mb-6 p-4 bg-gray-50 rounded-lg">
    <form @submit.prevent="submitObjective">
      <div class="mb-3">
        <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
        <input
          type="text"
          id="title"
          v-model="objectiveForm.title"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          required
        >
      </div>

      <div class="mb-3">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea
          id="description"
          v-model="objectiveForm.description"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          rows="3"
        ></textarea>
      </div>

      <div class="mb-3">
        <label for="due_date" class="block text-sm font-medium text-gray-700">Date d'échéance</label>
        <input
          type="date"
          id="due_date"
          v-model="objectiveForm.due_date"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        >
      </div>

      <div class="flex justify-end">
        <button
          type="button"
          @click="showObjectiveForm = false"
          class="mr-2 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 transition"
        >
          Annuler
        </button>
        <button
          type="submit"
          class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 transition"
          :disabled="objectiveProcessing"
        >
          Enregistrer
        </button>
      </div>
    </form>
  </div>

  <!-- Liste des objectifs -->
  <div v-if="objectives.length" class="space-y-4">
    <div
      v-for="objective in objectives"
      :key="objective.id"
      class="border p-4 rounded hover:bg-gray-50"
      :class="{ 'border-green-400 bg-green-50': objective.is_completed }"
    >
      <div class="flex items-start justify-between">
        <div>
          <h4 class="font-semibold" :class="{ 'line-through': objective.is_completed }">
            {{ objective.title }}
          </h4>
          <p v-if="objective.description" class="text-gray-600 mt-1">
            {{ objective.description }}
          </p>
          <div class="mt-2 text-sm">
            <span v-if="objective.due_date" class="text-gray-500">
              Échéance: {{ formatDate(objective.due_date) }}
            </span>
            <span v-if="objective.is_completed" class="ml-4 text-green-600">
              Complété le {{ formatDate(objective.completed_at) }}
            </span>
          </div>
        </div>

        <div v-if="canEdit" class="flex items-center">
          <button
            @click="toggleObjectiveCompletion(objective)"
            class="ml-2 p-1 rounded"
            :class="{ 'bg-green-100': !objective.is_completed, 'bg-gray-100': objective.is_completed }"
          >
            <svg v-if="objective.is_completed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </button>
          <button
            @click="deleteObjective(objective)"
            class="ml-2 p-1 rounded bg-red-100 text-red-500"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Message si aucun objectif -->
  <div v-else class="text-center py-8">
    <p class="text-gray-500">Aucun objectif défini pour ce projet</p>
  </div>
</div>

          <!-- Liste des tâches -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-bold">Tâches du projet</h3>
              <div v-if="canEdit">
                <Link :href="route('tasks.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                  Créer une tâche
                </Link>
              </div>
            </div>

            <!-- Liste des tâches -->
            <div v-if="tasks.length" class="space-y-4">
              <div
                v-for="task in tasks"
                :key="task.id"
                class="border p-4 rounded hover:bg-gray-50"
                :class="{
                  'border-yellow-400': task.status === 'pending',
                  'border-blue-400': task.status === 'in_progress',
                  'border-green-400 bg-green-50': task.status === 'completed'
                }"
              >
                <Link :href="route('tasks.show', task.id)">
                  <div class="flex justify-between">
                    <h4 class="font-semibold">{{ task.title }}</h4>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="{
                            'bg-yellow-100 text-yellow-800': task.status === 'pending',
                            'bg-blue-100 text-blue-800': task.status === 'in_progress',
                            'bg-green-100 text-green-800': task.status === 'completed'
                          }">
                      {{ getTaskStatusLabel(task.status) }}
                    </span>
                  </div>
                  <p v-if="task.description" class="text-gray-600 mt-1">
                    {{ task.description }}
                  </p>
                  <div class="mt-2 text-sm flex justify-between">
                    <span>Assignée à: {{ task.assigned_to.name }}</span>
                    <span v-if="task.status === 'completed' && task.start_time && task.end_time">
                      Durée: {{ getTaskDuration(task) }}
                    </span>
                  </div>
                </Link>
              </div>
            </div>

            <!-- Message si aucune tâche -->
            <div v-else class="text-center py-8">
              <p class="text-gray-500">Aucune tâche pour ce projet</p>
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
      project: Object,
      tasks: Array,
      objectives: Array,
      taskStats: Object,
      kpis: Object,
      canEdit: Boolean
    },
    data() {
      return {
        showObjectiveForm: false,
        objectiveForm: {
          title: '',
          description: '',
          due_date: ''
        },
        objectiveProcessing: false
      }
    },
    methods: {
      formatDate(dateString) {
        if (!dateString) return '';
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
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
      getTaskStatusLabel(status) {
        const labels = {
          'pending': 'En attente',
          'in_progress': 'En cours',
          'completed': 'Terminé'
        }
        return labels[status] || status;
      },
      getProgressColorClass(percentage) {
        if (percentage < 33) return 'bg-red-500';
        if (percentage < 66) return 'bg-yellow-500';
        return 'bg-green-500';
      },
      getTaskDuration(task) {
        if (!task.start_time || !task.end_time) return '-';

        const startTime = new Date(task.start_time);
        const endTime = new Date(task.end_time);
        const diffMinutes = Math.floor((endTime - startTime) / (1000 * 60));

        const hours = Math.floor(diffMinutes / 60);
        const minutes = diffMinutes % 60;

        return `${hours}h ${minutes}m`;
      },
      submitObjective() {
        this.objectiveProcessing = true;
        router.post(route('objectives.store', this.project.id), this.objectiveForm, {
          onSuccess: () => {
            this.showObjectiveForm = false;
            this.objectiveForm = {
              title: '',
              description: '',
              due_date: ''
            };
            this.objectiveProcessing = false;
          },
          onError: () => {
            this.objectiveProcessing = false;
          }
        });
      },
      toggleObjectiveCompletion(objective) {
        router.post(route('objectives.toggle', objective.id));
      },
      deleteObjective(objective) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet objectif ?')) {
          router.delete(route('objectives.destroy', objective.id));
        }
      }
    }
  }
  </script>
