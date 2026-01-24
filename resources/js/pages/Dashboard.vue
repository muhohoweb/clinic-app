<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Users, Activity, DollarSign, TrendingUp, Calendar, CreditCard } from 'lucide-vue-next';

const props = defineProps<{
  stats?: {
    totalPatients: number;
    totalVisits: number;
    totalPayments: number;
    totalCharged: number;
    totalPaid: number;
    totalBalance: number;
    todayVisits: number;
    todayPayments: number;
    monthVisits: number;
    monthRevenue: number;
    malePatients: number;
    femalePatients: number;
  };
  recentVisits?: any[];
  recentPayments?: any[];
  paymentsByMethod?: any[];
  topDiagnoses?: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
];

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-KE', {
    style: 'currency',
    currency: 'KES'
  }).format(amount || 0);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-KE', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">

      <!-- Stats Cards Row 1 -->
      <div class="grid auto-rows-min gap-4 md:grid-cols-4">
        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-gray-800">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Patients</p>
              <p class="text-2xl font-bold">{{ props.stats?.totalPatients || 0 }}</p>
              <p class="mt-1 text-xs text-gray-500">
                <span class="text-blue-600">{{ props.stats?.malePatients || 0 }}</span> male,
                <span class="text-pink-600">{{ props.stats?.femalePatients || 0 }}</span> female
              </p>
            </div>
            <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900">
              <Users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
            </div>
          </div>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-gray-800">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Visits</p>
              <p class="text-2xl font-bold">{{ props.stats?.totalVisits || 0 }}</p>
              <p class="mt-1 text-xs text-green-600">{{ props.stats?.todayVisits || 0 }} today</p>
            </div>
            <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
              <Activity class="h-6 w-6 text-green-600 dark:text-green-400" />
            </div>
          </div>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-gray-800">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
              <p class="text-2xl font-bold">{{ formatCurrency(props.stats?.totalPaid || 0) }}</p>
              <p class="mt-1 text-xs text-green-600">{{ formatCurrency(props.stats?.todayPayments || 0) }} today</p>
            </div>
            <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900">
              <DollarSign class="h-6 w-6 text-purple-600 dark:text-purple-400" />
            </div>
          </div>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white p-6 dark:border-sidebar-border dark:bg-gray-800">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Outstanding Balance</p>
              <p class="text-2xl font-bold text-red-600">{{ formatCurrency(props.stats?.totalBalance || 0) }}</p>
              <p class="mt-1 text-xs text-gray-500">{{ props.stats?.totalPayments || 0 }} payments</p>
            </div>
            <div class="rounded-full bg-red-100 p-3 dark:bg-red-900">
              <TrendingUp class="h-6 w-6 text-red-600 dark:text-red-400" />
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards Row 2 -->
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-gradient-to-br from-blue-50 to-blue-100 p-6 dark:from-blue-950 dark:to-blue-900">
          <div class="flex items-center gap-3">
            <div class="rounded-full bg-blue-600 p-3">
              <Calendar class="h-5 w-5 text-white" />
            </div>
            <div>
              <p class="text-sm font-medium text-blue-700 dark:text-blue-300">This Month</p>
              <p class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ props.stats?.monthVisits || 0 }} visits</p>
            </div>
          </div>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-gradient-to-br from-green-50 to-green-100 p-6 dark:from-green-950 dark:to-green-900">
          <div class="flex items-center gap-3">
            <div class="rounded-full bg-green-600 p-3">
              <DollarSign class="h-5 w-5 text-white" />
            </div>
            <div>
              <p class="text-sm font-medium text-green-700 dark:text-green-300">Monthly Revenue</p>
              <p class="text-xl font-bold text-green-900 dark:text-green-100">{{ formatCurrency(props.stats?.monthRevenue || 0) }}</p>
            </div>
          </div>
        </div>

        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-gradient-to-br from-purple-50 to-purple-100 p-6 dark:from-purple-950 dark:to-purple-900">
          <div class="flex items-center gap-3">
            <div class="rounded-full bg-purple-600 p-3">
              <CreditCard class="h-5 w-5 text-white" />
            </div>
            <div>
              <p class="text-sm font-medium text-purple-700 dark:text-purple-300">Total Charged</p>
              <p class="text-xl font-bold text-purple-900 dark:text-purple-100">{{ formatCurrency(props.stats?.totalCharged || 0) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity Section -->
      <div class="grid gap-4 md:grid-cols-2">
        <!-- Recent Visits -->
        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-gray-800">
          <div class="border-b border-gray-200 p-4 dark:border-gray-700">
            <h3 class="font-semibold">Recent Visits</h3>
          </div>
          <div class="p-4">
            <div v-if="!props.recentVisits || props.recentVisits.length === 0" class="py-8 text-center text-sm text-gray-500">
              No recent visits
            </div>
            <div v-else class="space-y-3">
              <div v-for="visit in props.recentVisits" :key="visit.id"
                   class="flex items-start justify-between rounded-lg border border-gray-100 p-3 dark:border-gray-700">
                <div class="flex-1">
                  <p class="font-medium text-sm">{{ visit.patient?.name || 'N/A' }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ visit.diagnosis }}</p>
                  <p class="text-xs text-gray-400 mt-1">{{ formatDate(visit.created_at) }}</p>
                </div>
                <span class="ml-2 inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ visit.type_of_diagnosis }}
                                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Payments -->
        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-gray-800">
          <div class="border-b border-gray-200 p-4 dark:border-gray-700">
            <h3 class="font-semibold">Recent Payments</h3>
          </div>
          <div class="p-4">
            <div v-if="!props.recentPayments || props.recentPayments.length === 0" class="py-8 text-center text-sm text-gray-500">
              No recent payments
            </div>
            <div v-else class="space-y-3">
              <div v-for="payment in props.recentPayments" :key="payment.id"
                   class="flex items-start justify-between rounded-lg border border-gray-100 p-3 dark:border-gray-700">
                <div class="flex-1">
                  <p class="font-medium text-sm">{{ payment.visit?.patient?.name || 'N/A' }}</p>
                  <p class="text-xs text-gray-500">{{ formatCurrency(payment.amount_paid) }}</p>
                  <p class="text-xs text-gray-400 mt-1">{{ formatDate(payment.created_at) }}</p>
                </div>
                <span class="ml-2 inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900 dark:text-blue-200 capitalize">
                                    {{ payment.mode_of_payment.replace('_', ' ') }}
                                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Diagnoses & Payment Methods -->
      <div class="grid gap-4 md:grid-cols-2">
        <!-- Top Diagnoses -->
        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-gray-800">
          <div class="border-b border-gray-200 p-4 dark:border-gray-700">
            <h3 class="font-semibold">Top Diagnoses (Last 30 Days)</h3>
          </div>
          <div class="p-4">
            <div v-if="!props.topDiagnoses || props.topDiagnoses.length === 0" class="py-8 text-center text-sm text-gray-500">
              No data available
            </div>
            <div v-else class="space-y-3">
              <div v-for="(diagnosis, index) in props.topDiagnoses" :key="index"
                   class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-medium">{{ diagnosis.diagnosis }}</p>
                </div>
                <span class="ml-2 rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    {{ diagnosis.count }}
                                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Methods -->
        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-gray-800">
          <div class="border-b border-gray-200 p-4 dark:border-gray-700">
            <h3 class="font-semibold">Payment Methods</h3>
          </div>
          <div class="p-4">
            <div v-if="!props.paymentsByMethod || props.paymentsByMethod.length === 0" class="py-8 text-center text-sm text-gray-500">
              No data available
            </div>
            <div v-else class="space-y-3">
              <div v-for="method in props.paymentsByMethod" :key="method.mode_of_payment"
                   class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="h-3 w-3 rounded-full"
                       :class="{
                                             'bg-green-500': method.mode_of_payment === 'cash',
                                             'bg-blue-500': method.mode_of_payment === 'mpesa',
                                             'bg-purple-500': method.mode_of_payment === 'bank_transfer',
                                             'bg-orange-500': method.mode_of_payment === 'insurance'
                                         }"></div>
                  <p class="text-sm font-medium capitalize">{{ method.mode_of_payment.replace('_', ' ') }}</p>
                </div>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    {{ method.count }}
                                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>