<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Edit, Trash2, Eye } from 'lucide-vue-next';

const props = defineProps<{
  visits?: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
  {
    title: 'Visits',
  },
];

const visitsList = props.visits || [];

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-KE', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};
</script>

<template>
  <Head title="Visits" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- Stats Cards -->
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative overflow-hidden rounded-xl border border-gray-200 p-6 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Visits</div>
          <div class="text-2xl font-bold">{{ visitsList.length }}</div>
        </div>
        <div class="relative overflow-hidden rounded-xl border border-gray-200 p-6 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">This Month</div>
          <div class="text-2xl font-bold">
            {{ visitsList.filter(v => new Date(v.created_at).getMonth() === new Date().getMonth()).length }}
          </div>
        </div>
        <div class="relative overflow-hidden rounded-xl border border-gray-200 p-6 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Today</div>
          <div class="text-2xl font-bold">
            {{ visitsList.filter(v => new Date(v.created_at).toDateString() === new Date().toDateString()).length }}
          </div>
        </div>
      </div>

      <!-- Visits Table -->
      <div class="relative min-h-[100vh] flex-1 rounded-xl border border-gray-200 bg-white md:min-h-min dark:border-gray-700 dark:bg-gray-800">
        <div class="p-6">
          <div class="mb-4">
            <h2 class="text-xl font-semibold">Visits List</h2>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">#</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Patient</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Complaints</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Diagnosis</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
              </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
              <tr v-if="visitsList.length === 0">
                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                  No visits found.
                </td>
              </tr>
              <tr v-for="(visit, index) in visitsList" :key="visit.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                  {{ index + 1 }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                  {{ formatDate(visit.created_at) }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                  {{ visit.patient?.name || 'N/A' }}
                  <div class="text-xs text-gray-500">{{ visit.patient?.number || '' }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                  <div class="max-w-xs truncate">{{ visit.complaints }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                  <div class="max-w-xs truncate">{{ visit.diagnosis }}</div>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm">
                  <span
                      class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize"
                      :class="{
                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': visit.type_of_diagnosis === 'Clinical',
                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': visit.type_of_diagnosis === 'Laboratory confirmed',
                        'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': visit.type_of_diagnosis === 'Radiological',
                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': visit.type_of_diagnosis === 'Presumptive',
                        'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': visit.type_of_diagnosis === 'Differential'
                      }"
                  >
                    {{ visit.type_of_diagnosis }}
                  </span>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                  <div class="flex items-center justify-end gap-2">
                    <button class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                      <Eye class="h-4 w-4" />
                      View
                    </button>
                    <button class="inline-flex items-center gap-1 rounded-md bg-blue-100 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800">
                      <Edit class="h-4 w-4" />
                      Edit
                    </button>
                    <button class="inline-flex items-center gap-1 rounded-md bg-red-100 px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-200 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800">
                      <Trash2 class="h-4 w-4" />
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            Showing {{ visitsList.length }} visits
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>