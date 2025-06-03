<template>
    <AppLayout :title="`Modifier ${project.name}`">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier le projet</h2>
      </template>

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <form @submit.prevent="submit">
              <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nom du projet</label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  required
                />
                <div v-if="errors.name" class="text-red-500 mt-1 text-sm">{{ errors.name }}</div>
              </div>

              <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                ></textarea>
                <div v-if="errors.description" class="text-red-500 mt-1 text-sm">{{ errors.description }}</div>
              </div>

              <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                <select
                  id="status"
                  v-model="form.status"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  required
                >
                  <option value="active">Actif</option>
                  <option value="on_hold">En pause</option>
                  <option value="completed">Terminé</option>
                </select>
                <div v-if="errors.status" class="text-red-500 mt-1 text-sm">{{ errors.status }}</div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                  <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début</label>
                  <input
                    id="start_date"
                    v-model="form.start_date"
                    type="date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  />
                  <div v-if="errors.start_date" class="text-red-500 mt-1 text-sm">{{ errors.start_date }}</div>
                </div>

                <div class="mb-4">
                  <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin prévue</label>
                  <input
                    id="end_date"
                    v-model="form.end_date"
                    type="date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  />
                  <div v-if="errors.end_date" class="text-red-500 mt-1 text-sm">{{ errors.end_date }}</div>
                </div>
              </div>

              <div class="flex items-center justify-between mt-6">
                <button
                  type="button"
                  @click="confirmDelete"
                  class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition"
                >
                  Supprimer le projet
                </button>

                <div class="flex items-center">
                  <Link
                    :href="route('projects.show', project.id)"
                    class="mr-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 transition"
                  >
                    Annuler
                  </Link>
                  <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 transition"
                    :disabled="processing"
                  >
                    Enregistrer les modifications
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal de confirmation de suppression -->
      <div v-if="showDeleteModal" class="fixed inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showDeleteModal = false"></div>

          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                  <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Supprimer le projet
                  </h3>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500">
                      Êtes-vous sûr de vouloir supprimer ce projet ? Cette action est irréversible et supprimera également toutes les tâches et objectifs associés à ce projet.
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                @click="deleteProject"
              >
                Supprimer
              </button>
              <button
                type="button"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                @click="showDeleteModal = false"
              >
                Annuler
              </button>
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  </template>

  <script>
  import { Link, useForm, router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';

  export default {
    components: {
      AppLayout,
      Link,
    },
    props: {
      project: Object,
      errors: Object,
    },
    data() {
      return {
        showDeleteModal: false,
        processing: false,
      };
    },
    setup(props) {
      const form = useForm({
        name: props.project.name || '',
        description: props.project.description || '',
        status: props.project.status || 'active',
        start_date: props.project.start_date || '',
        end_date: props.project.end_date || '',
      });

      return {
        form,
      };
    },
    methods: {
      submit() {
        this.processing = true;
        this.form.put(route('projects.update', this.project.id), {
          onSuccess: () => {
            this.processing = false;
          },
          onError: () => {
            this.processing = false;
          },
        });
      },
      confirmDelete() {
        this.showDeleteModal = true;
      },
      deleteProject() {
        router.delete(route('projects.destroy', this.project.id), {
          onBefore: () => {
            return confirm('Cette action est irréversible. Êtes-vous sûr de vouloir continuer ?');
          },
        });
        this.showDeleteModal = false;
      },
    },
  };
  </script>
