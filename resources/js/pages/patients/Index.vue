<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Edit, Trash2, Eye, Plus } from 'lucide-vue-next';
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
  DialogTrigger,
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
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { toast } from 'vue-sonner'
import { ref } from 'vue'

const props = defineProps<{
  patients?: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
  {
    title: 'Patients',
  },
];

const patientsList = props.patients || [];

// Add Modal State
const isAddDialogOpen = ref(false)
const addForm = ref({
  number: '',
  name: '',
  age: '',
  gender: '',
  phone_number: '',
  residence: '',
})

// Edit Modal State
const isEditDialogOpen = ref(false)
const editingPatient = ref<any>(null)
const editForm = ref({
  id: '',
  number: '',
  name: '',
  age: '',
  gender: '',
  phone_number: '',
  residence: '',
})

// View Modal State
const isViewDialogOpen = ref(false)
const viewingPatient = ref<any>(null)

// Delete Modal State
const isDeleteDialogOpen = ref(false)
const deletingPatient = ref<any>(null)

// Open Add Dialog
const openAddDialog = () => {
  addForm.value = {
    number: '',
    name: '',
    age: '',
    gender: '',
    phone_number: '',
    residence: '',
  }
  isAddDialogOpen.value = true
}

// Handle Add Submit
const handleAddSubmit = () => {
  const payload = {
    number: addForm.value.number,
    name: addForm.value.name,
    age: parseInt(addForm.value.age),
    gender: addForm.value.gender,
    phone_number: addForm.value.phone_number,
    residence: addForm.value.residence,
  }

  console.log('Add Patient Payload:', payload)

  // Show success toast
  toast.success('Patient Added', {
    description: `${addForm.value.name} has been added successfully.`,
  })

  // Close dialog
  isAddDialogOpen.value = false
}

// Close Add Dialog
const closeAddDialog = () => {
  isAddDialogOpen.value = false
}

// Open Edit Dialog
const openEditDialog = (patient: any) => {
  editingPatient.value = patient
  editForm.value = {
    id: patient.id,
    number: patient.number,
    name: patient.name,
    age: patient.age.toString(),
    gender: patient.gender,
    phone_number: patient.phone_number,
    residence: patient.residence,
  }
  isEditDialogOpen.value = true
}

// Handle Edit Submit
const handleEditSubmit = () => {
  const payload = {
    id: editForm.value.id,
    number: editForm.value.number,
    name: editForm.value.name,
    age: parseInt(editForm.value.age),
    gender: editForm.value.gender,
    phone_number: editForm.value.phone_number,
    residence: editForm.value.residence,
  }

  console.log('Edit Payload:', payload)

  // Show success toast with Sonner
  toast.success('Patient Updated', {
    description: `${editForm.value.name}'s information has been updated successfully.`,
  })

  // Close dialog after logging
  isEditDialogOpen.value = false
}

// Close Edit Dialog
const closeEditDialog = () => {
  isEditDialogOpen.value = false
  editingPatient.value = null
}

// Open View Dialog
const openViewDialog = (patient: any) => {
  viewingPatient.value = patient
  isViewDialogOpen.value = true
}

// Close View Dialog
const closeViewDialog = () => {
  isViewDialogOpen.value = false
  viewingPatient.value = null
}

// Open Delete Dialog
const openDeleteDialog = (patient: any) => {
  deletingPatient.value = patient
  isDeleteDialogOpen.value = true
}

// Handle Delete Confirm
const handleDeleteConfirm = () => {
  const payload = {
    id: deletingPatient.value.id,
    number: deletingPatient.value.number,
    name: deletingPatient.value.name,
  }

  console.log('Delete Payload:', payload)

  // Show success toast
  toast.success('Patient Deleted', {
    description: `${deletingPatient.value.name} has been deleted successfully.`,
  })

  // Close dialog
  isDeleteDialogOpen.value = false
  deletingPatient.value = null
}

// Close Delete Dialog
const closeDeleteDialog = () => {
  isDeleteDialogOpen.value = false
  deletingPatient.value = null
}
</script>

