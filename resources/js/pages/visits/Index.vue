<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Edit, Trash2, Eye, Plus, Search } from 'lucide-vue-next';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { toast } from 'vue-sonner'
import { ref, watch } from 'vue'
import axios from 'axios'

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

// Patient Search State
const patientSearch = ref('')
const searchedPatient = ref<any>(null)
const isSearching = ref(false)

// Add Modal State
const isAddDialogOpen = ref(false)
const addForm = useForm({
  patient_id: '',
  complaints: '',
  history_of_presenting_illness: '',
  allergies: '',
  physical_examination: '',
  lab_test: '',
  imaging: '',
  diagnosis: '',
  type_of_diagnosis: '',
  prescriptions: '',
})

// Edit Modal State
const isEditDialogOpen = ref(false)
const editingVisit = ref<any>(null)
const editForm = useForm({
  id: '',
  patient_id: '',
  complaints: '',
  history_of_presenting_illness: '',
  allergies: '',
  physical_examination: '',
  lab_test: '',
  imaging: '',
  diagnosis: '',
  type_of_diagnosis: '',
  prescriptions: '',
})

// View Modal State
const isViewDialogOpen = ref(false)
const viewingVisit = ref<any>(null)

// Delete Modal State
const isDeleteDialogOpen = ref(false)
const deletingVisit = ref<any>(null)

// Search Patient Function
const searchPatient = async () => {
  if (!patientSearch.value.trim()) {
    searchedPatient.value = null
    return
  }

  isSearching.value = true
  try {
    const response = await axios.get('/visits/search-patient', {
      params: { search: patientSearch.value }
    })

    if (response.data.patient) {
      searchedPatient.value = response.data.patient
      addForm.patient_id = response.data.patient.id
      toast.success('Patient Found', {
        description: `${response.data.patient.name} - ${response.data.patient.number}`,
      })
    } else {
      searchedPatient.value = null
      addForm.patient_id = ''
      toast.error('Patient Not Found', {
        description: 'No patient found with that phone number or patient number.',
      })
    }
  } catch (error) {
    console.error('Search error:', error)
    toast.error('Search Failed', {
      description: 'Unable to search for patient. Please try again.',
    })
  } finally {
    isSearching.value = false
  }
}

// Watch for patient search input changes
watch(patientSearch, () => {
  if (!patientSearch.value.trim()) {
    searchedPatient.value = null
    addForm.patient_id = ''
  }
})

// Open Add Dialog
const openAddDialog = () => {
  addForm.reset()
  addForm.clearErrors()
  patientSearch.value = ''
  searchedPatient.value = null
  isAddDialogOpen.value = true
}

// Handle Add Submit
const handleAddSubmit = () => {
  if (!addForm.patient_id) {
    toast.error('Patient Required', {
      description: 'Please search and select a patient first.',
    })
    return
  }

  addForm.post('/visits', {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Visit Added', {
        description: 'Visit has been recorded successfully.',
      })
      isAddDialogOpen.value = false
      addForm.reset()
      patientSearch.value = ''
      searchedPatient.value = null
    },
    onError: (errors) => {
      console.error('Validation errors:', errors)
      toast.error('Validation Error', {
        description: 'Please check the form for errors.',
      })
    },
  })
}

// Close Add Dialog
const closeAddDialog = () => {
  isAddDialogOpen.value = false
  addForm.reset()
  addForm.clearErrors()
  patientSearch.value = ''
  searchedPatient.value = null
}

// Open Edit Dialog
const openEditDialog = (visit: any) => {
  editingVisit.value = visit
  editForm.id = visit.id
  editForm.patient_id = visit.patient_id
  editForm.complaints = visit.complaints
  editForm.history_of_presenting_illness = visit.history_of_presenting_illness
  editForm.allergies = visit.allergies || ''
  editForm.physical_examination = visit.physical_examination
  editForm.lab_test = visit.lab_test
  editForm.imaging = visit.imaging
  editForm.diagnosis = visit.diagnosis
  editForm.type_of_diagnosis = visit.type_of_diagnosis
  editForm.prescriptions = visit.prescriptions
  editForm.clearErrors()
  isEditDialogOpen.value = true
}

