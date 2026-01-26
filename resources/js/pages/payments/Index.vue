<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Edit, Trash2, Eye, Plus, Search, Smartphone } from 'lucide-vue-next';
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
import { ref, watch, computed } from 'vue'
import axios from 'axios'
import Pagination from '@/components/Pagination.vue'

const props = defineProps<{
  payments?: {
    data: any[]
    links: any[]
  }
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

const paymentsList = computed(() => props.payments?.data || [])
const paginationLinks = computed(() => props.payments?.links || [])

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
const totalCharged = computed(() => paymentsList.value.reduce((sum, p) => sum + Number(p.amount_charged || 0), 0))
const totalPaid = computed(() => paymentsList.value.reduce((sum, p) => sum + Number(p.amount_paid || 0), 0))
const totalBalance = computed(() => paymentsList.value.reduce((sum, p) => sum + Number(p.balance || 0), 0))

// Visit Search State
const visitSearch = ref('')
const searchedVisit = ref<any>(null)
const isSearching = ref(false)

// Add Modal State
const isAddDialogOpen = ref(false)
const addForm = useForm({
  visit_id: '',
  amount_charged: '',
  amount_paid: '',
  mode_of_payment: '',
  mpesa_transaction_id: '',
  mpesa_phone_number: '',
  notes: '',
})

// Edit Modal State
const isEditDialogOpen = ref(false)
const editingPayment = ref<any>(null)
const editForm = useForm({
  id: '',
  visit_id: '',
  amount_charged: '',
  amount_paid: '',
  mode_of_payment: '',
  mpesa_transaction_id: '',
  mpesa_phone_number: '',
  notes: '',
})

// View Modal State
const isViewDialogOpen = ref(false)
const viewingPayment = ref<any>(null)

// Delete Modal State
const isDeleteDialogOpen = ref(false)
const deletingPayment = ref<any>(null)

// M-Pesa STK Push State
const isMpesaDialogOpen = ref(false)
const mpesaForm = ref({
  phone_number: '',
  amount: '',
})
const isSendingSTK = ref(false)

// Search Visit Function
const searchVisit = async () => {
  if (!visitSearch.value.trim()) {
    searchedVisit.value = null
    return
  }

  isSearching.value = true
  try {
    const response = await axios.get('/payments/search-visit', {
      params: { search: visitSearch.value }
    })

    if (response.data.visit) {
      searchedVisit.value = response.data.visit
      addForm.visit_id = response.data.visit.id
      toast.success('Visit Found', {
        description: `${response.data.visit.patient.name} - ${response.data.visit.diagnosis}`,
      })
    } else {
      searchedVisit.value = null
      addForm.visit_id = ''
      toast.error('Visit Not Found', {
        description: 'No visit found for this patient.',
      })
    }
  } catch (error) {
    console.error('Search error:', error)
    toast.error('Search Failed', {
      description: 'Unable to search for visit. Please try again.',
    })
  } finally {
    isSearching.value = false
  }
}

// Watch for visit search input changes
watch(visitSearch, () => {
  if (!visitSearch.value.trim()) {
    searchedVisit.value = null
    addForm.visit_id = ''
  }
})

// Auto-calculate balance
watch(() => [addForm.amount_charged, addForm.amount_paid], () => {
  // Balance is calculated on the backend
})

watch(() => [editForm.amount_charged, editForm.amount_paid], () => {
  // Balance is calculated on the backend
})

// Open Add Dialog
const openAddDialog = () => {
  addForm.reset()
  addForm.clearErrors()
  visitSearch.value = ''
  searchedVisit.value = null
  isAddDialogOpen.value = true
}

// Handle Add Submit
const handleAddSubmit = () => {
  if (!addForm.visit_id) {
    toast.error('Visit Required', {
      description: 'Please search and select a visit first.',
    })
    return
  }

  addForm.post('/payments', {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Payment Added', {
        description: 'Payment has been recorded successfully.',
      })
      isAddDialogOpen.value = false
      addForm.reset()
      visitSearch.value = ''
      searchedVisit.value = null
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
  visitSearch.value = ''
  searchedVisit.value = null
}

// Open Edit Dialog
const openEditDialog = (payment: any) => {
  editingPayment.value = payment
  editForm.id = payment.id
  editForm.visit_id = payment.visit_id
  editForm.amount_charged = payment.amount_charged.toString()
  editForm.amount_paid = payment.amount_paid.toString()
  editForm.mode_of_payment = payment.mode_of_payment
  editForm.mpesa_transaction_id = payment.mpesa_transaction_id || ''
  editForm.mpesa_phone_number = payment.mpesa_phone_number || ''
  editForm.notes = payment.notes || ''
  editForm.clearErrors()
  isEditDialogOpen.value = true
}

// Handle Edit Submit
const handleEditSubmit = () => {
  editForm.put(`/payments/${editForm.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Payment Updated', {
        description: 'Payment information has been updated successfully.',
      })
      isEditDialogOpen.value = false
      editingPayment.value = null
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
  editingPayment.value = null
  editForm.reset()
  editForm.clearErrors()
}

// Open View Dialog
const openViewDialog = (payment: any) => {
  viewingPayment.value = payment
  isViewDialogOpen.value = true
}

// Close View Dialog
const closeViewDialog = () => {
  isViewDialogOpen.value = false
  viewingPayment.value = null
}

// Open Delete Dialog
const openDeleteDialog = (payment: any) => {
  deletingPayment.value = payment
  isDeleteDialogOpen.value = true
}

// Handle Delete Confirm
const handleDeleteConfirm = () => {
  if (!deletingPayment.value) return

  router.delete(`/payments/${deletingPayment.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Payment Deleted', {
        description: 'Payment has been deleted successfully.',
      })
      isDeleteDialogOpen.value = false
      deletingPayment.value = null
    },
    onError: () => {
      toast.error('Delete Failed', {
        description: 'Unable to delete payment. Please try again.',
      })
    },
  })
}

