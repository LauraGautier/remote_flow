<template>
  <AppLayout title="Paramètres système">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Informations système
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Informations système -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Détails de l'application</h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-4">
              <div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Version de Laravel</div>
                <div class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ systemInfo.laravelVersion }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Version de PHP</div>
                <div class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ systemInfo.phpVersion }}</div>
              </div>
            </div>
            <div class="space-y-4">
              <div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Base de données</div>
                <div class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ systemInfo.database }}</div>
              </div>
              <div>
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Environnement</div>
                <div class="mt-1">
                  <span
                    class="px-2 py-1 text-xs rounded-full"
                    :class="getEnvironmentClass(systemInfo.environment)"
                  >
                    {{ systemInfo.environment }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { defineComponent } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

export default defineComponent({
  components: {
    AppLayout
  },

  props: {
    systemInfo: {
      type: Object,
      default: () => ({
        laravelVersion: 'Laravel 12.x',
        phpVersion: 'PHP 8.4.3',
        environment: 'local',
        database: 'mysql'
      })
    },
    maintenance: {
      type: Object,
      default: () => ({
        enabled: false,
        since: null
      })
    }
  },

  data() {
    return {
      showConfirmModal: false,
      processing: false
    }
  },

  methods: {
    getEnvironmentClass(env) {
      switch (env) {
        case 'production':
          return 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300';
        case 'staging':
          return 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300';
        case 'testing':
          return 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300';
        case 'local':
          return 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
        default:
          return 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
      }
    },
  }
})
</script>