// Handle Edit Submit
const handleEditSubmit = () => {
  editForm.put(`/visits/${editForm.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Visit Updated', {
        description: 'Visit information has been updated successfully.',
      })
      isEditDialogOpen.value = false
      editingVisit.value = null
    },
    onError: (errors) => {
      console.error('Validation errors:', errors)
      toast.error('Validation Error', {
        description: 'Please check the form for errors.',
      })
    },
  })
}

// Close Edit Dialog
const closeEditDialog = () => {
  isEditDialogOpen.value = false
  editingVisit.value = null
  editForm.reset()
  editForm.clearErrors()
}

// Open View Dialog
const openViewDialog = (visit: any) => {
  viewingVisit.value = visit
  isViewDialogOpen.value = true
}

// Close View Dialog
const closeViewDialog = () => {
  isViewDialogOpen.value = false
  viewingVisit.value = null
}

// Open Delete Dialog
const openDeleteDialog = (visit: any) => {
  deletingVisit.value = visit
  isDeleteDialogOpen.value = true
}

// Handle Delete Confirm
const handleDeleteConfirm = () => {
  if (!deletingVisit.value) return

  router.delete(`/visits/${deletingVisit.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Visit Deleted', {
        description: 'Visit has been deleted successfully.',
      })
      isDeleteDialogOpen.value = false
      deletingVisit.value = null
    },
    onError: () => {
      toast.error('Delete Failed', {
        description: 'Unable to delete visit. Please try again.',
      })
    },
  })
}