// Close Delete Dialog
const closeDeleteDialog = () => {
  isDeleteDialogOpen.value = false
  deletingPayment.value = null
}

// Open M-Pesa Dialog
const openMpesaDialog = () => {
  mpesaForm.value = {
    phone_number: searchedVisit.value?.patient?.phone_number || '',
    amount: addForm.amount_paid || '',
  }
  isMpesaDialogOpen.value = true
}

// Handle M-Pesa STK Push
const handleMpesaStkPush = async () => {
  if (!mpesaForm.value.phone_number || !mpesaForm.value.amount) {
    toast.error('Missing Information', {
      description: 'Please provide phone number and amount.',
    })
    return
  }

  isSendingSTK.value = true
  try {
    const response = await axios.post('/payments/mpesa-stk-push', mpesaForm.value)

    if (response.data.success) {
      toast.success('STK Push Sent', {
        description: response.data.message,
      })

      // Auto-fill M-Pesa details in the form
      addForm.mpesa_phone_number = mpesaForm.value.phone_number
      addForm.mpesa_transaction_id = response.data.transaction_id
      addForm.mode_of_payment = 'mpesa'

      isMpesaDialogOpen.value = false
    }
  } catch (error) {
    console.error('M-Pesa error:', error)
    toast.error('STK Push Failed', {
      description: 'Unable to send STK push. Please try again.',
    })
  } finally {
    isSendingSTK.value = false
  }
}

