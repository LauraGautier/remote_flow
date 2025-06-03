<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    team: Object,
    member: Object,
});

const form = useForm({
    role: props.member.team_role,
});

const submit = () => {
    form.put(route('admin.teams.members.update', [props.team.id, props.member.id]));
};
</script>

<template>
    <AppLayout title="Modifier le membre de l'équipe">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modifier le membre de l'équipe
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                Équipe : {{ team.name }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                Membre : {{ member.name }} ({{ member.email }})
                            </p>
                        </div>

                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                    Rôle dans l'équipe
                                </label>
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="form-select rounded-md shadow-sm w-full"
                                    required
                                >
                                    <option value="administrateur">Administrateur</option>
                                    <option value="manager">Manager</option>
                                    <option value="collaborateur">Collaborateur</option>
                                </select>
                                <div v-if="form.errors.role" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.role }}
                                </div>
                            </div>

                            <div class="flex justify-end gap-3">
                                <Link
                                    :href="route('admin.teams.show', team.id)"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
                                >
                                    Annuler
                                </Link>
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
