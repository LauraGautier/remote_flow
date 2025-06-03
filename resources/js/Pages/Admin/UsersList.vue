<template>
  <AppLayout title="Gestion des utilisateurs">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          Gestion des utilisateurs
        </h2>
        <Link :href="route('admin.users.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 dark:focus:ring-blue-700 disabled:opacity-25 transition">
          <i class="fas fa-user-plus mr-2"></i> Nouvel utilisateur
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Filtres -->
          <div class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-1">
              <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rechercher</label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                </div>
                <input
                  type="text"
                  name="search"
                  id="search"
                  v-model="form.search"
                  class="focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md"
                  placeholder="Nom ou email"
                  @keyup.enter="search"
                >
              </div>
            </div>
            <div class="w-full md:w-1/4">
              <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rôle</label>
              <select
                id="role"
                name="role"
                v-model="form.role"
                class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 dark:focus:border-blue-600 sm:text-sm"
                @change="search"
              >
                <option value="all">Tous les rôles</option>
                <option value="administrateur">Administrateur</option>
                <option value="manager">Manager</option>
                <option value="collaborateur">Collaborateur</option>
              </select>
            </div>
            <div class="w-full md:w-auto md:self-end">
              <button
                type="button"
                class="w-full md:w-auto inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-blue-500"
                @click="resetFilters"
              >
                <i class="fas fa-times mr-2"></i> Réinitialiser
              </button>
            </div>
          </div>

          <!-- Tableau des utilisateurs -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nom</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rôle</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Équipes</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date d'inscription</th>
                  <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="user in users.data" :key="user.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="h-10 w-10 flex-shrink-0">
                        <img
                          :src="user.profile_photo_url"
                          alt=""
                          class="h-10 w-10 rounded-full"
                        >
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ user.name }}</div>
                        <div v-if="user.id === $page.props.auth.user.id" class="text-xs text-gray-500 dark:text-gray-400">(Vous)</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ user.email }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="getRoleBadgeClass(user.role)"
                    >
                      {{ user.role }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ user.teams_count }} équipe(s)
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDate(user.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <Link
                      :href="route('admin.users.edit', user.id)"
                      class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3"
                    >
                      Modifier
                    </Link>

                    <button
                      v-if="user.id !== $page.props.auth.user.id"
                      type="button"
                      class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                      @click="confirmUserDeletion(user)"
                    >
                      Supprimer
                    </button>
                  </td>
                </tr>
                <tr v-if="users.data.length === 0">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center" colspan="6">
                    Aucun utilisateur trouvé
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-6 px-4 py-3 bg-gray-50 dark:bg-gray-700 text-xs font-medium uppercase text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center">
              <div>
              Affichage de {{ users.from }} à {{ users.to }} sur {{ users.total }} résultats
              </div>
              <div class="flex gap-2">
              <Link
                  v-if="users.prev_page_url"
                  :href="users.prev_page_url"
                  class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                  Précédent
              </Link>
              <Link
                  v-if="users.next_page_url"
                  :href="users.next_page_url"
                  class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                  Suivant
              </Link>
              </div>
          </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <ConfirmationModal :show="confirmingUserDeletion" @close="closeModal">
      <template #title>
        Supprimer l'utilisateur
      </template>
      <template #content>
        Êtes-vous sûr de vouloir supprimer l'utilisateur <span class="font-semibold">{{ userToDelete ? userToDelete.name : '' }}</span> ?
        <div class="mt-2 text-sm text-red-600 dark:text-red-400">
          Cette action est irréversible. Toutes les données associées à cet utilisateur seront supprimées.
        </div>
      </template>
      <template #footer>
        <SecondaryButton @click="closeModal">
          Annuler
        </SecondaryButton>

        <DangerButton
          class="ml-2"
          :class="{ 'opacity-25': processing }"
          :disabled="processing"
          @click="deleteUser"
        >
          Supprimer
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script>
import { defineComponent } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'

export default defineComponent({
  components: {
    AppLayout,
    Link,
    Pagination,
    ConfirmationModal,
    SecondaryButton,
    DangerButton
  },

  props: {
    users: Object,
    filters: Object
  },

  data() {
    return {
      form: {
        search: this.filters.search || '',
        role: this.filters.role || 'all'
      },
      confirmingUserDeletion: false,
      userToDelete: null,
      processing: false
    }
  },

  methods: {
    search() {
      router.get(route('admin.users.list'), this.form, {
        preserveState: true,
        replace: true
      })
    },

    resetFilters() {
      this.form = {
        search: '',
        role: 'all'
      }
      this.search()
    },

    getRoleBadgeClass(role) {
      switch (role) {
        case 'administrateur':
          return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        case 'manager':
          return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'collaborateur':
          return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        default:
          return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
      }
    },

    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      }).format(date);
    },

    confirmUserDeletion(user) {
      this.userToDelete = user;
      this.confirmingUserDeletion = true;
    },

    closeModal() {
      this.confirmingUserDeletion = false;
      setTimeout(() => {
        this.userToDelete = null;
      }, 300);
    },

    deleteUser() {
      this.processing = true;
      router.delete(route('admin.users.destroy', this.userToDelete.id), {
        onSuccess: () => {
          this.closeModal();
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
