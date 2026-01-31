<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import {
  Smartphone,
  Key,
  Eye,
  EyeOff,
  AlertCircle,
  CheckCircle2,
  Globe,
  Link as LinkIcon,
  Shield,
  Trash2,
  TestTube,
  Save,
} from 'lucide-vue-next';
import { toast } from 'vue-sonner';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'M-Pesa Configuration',
    href: '/settings/mpesa',
  },
];

// State
const isLoading = ref(false);
const isSaving = ref(false);
const isTesting = ref(false);
const showConsumerKey = ref(false);
const showConsumerSecret = ref(false);
const showPasskey = ref(false);
const hasExistingSettings = ref(false);
const existingSettingsId = ref(null);

// Form data - set environment to production by default
const formData = ref({
  consumer_key: '',
  consumer_secret: '',
  shortcode: '',
  passkey: '',
  base_url: 'https://api.safaricom.co.ke',
  callback_url: '',
  environment: 'production', // Changed to production
});

const errors = ref({
  consumer_key: '',
  consumer_secret: '',
  shortcode: '',
  passkey: '',
  base_url: '',
  callback_url: '',
});

// Settings status
const settingsStatus = ref({
  has_consumer_key: false,
  has_consumer_secret: false,
  has_passkey: false,
});

// Fetch existing settings
const fetchSettings = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get('/mpesa-settings');

    if (response.data.success && response.data.data) {
      const data = response.data.data;
      hasExistingSettings.value = true;
      existingSettingsId.value = data.id;

      formData.value = {
        consumer_key: '',
        consumer_secret: '',
        shortcode: data.shortcode || '',
        passkey: '',
        base_url: data.base_url || 'https://api.safaricom.co.ke',
        callback_url: data.callback_url || '',
        environment: 'production', // Always production
      };

      settingsStatus.value = {
        has_consumer_key: data.has_consumer_key,
        has_consumer_secret: data.has_consumer_secret,
        has_passkey: data.has_passkey,
      };
    } else {
      hasExistingSettings.value = false;
      formData.value.callback_url = window.location.origin + '/api/mpesa/callback';
    }
  } catch (error) {
    console.error('Error fetching settings:', error);
    formData.value.callback_url = window.location.origin + '/api/mpesa/callback';
  } finally {
    isLoading.value = false;
  }
};

// Save settings
const saveSettings = async () => {
  // Reset errors
  errors.value = {
    consumer_key: '',
    consumer_secret: '',
    shortcode: '',
    passkey: '',
    base_url: '',
    callback_url: '',
  };

  // Validation
  if (!formData.value.consumer_key || !formData.value.consumer_secret ||
      !formData.value.shortcode || !formData.value.passkey) {
    toast.error('Validation Error', {
      description: 'Please fill in all required fields.',
    });
    return;
  }

  isSaving.value = true;

  try {
    let response;

    if (hasExistingSettings.value && existingSettingsId.value) {
      response = await axios.put(`/mpesa-settings/${existingSettingsId.value}`, formData.value);
    } else {
      response = await axios.post('/mpesa-settings', formData.value);
    }

    if (response.data.success) {
      toast.success('Settings Saved', {
        description: 'M-Pesa configuration has been saved successfully.',
        action: {
          label: 'Test Connection',
          onClick: () => testConnection(),
        },
      });

      await fetchSettings();
    }
  } catch (error) {
    console.error('Error saving settings:', error);

    if (error.response?.data?.errors) {
      // Map validation errors
      Object.keys(error.response.data.errors).forEach(key => {
        if (errors.value.hasOwnProperty(key)) {
          errors.value[key] = error.response.data.errors[key][0];
        }
      });

      toast.error('Validation Error', {
        description: 'Please check the form for errors.',
      });
    } else {
      toast.error('Error', {
        description: error.response?.data?.message || 'Failed to save settings. Please try again.',
      });
    }
  } finally {
    isSaving.value = false;
  }
};

