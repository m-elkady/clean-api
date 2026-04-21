<template>
  <div>
    <div class="flex-between mb-4">
      <h1 class="text-2xl font-semibold">Users</h1>
      <Button label="New User" icon="pi pi-plus" @click="openCreateDialog" />
    </div>

    <Card>
      <template #content>
        <!-- Search Filters -->
        <div class="grid grid-cols-3 gap-4 mb-4">
          <InputText v-model="filters.firstName" placeholder="Search by first name" />
          <InputText v-model="filters.lastName" placeholder="Search by last name" />
          <InputText v-model="filters.email" placeholder="Search by email" />
        </div>

        <div class="flex gap-2 mb-4">
          <Button label="Search" icon="pi pi-search" @click="applyFilters" />
          <Button label="Clear" icon="pi pi-times" severity="secondary" outlined @click="clearFilters" />
        </div>

        <!-- Users Table -->
        <DataTable
          :value="users"
          :loading="isLoading"
          :paginator="true"
          :rows="params.perPage"
          :total-records="totalRecords"
          :lazy="true"
          @page="onPageChange"
          paginator-template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
          :rows-per-page-options="[10, 20, 50]"
          striped-rows
        >
          <Column field="firstName" header="First Name" sortable>
            <template #body="{ data }">{{ data.firstName }}</template>
          </Column>
          <Column field="lastName" header="Last Name" sortable>
            <template #body="{ data }">{{ data.lastName }}</template>
          </Column>
          <Column field="email" header="Email" sortable>
            <template #body="{ data }">{{ data.email }}</template>
          </Column>
          <Column header="Actions" :exportable="false">
            <template #body="{ data }">
              <Button
                icon="pi pi-pencil"
                outlined
                rounded
                severity="warn"
                class="mr-2"
                @click="openEditDialog(data)"
              />
              <Button
                icon="pi pi-trash"
                outlined
                rounded
                severity="danger"
                @click="confirmDelete(data)"
              />
            </template>
          </Column>
        </DataTable>
      </template>
    </Card>

    <!-- Create/Edit Dialog -->
    <Dialog
      v-model:visible="dialogVisible"
      :header="dialogMode === 'create' ? 'Create New User' : 'Edit User'"
      :style="{ width: '32rem' }"
      :modal="true"
    >
      <form @submit.prevent="saveUser" class="space-y-4">
        <div>
          <label for="firstName" class="block text-sm font-medium mb-2">First Name</label>
          <InputText
            id="firstName"
            v-model="formData.firstName"
            class="w-full"
            :invalid="!!formErrors.firstName"
          />
          <small v-if="formErrors.firstName" class="text-red-500">{{ formErrors.firstName }}</small>
        </div>

        <div>
          <label for="lastName" class="block text-sm font-medium mb-2">Last Name</label>
          <InputText
            id="lastName"
            v-model="formData.lastName"
            class="w-full"
            :invalid="!!formErrors.lastName"
          />
          <small v-if="formErrors.lastName" class="text-red-500">{{ formErrors.lastName }}</small>
        </div>

        <div>
          <label for="email" class="block text-sm font-medium mb-2">Email</label>
          <InputText
            id="email"
            v-model="formData.email"
            type="email"
            class="w-full"
            :invalid="!!formErrors.email"
          />
          <small v-if="formErrors.email" class="text-red-500">{{ formErrors.email }}</small>
        </div>

        <div v-if="dialogMode === 'create'">
          <label for="password" class="block text-sm font-medium mb-2">Password</label>
          <Password
            id="password"
            v-model="formData.password"
            :feedback="false"
            toggle-mask
            class="w-full"
            :invalid="!!formErrors.password"
          />
          <small v-if="formErrors.password" class="text-red-500">{{ formErrors.password }}</small>
        </div>

        <div class="flex justify-end gap-2">
          <Button label="Cancel" severity="secondary" outlined @click="dialogVisible = false" />
          <Button type="submit" label="Save" :loading="isSaving" />
        </div>
      </form>
    </Dialog>

    <!-- Delete Confirmation Dialog -->
    <ConfirmDialog />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive } from 'vue'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Card from 'primevue/card'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import ConfirmDialog from 'primevue/confirmdialog'
import { useUsers, useCreateUser, useUpdateUser, useDeleteUser } from '@/composables/useUsers'
import type { User, UserListParams } from '@/types'

const confirm = useConfirm()
const toast = useToast()

