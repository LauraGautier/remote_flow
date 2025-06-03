<template>
  <AppLayout title="Créer un projet">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Créer un nouveau projet</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="mb-4">
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom du projet</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                required
              />
              <div v-if="errors.name" class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ errors.name }}</div>
            </div>

            <div class="mb-4">
              <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
              <textarea
                id="description"
                v-model="form.description"
                rows="4"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
              ></textarea>
              <div v-if="errors.description" class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ errors.description }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="mb-4">
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de début</label>
                <input
                  id="start_date"
                  v-model="form.start_date"
                  type="date"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                />
                <div v-if="errors.start_date" class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ errors.start_date }}</div>
              </div>

              <div class="mb-4">
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de fin prévue</label>
                <input
                  id="end_date"
                  v-model="form.end_date"
                  type="date"
                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                />
                <div v-if="errors.end_date" class="text-red-500 dark:text-red-400 mt-1 text-sm">{{ errors.end_date }}</div>
              </div>
            </div>

            <div class="flex items-center justify-end mt-6">
              <Link
                :href="route('projects.index')"
                class="mr-4 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:text-gray-500 dark:hover:text-gray-200 focus:outline-none focus:border-blue-300 dark:focus:border-blue-700 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 active:text-gray-800 dark:active:text-gray-200 active:bg-gray-50 dark:active:bg-gray-700 transition"
              >
                Annuler
              </Link>
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-500 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:border-gray-900 dark:focus:border-gray-800 focus:ring focus:ring-gray-300 dark:focus:ring-gray-700 transition"
                :disabled="processing"
              >
                Créer le projet
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  components: {
    AppLayout,
    Link,
  },
  props: {
    team: Object,
    errors: Object,
  },
  setup() {
    const form = useForm({
      name: '',
      description: '',
      start_date: '',
      end_date: '',
    });

    return {
      form,
      processing: false,
    };
  },
  methods: {
    submit() {
      this.processing = true;
      this.form.post(route('projects.store'), {
        onSuccess: () => {
          this.processing = false;
        },
        onError: () => {
          this.processing = false;
        },
      });
    },
  },
};
</script>