// Test connection
const testConnection = async () => {
  if (!formData.value.consumer_key || !formData.value.consumer_secret) {
    toast.error('Validation Error', {
      description: 'Please enter Consumer Key and Consumer Secret to test connection.',
    });
    return;
  }

  isTesting.value = true;

  toast.promise(
      axios.post('/mpesa-settings/test-connection', {
        consumer_key: formData.value.consumer_key,
        consumer_secret: formData.value.consumer_secret,
        base_url: formData.value.base_url,
      }),
      {
        loading: 'Testing connection to M-Pesa API...',
        success: (response) => {
          isTesting.value = false;
          return response.data.message || 'Connection successful!';
        },
        error: (error) => {
          isTesting.value = false;
          return error.response?.data?.message || 'Connection test failed';
        },
      }
  );
};

// Delete settings
const deleteSettings = async () => {
  if (!existingSettingsId.value) return;

  if (!confirm('Are you sure you want to delete the M-Pesa configuration?')) {
    return;
  }

  toast.promise(
      axios.delete(`/mpesa-settings/${existingSettingsId.value}`),
      {
        loading: 'Deleting M-Pesa configuration...',
        success: (response) => {
          hasExistingSettings.value = false;
          existingSettingsId.value = null;
          formData.value = {
            consumer_key: '',
            consumer_secret: '',
            shortcode: '',
            passkey: '',
            base_url: 'https://api.safaricom.co.ke',
            callback_url: window.location.origin + '/api/mpesa/callback',
            environment: 'production',
          };
          settingsStatus.value = {
            has_consumer_key: false,
            has_consumer_secret: false,
            has_passkey: false,
          };
          return response.data.message || 'Configuration deleted successfully';
        },
        error: (error) => {
          return error.response?.data?.message || 'Failed to delete configuration';
        },
      }
  );
};