<template>
  <Head title="Patients" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <!-- Stats Cards -->
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative overflow-hidden rounded-xl border border-gray-200 p-6 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Patients</div>
          <div class="text-2xl font-bold">{{ patientsList.length }}</div>
        </div>
        <div class="relative overflow-hidden rounded-xl border border-gray-200 p-6 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Male Patients</div>
          <div class="text-2xl font-bold">{{ patientsList.filter(p => p.gender === 'male').length }}</div>
        </div>
        <div class="relative overflow-hidden rounded-xl border border-gray-200 p-6 bg-white dark:border-gray-700 dark:bg-gray-800">
          <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Female Patients</div>
          <div class="text-2xl font-bold">{{ patientsList.filter(p => p.gender === 'female').length }}</div>
        </div>
      </div>

      <!-- Patients Table -->
      <div class="relative min-h-[100vh] flex-1 rounded-xl border border-gray-200 bg-white md:min-h-min dark:border-gray-700 dark:bg-gray-800">
        <div class="p-6">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold">Patients List</h2>
            <Button @click="openAddDialog">
              <Plus class="mr-2 h-4 w-4" />
              Add Patient
            </Button>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>#</TableHead>
                  <TableHead>Patient No</TableHead>
                  <TableHead>Name</TableHead>
                  <TableHead>Age</TableHead>
                  <TableHead>Gender</TableHead>
                  <TableHead>Phone</TableHead>
                  <TableHead>Residence</TableHead>
                  <TableHead class="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-if="patientsList.length === 0">
                  <TableCell colspan="8" class="text-center text-gray-500 dark:text-gray-400 h-24">
                    No patients found.
                  </TableCell>
                </TableRow>
                <TableRow v-for="(patient, index) in patientsList" :key="patient.id">
                  <TableCell class="font-medium">
                    {{ index + 1 }}
                  </TableCell>
                  <TableCell class="font-medium">
                    {{ patient.number }}
                  </TableCell>
                  <TableCell>
                    {{ patient.name }}
                  </TableCell>
                  <TableCell>
                    {{ patient.age }}
                  </TableCell>
                  <TableCell>
                    <span
                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold capitalize"
                        :class="{
                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': patient.gender === 'male',
                        'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200': patient.gender === 'female'
                      }"
                    >
                      {{ patient.gender }}
                    </span>
                  </TableCell>
                  <TableCell>
                    {{ patient.phone_number }}
                  </TableCell>
                  <TableCell>
                    {{ patient.residence }}
                  </TableCell>
                  <TableCell class="text-right">
                    <div class="flex items-center justify-end gap-2">
                      <button
                          @click="openViewDialog(patient)"
                          class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                      >
                        <Eye class="h-4 w-4" />
                        View
                      </button>
                      <button
                          @click="openEditDialog(patient)"
                          class="inline-flex items-center gap-1 rounded-md bg-blue-100 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800"
                      >
                        <Edit class="h-4 w-4" />
                        Edit
                      </button>
                      <button
                          @click="openDeleteDialog(patient)"
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
            Showing {{ patientsList.length }} patients
          </div>
        </div>
      </div>
    </div>

    <!-- Add Patient Dialog -->
    <Dialog v-model:open="isAddDialogOpen">
      <DialogContent class="sm:max-w-[600px]" @interact-outside="(e) => e.preventDefault()">
        <DialogHeader>
          <DialogTitle>Add New Patient</DialogTitle>
          <DialogDescription>
            Enter the patient information below. Click save when you're done.
          </DialogDescription>
        </DialogHeader>

        <form @submit.prevent="handleAddSubmit">
          <div class="grid gap-4 py-4">
            <!-- Row 1: Patient Number and Name -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="add-number">Patient Number</Label>
                <Input
                    id="add-number"
                    v-model="addForm.number"
                    placeholder="Enter patient number"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="add-name">Name</Label>
                <Input
                    id="add-name"
                    v-model="addForm.name"
                    placeholder="Enter patient name"
                    required
                />
              </div>
            </div>

            <!-- Row 2: Age and Gender -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="add-age">Age</Label>
                <Input
                    id="add-age"
                    v-model="addForm.age"
                    type="number"
                    placeholder="Enter age"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="add-gender">Gender</Label>
                <Select v-model="addForm.gender" required>
                  <SelectTrigger id="add-gender">
                    <SelectValue placeholder="Select gender" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="male">Male</SelectItem>
                    <SelectItem value="female">Female</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            <!-- Row 3: Phone Number and Residence -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="add-phone">Phone Number</Label>
                <Input
                    id="add-phone"
                    v-model="addForm.phone_number"
                    placeholder="Enter phone number"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="add-residence">Residence</Label>
                <Input
                    id="add-residence"
                    v-model="addForm.residence"
                    placeholder="Enter residence"
                    required
                />
              </div>
            </div>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="closeAddDialog">
              Cancel
            </Button>
            <Button type="submit">
              Save Patient
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Edit Patient Dialog -->
    <Dialog v-model:open="isEditDialogOpen">
      <DialogContent class="sm:max-w-[600px]" @interact-outside="(e) => e.preventDefault()">
        <DialogHeader>
          <DialogTitle>Edit Patient</DialogTitle>
          <DialogDescription>
            Make changes to the patient information. Click save when you're done.
          </DialogDescription>
        </DialogHeader>

        <form @submit.prevent="handleEditSubmit">
          <div class="grid gap-4 py-4">
            <!-- Row 1: Patient Number and Name -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="edit-number">Patient Number</Label>
                <Input
                    id="edit-number"
                    v-model="editForm.number"
                    placeholder="Enter patient number"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="edit-name">Name</Label>
                <Input
                    id="edit-name"
                    v-model="editForm.name"
                    placeholder="Enter patient name"
                    required
                />
              </div>
            </div>

            <!-- Row 2: Age and Gender -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="edit-age">Age</Label>
                <Input
                    id="edit-age"
                    v-model="editForm.age"
                    type="number"
                    placeholder="Enter age"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="edit-gender">Gender</Label>
                <Select v-model="editForm.gender" required>
                  <SelectTrigger id="edit-gender">
                    <SelectValue placeholder="Select gender" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="male">Male</SelectItem>
                    <SelectItem value="female">Female</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            <!-- Row 3: Phone Number and Residence -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="edit-phone">Phone Number</Label>
                <Input
                    id="edit-phone"
                    v-model="editForm.phone_number"
                    placeholder="Enter phone number"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="edit-residence">Residence</Label>
                <Input
                    id="edit-residence"
                    v-model="editForm.residence"
                    placeholder="Enter residence"
                    required
                />
              </div>
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

    <!-- View Patient Dialog -->
    <Dialog v-model:open="isViewDialogOpen">
      <DialogContent class="sm:max-w-[600px]" @interact-outside="(e) => e.preventDefault()">
        <DialogHeader>
          <DialogTitle>Patient Details</DialogTitle>
          <DialogDescription>
            View patient information.
          </DialogDescription>
        </DialogHeader>

        <div v-if="viewingPatient" class="grid gap-4 py-4">
          <!-- Row 1: Patient Number and Name -->
          <div class="grid grid-cols-2 gap-4">
            <div class="grid gap-2">
              <Label class="text-muted-foreground">Patient Number</Label>
              <p class="text-sm font-medium">{{ viewingPatient.number }}</p>
            </div>

            <div class="grid gap-2">
              <Label class="text-muted-foreground">Name</Label>
              <p class="text-sm font-medium">{{ viewingPatient.name }}</p>
            </div>
          </div>

          <!-- Row 2: Age and Gender -->
          <div class="grid grid-cols-2 gap-4">
            <div class="grid gap-2">
              <Label class="text-muted-foreground">Age</Label>
              <p class="text-sm font-medium">{{ viewingPatient.age }}</p>
            </div>

            <div class="grid gap-2">
              <Label class="text-muted-foreground">Gender</Label>
              <p class="text-sm font-medium capitalize">{{ viewingPatient.gender }}</p>
            </div>
          </div>

          <!-- Row 3: Phone Number and Residence -->
          <div class="grid grid-cols-2 gap-4">
            <div class="grid gap-2">
              <Label class="text-muted-foreground">Phone Number</Label>
              <p class="text-sm font-medium">{{ viewingPatient.phone_number }}</p>
            </div>

            <div class="grid gap-2">
              <Label class="text-muted-foreground">Residence</Label>
              <p class="text-sm font-medium">{{ viewingPatient.residence }}</p>
            </div>
          </div>
        </div>

        <DialogFooter>
          <Button type="button" @click="closeViewDialog">
            Close
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Delete Patient Alert Dialog -->
    <AlertDialog v-model:open="isDeleteDialogOpen">
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
          <AlertDialogDescription>
            This action cannot be undone. This will permanently delete
            <span v-if="deletingPatient" class="font-semibold">{{ deletingPatient.name }}'s</span>
            record from the system.
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