// Close Delete Dialog
const closeDeleteDialog = () => {
  isDeleteDialogOpen.value = false
  deletingVisit.value = null
}
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
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold">Visits List</h2>
            <Button @click="openAddDialog">
              <Plus class="mr-2 h-4 w-4" />
              Add Visit
            </Button>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>#</TableHead>
                  <TableHead>Date</TableHead>
                  <TableHead>Patient</TableHead>
                  <TableHead>Complaints</TableHead>
                  <TableHead>Diagnosis</TableHead>
                  <TableHead>Type</TableHead>
                  <TableHead class="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-if="visitsList.length === 0">
                  <TableCell colspan="7" class="text-center text-gray-500 dark:text-gray-400 h-24">
                    No visits found.
                  </TableCell>
                </TableRow>
                <TableRow v-for="(visit, index) in visitsList" :key="visit.id">
                  <TableCell class="font-medium">
                    {{ index + 1 }}
                  </TableCell>
                  <TableCell>
                    {{ formatDate(visit.created_at) }}
                  </TableCell>
                  <TableCell>
                    <div class="font-medium">{{ visit.patient?.name || 'N/A' }}</div>
                    <div class="text-xs text-gray-500">{{ visit.patient?.number || '' }}</div>
                  </TableCell>
                  <TableCell>
                    <div class="max-w-xs truncate">{{ visit.complaints }}</div>
                  </TableCell>
                  <TableCell>
                    <div class="max-w-xs truncate">{{ visit.diagnosis }}</div>
                  </TableCell>
                  <TableCell>
                    <span
                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
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
                  </TableCell>
                  <TableCell class="text-right">
                    <div class="flex items-center justify-end gap-2">
                      <button
                          @click="openViewDialog(visit)"
                          class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                      >
                        <Eye class="h-4 w-4" />
                        View
                      </button>
                      <button
                          @click="openEditDialog(visit)"
                          class="inline-flex items-center gap-1 rounded-md bg-blue-100 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800"
                      >
                        <Edit class="h-4 w-4" />
                        Edit
                      </button>
                      <button
                          @click="openDeleteDialog(visit)"
                          class="inline-flex items-center gap-1 rounded-md bg-red-100 px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-200 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800"
                      >
                        <Trash2 class="h-4 w-4" />
                        Delete
                      </button>
                    </div>
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
            Showing {{ visitsList.length }} visits
          </div>
        </div>
      </div>
    </div>

    <!-- Add Visit Dialog -->
    <Dialog v-model:open="isAddDialogOpen">
      <DialogContent class="sm:max-w-[700px] max-h-[90vh] overflow-y-auto" @interact-outside="(e) => e.preventDefault()">
        <DialogHeader>
          <DialogTitle>Add New Visit</DialogTitle>
          <DialogDescription>
            Search for a patient and enter visit information below.
          </DialogDescription>
        </DialogHeader>

        <form @submit.prevent="handleAddSubmit">
          <div class="grid gap-4 py-4">
            <!-- Patient Search -->
            <div class="grid gap-2">
              <Label for="patient-search">Search Patient (Phone or Patient Number)</Label>
              <div class="flex gap-2">
                <Input
                    id="patient-search"
                    v-model="patientSearch"
                    placeholder="Enter phone number or patient number"
                    @keyup.enter.prevent="searchPatient"
                />
                <Button type="button" @click="searchPatient" :disabled="isSearching">
                  <Search class="h-4 w-4 mr-2" />
                  {{ isSearching ? 'Searching...' : 'Search' }}
                </Button>
              </div>
              <div v-if="searchedPatient" class="p-3 bg-green-50 dark:bg-green-900/20 rounded-md border border-green-200 dark:border-green-800">
                <p class="text-sm font-medium text-green-900 dark:text-green-100">
                  Patient: {{ searchedPatient.name }}
                </p>
                <p class="text-xs text-green-700 dark:text-green-300">
                  {{ searchedPatient.number }} | {{ searchedPatient.phone_number }}
                </p>
              </div>
            </div>

            <!-- Row 1: Complaints and History -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="add-complaints">Complaints</Label>
                <Textarea
                    id="add-complaints"
                    v-model="addForm.complaints"
                    placeholder="Enter patient complaints"
                    rows="3"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="add-history">History of Presenting Illness</Label>
                <Textarea
                    id="add-history"
                    v-model="addForm.history_of_presenting_illness"
                    placeholder="Enter history"
                    rows="3"
                    required
                />
              </div>
            </div>

            <!-- Row 2: Allergies and Physical Examination -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="add-allergies">Allergies</Label>
                <Textarea
                    id="add-allergies"
                    v-model="addForm.allergies"
                    placeholder="Enter allergies (optional)"
                    rows="3"
                />
              </div>

              <div class="grid gap-2">
                <Label for="add-physical">Physical Examination</Label>
                <Textarea
                    id="add-physical"
                    v-model="addForm.physical_examination"
                    placeholder="Enter physical examination findings"
                    rows="3"
                    required
                />
              </div>
            </div>

            <!-- Row 3: Lab Test and Imaging -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="add-lab">Lab Test</Label>
                <Textarea
                    id="add-lab"
                    v-model="addForm.lab_test"
                    placeholder="Enter lab test results"
                    rows="3"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="add-imaging">Imaging</Label>
                <Textarea
                    id="add-imaging"
                    v-model="addForm.imaging"
                    placeholder="Enter imaging results"
                    rows="3"
                    required
                />
              </div>
            </div>

            <!-- Row 4: Diagnosis and Type -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="add-diagnosis">Diagnosis</Label>
                <Textarea
                    id="add-diagnosis"
                    v-model="addForm.diagnosis"
                    placeholder="Enter diagnosis"
                    rows="3"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="add-type">Type of Diagnosis</Label>
                <Select v-model="addForm.type_of_diagnosis" required>
                  <SelectTrigger id="add-type">
                    <SelectValue placeholder="Select type" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="Clinical">Clinical</SelectItem>
                    <SelectItem value="Laboratory confirmed">Laboratory confirmed</SelectItem>
                    <SelectItem value="Radiological">Radiological</SelectItem>
                    <SelectItem value="Presumptive">Presumptive</SelectItem>
                    <SelectItem value="Differential">Differential</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            <!-- Row 5: Prescriptions -->
            <div class="grid gap-2">
              <Label for="add-prescriptions">Prescriptions</Label>
              <Textarea
                  id="add-prescriptions"
                  v-model="addForm.prescriptions"
                  placeholder="Enter prescriptions"
                  rows="4"
                  required
              />
            </div>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="closeAddDialog">
              Cancel
            </Button>
            <Button type="submit" :disabled="!searchedPatient">
              Save Visit
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Edit Visit Dialog -->
    <Dialog v-model:open="isEditDialogOpen">
      <DialogContent class="sm:max-w-[700px] max-h-[90vh] overflow-y-auto" @interact-outside="(e) => e.preventDefault()">
        <DialogHeader>
          <DialogTitle>Edit Visit</DialogTitle>
          <DialogDescription>
            Make changes to the visit information.
          </DialogDescription>
        </DialogHeader>

        <form @submit.prevent="handleEditSubmit">
          <div class="grid gap-4 py-4">
            <!-- Patient Info (Read-only) -->
            <div v-if="editingVisit" class="p-3 bg-gray-50 dark:bg-gray-900/20 rounded-md border border-gray-200 dark:border-gray-700">
              <p class="text-sm font-medium">Patient: {{ editingVisit.patient?.name }}</p>
              <p class="text-xs text-gray-500">{{ editingVisit.patient?.number }}</p>
            </div>

            <!-- Row 1: Complaints and History -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="edit-complaints">Complaints</Label>
                <Textarea
                    id="edit-complaints"
                    v-model="editForm.complaints"
                    placeholder="Enter patient complaints"
                    rows="3"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="edit-history">History of Presenting Illness</Label>
                <Textarea
                    id="edit-history"
                    v-model="editForm.history_of_presenting_illness"
                    placeholder="Enter history"
                    rows="3"
                    required
                />
              </div>
            </div>

            <!-- Row 2: Allergies and Physical Examination -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="edit-allergies">Allergies</Label>
                <Textarea
                    id="edit-allergies"
                    v-model="editForm.allergies"
                    placeholder="Enter allergies (optional)"
                    rows="3"
                />
              </div>

              <div class="grid gap-2">
                <Label for="edit-physical">Physical Examination</Label>
                <Textarea
                    id="edit-physical"
                    v-model="editForm.physical_examination"
                    placeholder="Enter physical examination findings"
                    rows="3"
                    required
                />
              </div>
            </div>

            <!-- Row 3: Lab Test and Imaging -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="edit-lab">Lab Test</Label>
                <Textarea
                    id="edit-lab"
                    v-model="editForm.lab_test"
                    placeholder="Enter lab test results"
                    rows="3"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="edit-imaging">Imaging</Label>
                <Textarea
                    id="edit-imaging"
                    v-model="editForm.imaging"
                    placeholder="Enter imaging results"
                    rows="3"
                    required
                />
              </div>
            </div>

            <!-- Row 4: Diagnosis and Type -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="edit-diagnosis">Diagnosis</Label>
                <Textarea
                    id="edit-diagnosis"
                    v-model="editForm.diagnosis"
                    placeholder="Enter diagnosis"
                    rows="3"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="edit-type">Type of Diagnosis</Label>
                <Select v-model="editForm.type_of_diagnosis" required>
                  <SelectTrigger id="edit-type">
                    <SelectValue placeholder="Select type" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="Clinical">Clinical</SelectItem>
                    <SelectItem value="Laboratory confirmed">Laboratory confirmed</SelectItem>
                    <SelectItem value="Radiological">Radiological</SelectItem>
                    <SelectItem value="Presumptive">Presumptive</SelectItem>
                    <SelectItem value="Differential">Differential</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            <!-- Row 5: Prescriptions -->
            <div class="grid gap-2">
              <Label for="edit-prescriptions">Prescriptions</Label>
              <Textarea
                  id="edit-prescriptions"
                  v-model="editForm.prescriptions"
                  placeholder="Enter prescriptions"
                  rows="4"
                  required
              />
            </div>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="closeEditDialog">
              Cancel
            </Button>
            <Button type="submit">
              Save Changes
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- View Visit Dialog -->
    <Dialog v-model:open="isViewDialogOpen">
      <DialogContent class="sm:max-w-[900px] max-h-[90vh] overflow-y-auto" @interact-outside="(e) => e.preventDefault()">
        <DialogHeader>
          <DialogTitle>Visit Details</DialogTitle>
          <DialogDescription>
            Complete visit information and patient history
          </DialogDescription>
        </DialogHeader>

        <div v-if="viewingVisit" class="grid gap-6 py-4">
          <!-- Patient Header Card -->
          <div class="rounded-lg border border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 dark:border-gray-700 dark:from-blue-950 dark:to-indigo-950">
            <div class="flex items-start justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ viewingVisit.patient?.name }}
                </h3>
                <div class="mt-1 flex items-center gap-4 text-sm text-gray-600 dark:text-gray-300">
                  <span class="font-medium">{{ viewingVisit.patient?.number }}</span>
                  <span>•</span>
                  <span>{{ viewingVisit.patient?.age }} years old</span>
                  <span>•</span>
                  <span class="capitalize">{{ viewingVisit.patient?.gender }}</span>
                  <span>•</span>
                  <span>{{ viewingVisit.patient?.phone_number }}</span>
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Visit Date</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ formatDate(viewingVisit.created_at) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Current Visit Information -->
          <div class="space-y-4">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">
              Current Visit Information
            </h4>

            <!-- Complaints & History -->
            <div class="grid grid-cols-2 gap-4">
              <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <Label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                  Chief Complaints
                </Label>
                <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                  {{ viewingVisit.complaints }}
                </p>
              </div>

              <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <Label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                  History of Presenting Illness
                </Label>
                <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                  {{ viewingVisit.history_of_presenting_illness }}
                </p>
              </div>
            </div>

            <!-- Allergies & Physical Examination -->
            <div class="grid grid-cols-2 gap-4">
              <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <Label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                  Allergies
                </Label>
                <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                  {{ viewingVisit.allergies || 'No known allergies' }}
                </p>
              </div>

              <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <Label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                  Physical Examination
                </Label>
                <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                  {{ viewingVisit.physical_examination }}
                </p>
              </div>
            </div>

            <!-- Lab Test & Imaging -->
            <div class="grid grid-cols-2 gap-4">
              <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <Label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                  Laboratory Tests
                </Label>
                <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                  {{ viewingVisit.lab_test }}
                </p>
              </div>

              <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <Label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                  Imaging Studies
                </Label>
                <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                  {{ viewingVisit.imaging }}
                </p>
              </div>
            </div>

            <!-- Diagnosis -->
            <div class="rounded-lg border-2 border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-950">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <Label class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wide">
                    Diagnosis
                  </Label>
                  <p class="mt-2 text-sm font-medium text-blue-900 dark:text-blue-100">
                    {{ viewingVisit.diagnosis }}
                  </p>
                </div>
                <span
                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                    :class="{
                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': viewingVisit.type_of_diagnosis === 'Clinical',
                    'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': viewingVisit.type_of_diagnosis === 'Laboratory confirmed',
                    'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': viewingVisit.type_of_diagnosis === 'Radiological',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': viewingVisit.type_of_diagnosis === 'Presumptive',
                    'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': viewingVisit.type_of_diagnosis === 'Differential'
                  }"
                >
                  {{ viewingVisit.type_of_diagnosis }}
                </span>
              </div>
            </div>

            <!-- Prescriptions -->
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950">
              <Label class="text-xs font-semibold text-green-700 dark:text-green-300 uppercase tracking-wide">
                Prescriptions & Treatment Plan
              </Label>
              <p class="mt-2 text-sm text-green-900 dark:text-green-100 whitespace-pre-line">
                {{ viewingVisit.prescriptions }}
              </p>
            </div>
          </div>

          <!-- Patient Visit History -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">
                Previous Visits History
              </h4>
              <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ visitsList.filter(v => v.patient_id === viewingVisit.patient_id && v.id !== viewingVisit.id).length }} previous visit(s)
              </span>
            </div>

            <div class="max-h-[300px] space-y-3 overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
              <div
                  v-if="visitsList.filter(v => v.patient_id === viewingVisit.patient_id && v.id !== viewingVisit.id).length === 0"
                  class="text-center py-8 text-sm text-gray-500 dark:text-gray-400"
              >
                No previous visits recorded for this patient
              </div>

              <div
                  v-for="visit in visitsList.filter(v => v.patient_id === viewingVisit.patient_id && v.id !== viewingVisit.id).sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())"
                  :key="visit.id"
                  class="rounded-md border border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-800"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-2">
                      <span class="text-xs font-semibold text-gray-900 dark:text-white">
                        {{ formatDate(visit.created_at) }}
                      </span>
                      <span
                          class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
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
                    </div>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                      <span class="font-medium">Complaints:</span> {{ visit.complaints }}
                    </p>
                    <p class="mt-1 text-xs font-medium text-gray-900 dark:text-white">
                      <span class="font-normal text-gray-600 dark:text-gray-400">Diagnosis:</span> {{ visit.diagnosis }}
                    </p>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                      <span class="font-medium">Treatment:</span> {{ visit.prescriptions.length > 100 ? visit.prescriptions.substring(0, 100) + '...' : visit.prescriptions }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <DialogFooter class="gap-2">
          <Button type="button" variant="outline" @click="closeViewDialog">
            Close
          </Button>
          <Button type="button" @click="openEditDialog(viewingVisit); closeViewDialog()">
            <Edit class="mr-2 h-4 w-4" />
            Edit Visit
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Delete Visit Alert Dialog -->
    <AlertDialog v-model:open="isDeleteDialogOpen">
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
          <AlertDialogDescription>
            This action cannot be undone. This will permanently delete this visit record from the system.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel @click="closeDeleteDialog">Cancel</AlertDialogCancel>
          <AlertDialogAction @click="handleDeleteConfirm" class="bg-red-600 hover:bg-red-700">
            Delete
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  </AppLayout>
</template>