// Query params state
const params = reactive<UserListParams>({
  page: 1,
  perPage: 10,
  sortBy: 'id',
  order: 'asc',
  firstName: '',
  lastName: '',
  email: '',
})

const filters = reactive({
  firstName: '',
  lastName: '',
  email: '',
})

// Fetch users list
const { data, isLoading } = useUsers(params)

const users = computed(() => data.value?.users ?? [])
const totalRecords = computed(() => data.value?.count ?? 0)

// Dialog state
const dialogVisible = ref(false)
const dialogMode = ref<'create' | 'edit'>('create')
const isSaving = ref(false)

// Form data
const formData = reactive({
  id: 0,
  firstName: '',
  lastName: '',
  email: '',
  password: '',
})

// Form errors
const formErrors = reactive<Record<string, string>>({})

// Mutations
const createUserMutation = useCreateUser()
const updateUserMutation = useUpdateUser()
const deleteUserMutation = useDeleteUser()

// Validate form
function validateForm(): boolean {
  const errors: Record<string, string> = {}

  if (!formData.firstName.trim()) {
    errors.firstName = 'First name is required'
  }
  if (!formData.lastName.trim()) {
    errors.lastName = 'Last name is required'
  }
  if (!formData.email.trim()) {
    errors.email = 'Email is required'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
    errors.email = 'Email must be valid'
  }
  if (dialogMode.value === 'create' && !formData.password) {
    errors.password = 'Password is required'
  }

  Object.assign(formErrors, errors)
  return Object.keys(errors).length === 0
}

// Open create dialog
function openCreateDialog() {
  dialogMode.value = 'create'
  Object.assign(formData, {
    id: 0,
    firstName: '',
    lastName: '',
    email: '',
    password: '',
  })
  Object.keys(formErrors).forEach((key) => delete formErrors[key])
  dialogVisible.value = true
}

// Open edit dialog
function openEditDialog(user: User) {
  dialogMode.value = 'edit'
  Object.assign(formData, {
    id: user.id,
    firstName: user.firstName,
    lastName: user.lastName,
    email: user.email,
    password: '',
  })
  Object.keys(formErrors).forEach((key) => delete formErrors[key])
  dialogVisible.value = true
}

// Save user
async function saveUser() {
  if (!validateForm()) return

  isSaving.value = true

  try {
    if (dialogMode.value === 'create') {
      await createUserMutation.mutateAsync({
        firstName: formData.firstName,
        lastName: formData.lastName,
        email: formData.email,
        password: formData.password,
      })
      toast.add({ severity: 'success', summary: 'Success', detail: 'User created successfully', life: 3000 })
    } else {
      await updateUserMutation.mutateAsync({
        id: formData.id,
        data: {
          firstName: formData.firstName,
          lastName: formData.lastName,
          email: formData.email,
        },
      })
      toast.add({ severity: 'success', summary: 'Success', detail: 'User updated successfully', life: 3000 })
    }
    dialogVisible.value = false
  } catch (error: unknown) {
    const message =
      (error as { response?: { data?: { errors?: string | { message?: string } } } })?.response?.data
        ?.errors ?? 'An error occurred'
    const errorMessage = typeof message === 'string' ? message : 'An error occurred'
    toast.add({ severity: 'error', summary: 'Error', detail: errorMessage, life: 3000 })
  } finally {
    isSaving.value = false
  }
}

// Confirm delete
function confirmDelete(user: User) {
  confirm.require({
    message: `Are you sure you want to delete ${user.firstName} ${user.lastName}?`,
    header: 'Delete User',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Delete',
    rejectLabel: 'Cancel',
    acceptProps: {
      severity: 'danger',
    },
    accept: async () => {
      try {
        await deleteUserMutation.mutateAsync(user.id)
        toast.add({ severity: 'success', summary: 'Success', detail: 'User deleted successfully', life: 3000 })
      } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete user', life: 3000 })
      }
    },
  })
}

// Apply filters
function applyFilters() {
  params.firstName = filters.firstName
  params.lastName = filters.lastName
  params.email = filters.email
  params.page = 1
}

// Clear filters
function clearFilters() {
  filters.firstName = ''
  filters.lastName = ''
  filters.email = ''
  params.firstName = ''
  params.lastName = ''
  params.email = ''
  params.page = 1
}

// Handle pagination
function onPageChange(event: { page: number; rows: number }) {
  params.page = event.page + 1
  params.limit = event.rows
}
</script>
