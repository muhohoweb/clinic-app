<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
  Bell,
  Mail,
  Settings,
  BarChart3,
  PieChart,
  TrendingUp,
  Users
} from 'lucide-vue-next';

import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
];

// Report Scheduling Form
const isEnabled = ref(false);

const reportForm = useForm({
  email: '',
  frequency: 'daily',
  includePatientStats: true,
  includeVisitStats: true,
  includeCharts: true,
});

const handleReportScheduleSubmit = () => {
  // Add the enabled state to the form data when submitting
  const formData = {
    enabled: isEnabled.value,
    ...reportForm.data()
  };

  reportForm.transform(() => formData).post('/dashboard/schedule-reports', {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Report Schedule Updated', {
        description: 'Your automated report settings have been saved successfully.',
      });
    },
    onError: (errors) => {
      toast.error('Error', {
        description: 'Failed to update report schedule. Please check your inputs.',
      });
    },
  });
};
</script>

<template>
  <Head title="Report" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">

      <!-- Header Section -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Report</h1>
          <p class="text-muted-foreground">Welcome back! Here's your clinic overview.</p>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

        <!-- Automated Report Scheduling Card -->
        <Card class="lg:col-span-1">
          <CardHeader>
            <div class="flex items-center gap-2">
              <Bell class="h-5 w-5" />
              <CardTitle>Automated Reports</CardTitle>
            </div>
            <CardDescription>
              Schedule automated reports to be sent to your email
            </CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <form @submit.prevent="handleReportScheduleSubmit" class="space-y-4">
              <!-- Enable/Disable Toggle -->
              <div class="flex items-center justify-between space-x-2 rounded-lg border p-4 dark:border-gray-700">
                <div class="flex-1 space-y-0.5">
                  <Label class="text-base font-medium">Enable Automated Reports</Label>
                  <p class="text-sm text-muted-foreground">
                    Receive scheduled reports automatically
                  </p>
                </div>
                <Switch
                    v-model:checked="isEnabled"
                    id="report-enabled"
                />
              </div>

              <!-- Email Input -->
              <div class="space-y-2">
                <Label for="report-email">Email Address</Label>
                <div class="relative">
                  <Mail class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                  <Input
                      id="report-email"
                      v-model="reportForm.email"
                      type="email"
                      placeholder="your.email@clinic.com"
                      class="pl-9"
                      :disabled="!isEnabled"
                      required
                  />
                </div>
              </div>

              <!-- Frequency Selection -->
              <div class="space-y-2">
                <Label for="report-frequency">Report Frequency</Label>
                <Select
                    v-model="reportForm.frequency"
                    :disabled="!isEnabled"
                >
                  <SelectTrigger id="report-frequency">
                    <SelectValue placeholder="Select frequency" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="daily">Daily (Every day at 8:00 AM)</SelectItem>
                    <SelectItem value="weekly">Weekly (Every Monday)</SelectItem>
                    <SelectItem value="bi-weekly">Bi-weekly (1st & 15th of month)</SelectItem>
                    <SelectItem value="monthly">Monthly (1st of every month)</SelectItem>
                    <SelectItem value="quarterly">Quarterly (Every 3 months)</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <!-- Report Content Options -->
              <div class="space-y-3">
                <Label class="text-sm font-medium">Include in Report</Label>

                <div class="flex items-center space-x-2">
                  <Switch
                      v-model:checked="reportForm.includePatientStats"
                      id="include-patients"
                      :disabled="!isEnabled"
                  />
                  <Label for="include-patients" class="text-sm font-normal cursor-pointer">
                    Patient Statistics
                  </Label>
                </div>

                <div class="flex items-center space-x-2">
                  <Switch
                      v-model:checked="reportForm.includeVisitStats"
                      id="include-visits"
                      :disabled="!isEnabled"
                  />
                  <Label for="include-visits" class="text-sm font-normal cursor-pointer">
                    Visit Analytics
                  </Label>
                </div>

                <div class="flex items-center space-x-2">
                  <Switch
                      v-model:checked="reportForm.includeCharts"
                      id="include-charts"
                      :disabled="!isEnabled"
                  />
                  <Label for="include-charts" class="text-sm font-normal cursor-pointer">
                    Charts & Visualizations
                  </Label>
                </div>
              </div>

              <Separator />

              <!-- Submit Button -->
              <Button
                  type="submit"
                  class="w-full"
                  :disabled="reportForm.processing || !isEnabled"
              >
                <Settings class="mr-2 h-4 w-4" />
                {{ reportForm.processing ? 'Saving...' : 'Save Schedule' }}
              </Button>
            </form>
          </CardContent>
        </Card>

        <!-- Placeholder Card 1 -->
        <Card class="lg:col-span-1">
          <CardHeader>
            <div class="flex items-center gap-2">
              <BarChart3 class="h-5 w-5 text-muted-foreground" />
              <CardTitle>Visit Analytics</CardTitle>
            </div>
            <CardDescription>Weekly visits overview</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="flex items-center justify-center h-[300px] text-muted-foreground">
              <div class="text-center">
                <BarChart3 class="h-12 w-12 mx-auto mb-2 opacity-20" />
                <p class="text-sm">Charts & visualizations coming soon</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Placeholder Card 2 -->
        <Card class="lg:col-span-1">
          <CardHeader>
            <div class="flex items-center gap-2">
              <PieChart class="h-5 w-5 text-muted-foreground" />
              <CardTitle>Diagnosis Distribution</CardTitle>
            </div>
            <CardDescription>Breakdown by diagnosis type</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="flex items-center justify-center h-[300px] text-muted-foreground">
              <div class="text-center">
                <PieChart class="h-12 w-12 mx-auto mb-2 opacity-20" />
                <p class="text-sm">Distribution charts coming soon</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Placeholder Card 3 -->
        <Card class="lg:col-span-2">
          <CardHeader>
            <div class="flex items-center gap-2">
              <TrendingUp class="h-5 w-5 text-muted-foreground" />
              <CardTitle>Monthly Trends</CardTitle>
            </div>
            <CardDescription>Patient and visit trends over time</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="flex items-center justify-center h-[300px] text-muted-foreground">
              <div class="text-center">
                <TrendingUp class="h-12 w-12 mx-auto mb-2 opacity-20" />
                <p class="text-sm">Trend analysis coming soon</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Placeholder Card 4 -->
        <Card class="lg:col-span-1">
          <CardHeader>
            <div class="flex items-center gap-2">
              <Users class="h-5 w-5 text-muted-foreground" />
              <CardTitle>Patient Overview</CardTitle>
            </div>
            <CardDescription>Patient demographics</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="flex items-center justify-center h-[300px] text-muted-foreground">
              <div class="text-center">
                <Users class="h-12 w-12 mx-auto mb-2 opacity-20" />
                <p class="text-sm">Patient insights coming soon</p>
              </div>
            </div>
          </CardContent>
        </Card>

      </div>
    </div>
  </AppLayout>
</template>
