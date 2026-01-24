<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Edit, Trash2, Eye, DollarSign } from 'lucide-vue-next';

const props = defineProps<{
  payments?: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
  {
    title: 'Payments',
  },
];

const paymentsList = props.payments || [];

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-KE', {
    style: 'currency',
    currency: 'KES'
  }).format(amount);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-KE', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

// Calculate totals
const totalCharged = paymentsList.reduce((sum, p) => sum + Number(p.amount_charged || 0), 0);
const totalPaid = paymentsList.reduce((sum, p) => sum + Number(p.amount_paid || 0), 0);
const totalBalance = paymentsList.reduce((sum, p) => sum + Number(p.balance || 0), 0);
</script>

<template>
  <Head title="Payments" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- Stats Cards -->
      <div class="grid auto-rows-min gap-4 md:grid-cols-4">
        <div class="relative overflow-hidden rounded-xl border border-gray-200 p-6 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Payments</div>
          <div class="text-2xl font-bold">{{ paymentsList.length }}</div>
        </div>
        <div class="relative overflow-hidden rounded-xl border border-blue-200 p-6 bg-blue-50 dark:border-blue-700 dark:bg-blue-950">
          <div class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Charged</div>
          <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ formatCurrency(totalCharged) }}</div>
        </div>
        <div class="relative overflow-hidden rounded-xl border border-green-200 p-6 bg-green-50 dark:border-green-700 dark:bg-green-950">
          <div class="text-sm font-medium text-green-600 dark:text-green-400">Total Paid</div>
          <div class="text-2xl font-bold text-green-700 dark:text-green-300">{{ formatCurrency(totalPaid) }}</div>
        </div>
        <div class="relative overflow-hidden rounded-xl border border-red-200 p-6 bg-red-50 dark:border-red-700 dark:bg-red-950">
          <div class="text-sm font-medium text-red-600 dark:text-red-400">Total Balance</div>
          <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ formatCurrency(totalBalance) }}</div>
        </div>
      </div>

      <!-- Payments Table -->
      <div class="relative min-h-[100vh] flex-1 rounded-xl border border-gray-200 bg-white md:min-h-min dark:border-gray-700 dark:bg-gray-800">
        <div class="p-6">
          <div class="mb-4">
            <h2 class="text-xl font-semibold">Payments List</h2>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">#</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Patient</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Visit Details</th>
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Charged</th>
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Paid</th>
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Balance</th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Payment Method</th>
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
              </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
              <tr v-if="paymentsList.length === 0">
                <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                  No payments found.
                </td>
              </tr>
              <tr v-for="(payment, index) in paymentsList" :key="payment.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                  {{ index + 1 }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                  {{ formatDate(payment.created_at) }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 dark:text-white">
                  {{ payment.visit?.patient?.name || 'N/A' }}
                  <div class="text-xs text-gray-500">{{ payment.visit?.patient?.number || '' }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                  <div class="max-w-xs truncate">{{ payment.visit?.diagnosis || 'N/A' }}</div>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                  {{ formatCurrency(payment.amount_charged) }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium text-green-600 dark:text-green-400">
                  {{ formatCurrency(payment.amount_paid) }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium"
                    :class="payment.balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400'"
                >
                  {{ formatCurrency(payment.balance) }}
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm">
                  <span
                      class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize"
                      :class="{
                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': payment.mode_of_payment === 'cash',
                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': payment.mode_of_payment === 'mpesa',
                        'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': payment.mode_of_payment === 'bank_transfer',
                        'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200': payment.mode_of_payment === 'insurance'
                      }"
                  >
                    {{ payment.mode_of_payment.replace('_', ' ') }}
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
            Showing {{ paymentsList.length }} payments
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>