// Load settings on mount
onMounted(() => {
  fetchSettings();
});
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbItems">
    <Head title="M-Pesa Configuration" />

    <h1 class="sr-only">M-Pesa Configuration</h1>

    <SettingsLayout>
      <div class="flex flex-col space-y-6">

        <!-- Header with status badge -->
        <div class="flex items-center justify-between">
          <Heading
              variant="small"
              title="M-Pesa Configuration"
              description="Manage your M-Pesa payment gateway credentials"
          />
          <Badge v-if="hasExistingSettings" variant="default" class="gap-1">
            <CheckCircle2 class="h-3 w-3" />
            Configured
          </Badge>
        </div>

        <!-- Security Warning -->
        <Alert variant="default" class="border-amber-200 bg-amber-50 dark:border-amber-900 dark:bg-amber-950">
          <AlertCircle class="h-4 w-4 text-amber-600" />
          <AlertTitle class="text-amber-800 dark:text-amber-200">Security Notice</AlertTitle>
          <AlertDescription class="text-amber-700 dark:text-amber-300">
            Your M-Pesa credentials are sensitive. Never share them with anyone. All credentials are encrypted and stored securely.
          </AlertDescription>
        </Alert>

        <!-- Two Column Grid for Form Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <!-- Left Column -->
          <div class="space-y-6">

            <!-- Consumer Key -->
            <div class="space-y-2">
              <Label for="consumer-key">Consumer Key *</Label>
              <div class="relative">
                <Key class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                <Input
                    id="consumer-key"
                    v-model="formData.consumer_key"
                    :type="showConsumerKey ? 'text' : 'password'"
                    :placeholder="settingsStatus.has_consumer_key ? '••••••••••••••••' : 'Enter consumer key'"
                    class="pl-9 pr-10"
                    required
                />
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    class="absolute right-1 top-1 h-8 w-8"
                    @click="showConsumerKey = !showConsumerKey"
                >
                  <Eye v-if="!showConsumerKey" class="h-4 w-4" />
                  <EyeOff v-else class="h-4 w-4" />
                </Button>
              </div>
              <InputError :message="errors.consumer_key" />
            </div>

            <!-- Consumer Secret -->
            <div class="space-y-2">
              <Label for="consumer-secret">Consumer Secret *</Label>
              <div class="relative">
                <Shield class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                <Input
                    id="consumer-secret"
                    v-model="formData.consumer_secret"
                    :type="showConsumerSecret ? 'text' : 'password'"
                    :placeholder="settingsStatus.has_consumer_secret ? '••••••••••••••••' : 'Enter consumer secret'"
                    class="pl-9 pr-10"
                    required
                />
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    class="absolute right-1 top-1 h-8 w-8"
                    @click="showConsumerSecret = !showConsumerSecret"
                >
                  <Eye v-if="!showConsumerSecret" class="h-4 w-4" />
                  <EyeOff v-else class="h-4 w-4" />
                </Button>
              </div>
              <InputError :message="errors.consumer_secret" />
            </div>

            <!-- Base URL -->
            <div class="space-y-2">
              <Label for="base-url">API Base URL</Label>
              <div class="relative">
                <Globe class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                <Input
                    id="base-url"
                    v-model="formData.base_url"
                    type="url"
                    class="pl-9"
                    readonly
                />
              </div>
              <InputError :message="errors.base_url" />
            </div>

          </div>

          <!-- Right Column -->
          <div class="space-y-6">

            <!-- Shortcode -->
            <div class="space-y-2">
              <Label for="shortcode">Business Shortcode *</Label>
              <div class="relative">
                <Smartphone class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                <Input
                    id="shortcode"
                    v-model="formData.shortcode"
                    type="text"
                    placeholder="e.g., 174379"
                    class="pl-9"
                    required
                />
              </div>
              <InputError :message="errors.shortcode" />
            </div>

            <!-- Passkey -->
            <div class="space-y-2">
              <Label for="passkey">Passkey (Lipa Na M-Pesa Online) *</Label>
              <div class="relative">
                <Key class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                <Input
                    id="passkey"
                    v-model="formData.passkey"
                    :type="showPasskey ? 'text' : 'password'"
                    :placeholder="settingsStatus.has_passkey ? '••••••••••••••••' : 'Enter passkey'"
                    class="pl-9 pr-10"
                    required
                />
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    class="absolute right-1 top-1 h-8 w-8"
                    @click="showPasskey = !showPasskey"
                >
                  <Eye v-if="!showPasskey" class="h-4 w-4" />
                  <EyeOff v-else class="h-4 w-4" />
                </Button>
              </div>
              <!-- Empty space to match alignment -->
              <div class="min-h-[16px]"></div>
              <InputError :message="errors.passkey" />
            </div>

            <!-- Callback URL -->
            <div class="space-y-2">
              <Label for="callback-url">Callback URL</Label>
              <div class="relative">
                <LinkIcon class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                <Input
                    id="callback-url"
                    v-model="formData.callback_url"
                    type="url"
                    placeholder="/api/mpesa/callback"
                    class="pl-9"
                />
              </div>
              <InputError :message="errors.callback_url" />
            </div>

          </div>

        </div>

        <Separator />

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-4">
          <Button
              type="button"
              @click="saveSettings"
              :disabled="isSaving"
          >
            <Save class="mr-2 h-4 w-4" />
            {{ isSaving ? 'Saving...' : (hasExistingSettings ? 'Update' : 'Save') }}
          </Button>

          <Button
              type="button"
              variant="outline"
              @click="testConnection"
              :disabled="isTesting || !formData.consumer_key || !formData.consumer_secret"
          >
            <TestTube class="mr-2 h-4 w-4" />
            {{ isTesting ? 'Testing...' : 'Test Connection' }}
          </Button>

          <Button
              v-if="hasExistingSettings"
              type="button"
              variant="destructive"
              @click="deleteSettings"
          >
            <Trash2 class="mr-2 h-4 w-4" />
            Delete
          </Button>

          <Transition
              enter-active-class="transition ease-in-out"
              enter-from-class="opacity-0"
              leave-active-class="transition ease-in-out"
              leave-to-class="opacity-0"
          >
            <p
                v-show="!isSaving && hasExistingSettings"
                class="text-sm text-neutral-600"
            >
              Last updated {{ new Date().toLocaleDateString() }}
            </p>
          </Transition>
        </div>

        <Separator />

        <!-- Help Section -->
        <div class="rounded-lg border bg-muted/50 p-4 space-y-3">
          <h4 class="text-sm font-medium">Getting Started</h4>
          <div class="space-y-2 text-sm text-muted-foreground">
            <p>
              <strong>Step 1:</strong> Visit the
              <a href="https://developer.safaricom.co.ke" target="_blank" class="text-primary hover:underline">
                Safaricom Daraja Portal
              </a>
            </p>
            <p><strong>Step 2:</strong> Create a production app and get your credentials</p>
            <p><strong>Step 3:</strong> Configure your callback URL in the Daraja portal</p>
            <p><strong>Step 4:</strong> Save and test your connection</p>
          </div>
        </div>

      </div>
    </SettingsLayout>
  </AppLayout>
</template>