// Close M-Pesa Dialog
const closeMpesaDialog = () => {
  isMpesaDialogOpen.value = false
}
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
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold">Payments List</h2>
            <Button @click="openAddDialog">
              <Plus class="mr-2 h-4 w-4" />
              Add Payment
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
                  <TableHead>Visit Details</TableHead>
                  <TableHead class="text-right">Charged</TableHead>
                  <TableHead class="text-right">Paid</TableHead>
                  <TableHead class="text-right">Balance</TableHead>
                  <TableHead>Payment Method</TableHead>
                  <TableHead class="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-if="paymentsList.length === 0">
                  <TableCell colspan="9" class="text-center text-gray-500 dark:text-gray-400 h-24">
                    No payments found.
                  </TableCell>
                </TableRow>
                <TableRow v-for="(payment, index) in paymentsList" :key="payment.id">
                  <TableCell class="font-medium">
                    {{ index + 1 }}
                  </TableCell>
                  <TableCell>
                    {{ formatDate(payment.created_at) }}
                  </TableCell>
                  <TableCell>
                    <div class="font-medium">{{ payment.visit?.patient?.name || 'N/A' }}</div>
                    <div class="text-xs text-gray-500">{{ payment.visit?.patient?.number || '' }}</div>
                  </TableCell>
                  <TableCell>
                    <div class="max-w-xs truncate">{{ payment.visit?.diagnosis || 'N/A' }}</div>
                  </TableCell>
                  <TableCell class="text-right font-medium">
                    {{ formatCurrency(payment.amount_charged) }}
                  </TableCell>
                  <TableCell class="text-right font-medium text-green-600 dark:text-green-400">
                    {{ formatCurrency(payment.amount_paid) }}
                  </TableCell>
                  <TableCell class="text-right font-medium"
                             :class="payment.balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400'"
                  >
                    {{ formatCurrency(payment.balance) }}
                  </TableCell>
                  <TableCell>
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
                  </TableCell>
                  <TableCell class="text-right">
                    <div class="flex items-center justify-end gap-2">
                      <button
                          @click="openViewDialog(payment)"
                          class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                      >
                        <Eye class="h-4 w-4" />
                        View
                      </button>
                      <button
                          @click="openEditDialog(payment)"
                          class="inline-flex items-center gap-1 rounded-md bg-blue-100 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800"
                      >
                        <Edit class="h-4 w-4" />
                        Edit
                      </button>
                      <button
                          @click="openDeleteDialog(payment)"
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

          <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-500 dark:text-gray-400">
              Showing {{ paymentsList.length }} payments on this page
            </div>
            <Pagination :links="paginationLinks" />
          </div>
        </div>
      </div>
    </div>


    <!-- Add Payment Dialog -->
    <Dialog v-model:open="isAddDialogOpen">
      <DialogContent class="sm:max-w-[600px] max-h-[90vh] overflow-y-auto" @interact-outside="(e) => e.preventDefault()">
        <DialogHeader>
          <DialogTitle>Add New Payment</DialogTitle>
          <DialogDescription>
            Search for a patient visit and record payment details.
          </DialogDescription>
        </DialogHeader>

        <form @submit.prevent="handleAddSubmit">
          <div class="grid gap-4 py-4">
            <!-- Visit Search -->
            <div class="grid gap-2">
              <Label for="visit-search">Search Patient (Phone or Patient Number)</Label>
              <div class="flex gap-2">
                <Input
                    id="visit-search"
                    v-model="visitSearch"
                    placeholder="Enter phone number or patient number"
                    @keyup.enter.prevent="searchVisit"
                />
                <Button type="button" @click="searchVisit" :disabled="isSearching">
                  <Search class="h-4 w-4 mr-2" />
                  {{ isSearching ? 'Searching...' : 'Search' }}
                </Button>
              </div>
              <div v-if="searchedVisit" class="p-3 bg-green-50 dark:bg-green-900/20 rounded-md border border-green-200 dark:border-green-800">
                <p class="text-sm font-medium text-green-900 dark:text-green-100">
                  Patient: {{ searchedVisit.patient.name }}
                </p>
                <p class="text-xs text-green-700 dark:text-green-300">
                  {{ searchedVisit.patient.number }} | Diagnosis: {{ searchedVisit.diagnosis }}
                </p>
              </div>
            </div>

            <!-- Row 1: Amount Charged and Amount Paid -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="add-charged">Amount Charged (KES)</Label>
                <Input
                    id="add-charged"
                    v-model="addForm.amount_charged"
                    type="number"
                    step="0.01"
                    placeholder="0.00"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="add-paid">Amount Paid (KES)</Label>
                <Input
                    id="add-paid"
                    v-model="addForm.amount_paid"
                    type="number"
                    step="0.01"
                    placeholder="0.00"
                    required
                />
              </div>
            </div>

            <!-- Row 2: Payment Method -->
            <div class="grid gap-2">
              <Label for="add-method">Payment Method</Label>
              <Select v-model="addForm.mode_of_payment" required>
                <SelectTrigger id="add-method">
                  <SelectValue placeholder="Select payment method" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="cash">Cash</SelectItem>
                  <SelectItem value="mpesa">M-Pesa</SelectItem>
                  <SelectItem value="bank_transfer">Bank Transfer</SelectItem>
                  <SelectItem value="insurance">Insurance</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <!-- M-Pesa STK Push Button -->
            <div v-if="searchedVisit" class="grid gap-2">
              <Button type="button" variant="outline" @click="openMpesaDialog" class="w-full">
                <Smartphone class="mr-2 h-4 w-4" />
                Send M-Pesa STK Push
              </Button>
            </div>

            <!-- M-Pesa Details (shown if mpesa selected) -->
            <div v-if="addForm.mode_of_payment === 'mpesa'" class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="add-mpesa-phone">M-Pesa Phone Number</Label>
                <Input
                    id="add-mpesa-phone"
                    v-model="addForm.mpesa_phone_number"
                    placeholder="254712345678"
                />
              </div>

              <div class="grid gap-2">
                <Label for="add-mpesa-id">M-Pesa Transaction ID</Label>
                <Input
                    id="add-mpesa-id"
                    v-model="addForm.mpesa_transaction_id"
                    placeholder="QGX123ABC"
                />
              </div>
            </div>

            <!-- Notes -->
            <div class="grid gap-2">
              <Label for="add-notes">Notes (Optional)</Label>
              <Textarea
                  id="add-notes"
                  v-model="addForm.notes"
                  placeholder="Add any additional notes"
                  rows="3"
              />
            </div>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="closeAddDialog">
              Cancel
            </Button>
            <Button type="submit" :disabled="!searchedVisit">
              Save Payment
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>


    <!-- M-Pesa STK Push Dialog -->
    <Dialog v-model:open="isMpesaDialogOpen">
      <DialogContent class="sm:max-w-[400px]" @interact-outside="(e) => e.preventDefault()">
        <DialogHeader>
          <DialogTitle>M-Pesa STK Push</DialogTitle>
          <DialogDescription>
            Send payment request to customer's phone
          </DialogDescription>
        </DialogHeader>

        <div class="grid gap-4 py-4">
          <div class="grid gap-2">
            <Label for="mpesa-phone">Phone Number</Label>
            <Input
                id="mpesa-phone"
                v-model="mpesaForm.phone_number"
                placeholder="254712345678"
                required
            />
            <p class="text-xs text-gray-500">Format: 254XXXXXXXXX</p>
          </div>

          <div class="grid gap-2">
            <Label for="mpesa-amount">Amount (KES)</Label>
            <Input
                id="mpesa-amount"
                v-model="mpesaForm.amount"
                type="number"
                step="0.01"
                placeholder="0.00"
                required
            />
          </div>

          <div class="rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-800 dark:bg-blue-950">
            <p class="text-xs text-blue-700 dark:text-blue-300">
              Customer will receive a payment prompt on their phone. Once they enter their PIN, the transaction will be processed.
            </p>
          </div>
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="closeMpesaDialog" :disabled="isSendingSTK">
            Cancel
          </Button>
          <Button type="button" @click="handleMpesaStkPush" :disabled="isSendingSTK">
            <Smartphone class="mr-2 h-4 w-4" />
            {{ isSendingSTK ? 'Sending...' : 'Send STK Push' }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>


    <!-- Edit Payment Dialog -->
    <Dialog v-model:open="isEditDialogOpen">
      <DialogContent class="sm:max-w-[600px] max-h-[90vh] overflow-y-auto" @interact-outside="(e) => e.preventDefault()">
        <DialogHeader>
          <DialogTitle>Edit Payment</DialogTitle>
          <DialogDescription>
            Make changes to the payment information.
          </DialogDescription>
        </DialogHeader>

        <form @submit.prevent="handleEditSubmit">
          <div class="grid gap-4 py-4">
            <!-- Patient Info (Read-only) -->
            <div v-if="editingPayment" class="p-3 bg-gray-50 dark:bg-gray-900/20 rounded-md border border-gray-200 dark:border-gray-700">
              <p class="text-sm font-medium">Patient: {{ editingPayment.visit?.patient?.name }}</p>
              <p class="text-xs text-gray-500">{{ editingPayment.visit?.patient?.number }} | {{ editingPayment.visit?.diagnosis }}</p>
            </div>

            <!-- Row 1: Amount Charged and Amount Paid -->
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="edit-charged">Amount Charged (KES)</Label>
                <Input
                    id="edit-charged"
                    v-model="editForm.amount_charged"
                    type="number"
                    step="0.01"
                    placeholder="0.00"
                    required
                />
              </div>

              <div class="grid gap-2">
                <Label for="edit-paid">Amount Paid (KES)</Label>
                <Input
                    id="edit-paid"
                    v-model="editForm.amount_paid"
                    type="number"
                    step="0.01"
                    placeholder="0.00"
                    required
                />
              </div>
            </div>

            <!-- Row 2: Payment Method -->
            <div class="grid gap-2">
              <Label for="edit-method">Payment Method</Label>
              <Select v-model="editForm.mode_of_payment" required>
                <SelectTrigger id="edit-method">
                  <SelectValue placeholder="Select payment method" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="cash">Cash</SelectItem>
                  <SelectItem value="mpesa">M-Pesa</SelectItem>
                  <SelectItem value="bank_transfer">Bank Transfer</SelectItem>
                  <SelectItem value="insurance">Insurance</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <!-- M-Pesa Details (shown if mpesa selected) -->
            <div v-if="editForm.mode_of_payment === 'mpesa'" class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <Label for="edit-mpesa-phone">M-Pesa Phone Number</Label>
                <Input
                    id="edit-mpesa-phone"
                    v-model="editForm.mpesa_phone_number"
                    placeholder="254712345678"
                />
              </div>

              <div class="grid gap-2">
                <Label for="edit-mpesa-id">M-Pesa Transaction ID</Label>
                <Input
                    id="edit-mpesa-id"
                    v-model="editForm.mpesa_transaction_id"
                    placeholder="QGX123ABC"
                />
              </div>
            </div>

            <!-- Notes -->
            <div class="grid gap-2">
              <Label for="edit-notes">Notes (Optional)</Label>
              <Textarea
                  id="edit-notes"
                  v-model="editForm.notes"
                  placeholder="Add any additional notes"
                  rows="3"
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

    <!-- View Payment Dialog -->
    <Dialog v-model:open="isViewDialogOpen">
      <DialogContent class="sm:max-w-[600px] max-h-[90vh] overflow-y-auto" @interact-outside="(e) => e.preventDefault()">
        <DialogHeader>
          <DialogTitle>Payment Details</DialogTitle>
          <DialogDescription>
            View complete payment information.
          </DialogDescription>
        </DialogHeader>

        <div v-if="viewingPayment" class="grid gap-4 py-4">
          <!-- Patient Header Card -->
          <div class="rounded-lg border border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 dark:border-gray-700 dark:from-blue-950 dark:to-indigo-950">
            <div class="flex items-start justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ viewingPayment.visit?.patient?.name }}
                </h3>
                <div class="mt-1 flex items-center gap-4 text-sm text-gray-600 dark:text-gray-300">
                  <span class="font-medium">{{ viewingPayment.visit?.patient?.number }}</span>
                  <span>•</span>
                  <span>{{ viewingPayment.visit?.patient?.phone_number }}</span>
                </div>
              </div>
              <div class="text-right">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Date</div>
                <div class="text-lg font-semibold text-gray-900 dark:text-white">
                  {{ formatDate(viewingPayment.created_at) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Visit Information -->
          <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <Label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
              Visit Details
            </Label>
            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
              {{ viewingPayment.visit?.diagnosis }}
            </p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Complaints: {{ viewingPayment.visit?.complaints }}
            </p>
          </div>

          <!-- Payment Amounts -->
          <div class="grid grid-cols-3 gap-4">
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700 dark:bg-blue-950">
              <Label class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide">
                Amount Charged
              </Label>
              <p class="mt-2 text-xl font-bold text-blue-700 dark:text-blue-300">
                {{ formatCurrency(viewingPayment.amount_charged) }}
              </p>
            </div>

            <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700 dark:bg-green-950">
              <Label class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wide">
                Amount Paid
              </Label>
              <p class="mt-2 text-xl font-bold text-green-700 dark:text-green-300">
                {{ formatCurrency(viewingPayment.amount_paid) }}
              </p>
            </div>

            <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-700 dark:bg-red-950">
              <Label class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide">
                Balance
              </Label>
              <p class="mt-2 text-xl font-bold text-red-700 dark:text-red-300">
                {{ formatCurrency(viewingPayment.balance) }}
              </p>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <Label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
              Payment Method
            </Label>
            <p class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100 capitalize">
              {{ viewingPayment.mode_of_payment.replace('_', ' ') }}
            </p>
          </div>

          <!-- M-Pesa Details (if applicable) -->
          <div v-if="viewingPayment.mode_of_payment === 'mpesa' && (viewingPayment.mpesa_transaction_id || viewingPayment.mpesa_phone_number)"
               class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-700 dark:bg-blue-950">
            <Label class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide">
              M-Pesa Transaction Details
            </Label>
            <div class="mt-2 space-y-1">
              <p v-if="viewingPayment.mpesa_phone_number" class="text-sm text-blue-900 dark:text-blue-100">
                <span class="font-medium">Phone:</span> {{ viewingPayment.mpesa_phone_number }}
              </p>
              <p v-if="viewingPayment.mpesa_transaction_id" class="text-sm text-blue-900 dark:text-blue-100">
                <span class="font-medium">Transaction ID:</span> {{ viewingPayment.mpesa_transaction_id }}
              </p>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="viewingPayment.notes" class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <Label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
              Notes
            </Label>
            <p class="mt-2 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">
              {{ viewingPayment.notes }}
            </p>
          </div>
        </div>

        <DialogFooter class="gap-2">
          <Button type="button" variant="outline" @click="closeViewDialog">
            Close
          </Button>
          <Button type="button" @click="openEditDialog(viewingPayment); closeViewDialog()">
            <Edit class="mr-2 h-4 w-4" />
            Edit Payment
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>


    <!-- Delete Payment Alert Dialog -->
    <AlertDialog v-model:open="isDeleteDialogOpen">
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
          <AlertDialogDescription>
            This action cannot be undone. This will permanently delete this payment record from the system.
            <span v-if="deletingPayment" class="mt-2 block font-semibold">
              Amount: {{ formatCurrency(deletingPayment.amount_paid) }} for {{ deletingPayment.visit?.patient?.name }}
            </span>
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