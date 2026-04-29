<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold tracking-tight">Users</h1>
      <Button @click="openCreateDialog">
        <Plus class="mr-2 h-4 w-4" />
        New User
      </Button>
    </div>

    <Card>
      <CardContent class="pt-6">
        <!-- Search Filters -->
        <div class="grid gap-4 md:grid-cols-3 mb-4">
          <Input v-model="filters.firstName" placeholder="Search by first name" />
          <Input v-model="filters.lastName" placeholder="Search by last name" />
          <Input v-model="filters.email" placeholder="Search by email" />
        </div>

        <div class="flex gap-2 mb-4">
          <Button @click="applyFilters" variant="secondary">
            <Search class="mr-2 h-4 w-4" />
            Search
          </Button>
          <Button @click="clearFilters" variant="outline">
            <X class="mr-2 h-4 w-4" />
            Clear
          </Button>
        </div>

        <!-- Users Table -->
        <div class="rounded-md border">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead @click="sortBy('firstName')" class="cursor-pointer hover:text-accent-foreground">
                  First Name <ArrowUpDown v-if="params.sortBy === 'firstName'" class="ml-1 h-3 w-3 inline" />
                </TableHead>
                <TableHead @click="sortBy('lastName')" class="cursor-pointer hover:text-accent-foreground">
                  Last Name <ArrowUpDown v-if="params.sortBy === 'lastName'" class="ml-1 h-3 w-3 inline" />
                </TableHead>
                <TableHead @click="sortBy('email')" class="cursor-pointer hover:text-accent-foreground">
                  Email <ArrowUpDown v-if="params.sortBy === 'email'" class="ml-1 h-3 w-3 inline" />
                </TableHead>
                <TableHead class="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="isLoading">
                <TableCell :colspan="4" class="text-center py-8">
                  <Loader2 class="h-6 w-6 animate-spin mx-auto" />
                  <p class="text-sm text-muted-foreground mt-2">Loading users...</p>
                </TableCell>
              </TableRow>
              <TableRow v-else-if="users.length === 0">
                <TableCell :colspan="4" class="text-center py-8 text-muted-foreground">
                  No users found
                </TableCell>
              </TableRow>
              <TableRow v-else v-for="user in users" :key="user.id">
                <TableCell>{{ user.firstName }}</TableCell>
                <TableCell>{{ user.lastName }}</TableCell>
                <TableCell>{{ user.email }}</TableCell>
                <TableCell class="text-right">
                  <Button
                    variant="ghost"
                    size="icon"
                    @click="openEditDialog(user)"
                  >
                    <Pencil class="h-4 w-4" />
                  </Button>
                  <Button
                    variant="ghost"
                    size="icon"
                    class="text-destructive hover:text-destructive"
                    @click="confirmDelete(user)"
                  >
                    <Trash2 class="h-4 w-4" />
                  </Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <!-- Pagination -->
        <div v-if="totalRecords > 0" class="flex items-center justify-between mt-4">
          <p class="text-sm text-muted-foreground">
            Showing {{ ((params.page ?? 1) - 1) * (params.perPage ?? 10) + 1 }} to {{ Math.min((params.page ?? 1) * (params.perPage ?? 10), totalRecords) }} of {{ totalRecords }} users
          </p>
          <div class="flex items-center gap-2">
            <Button
              variant="outline"
              size="sm"
              :disabled="(params.page ?? 1) === 1"
              @click="prevPage"
            >
              Previous
            </Button>
            <span class="text-sm">Page {{ params.page ?? 1 }} of {{ totalPages }}</span>
            <Button
              variant="outline"
              size="sm"
              :disabled="(params.page ?? 1) >= totalPages"
              @click="nextPage"
            >
              Next
            </Button>
            <select
              v-model="params.perPage"
              class="ml-4 rounded-md border border-input bg-background px-3 py-1 text-sm"
            >
              <option :value="10">10 per page</option>
              <option :value="20">20 per page</option>
              <option :value="50">50 per page</option>
            </select>
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Create/Edit Dialog -->
    <Dialog v-model:open="dialogVisible">
      <DialogContent class="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>{{ dialogMode === 'create' ? 'Create New User' : 'Edit User' }}</DialogTitle>
        </DialogHeader>

        <form @submit.prevent="saveUser" class="space-y-4">
          <div class="space-y-2">
            <Label for="firstName">First Name</Label>
            <Input
              id="firstName"
              v-model="formData.firstName"
              :class="{ 'border-destructive': formErrors.firstName }"
            />
            <p v-if="formErrors.firstName" class="text-sm text-destructive">{{ formErrors.firstName }}</p>
          </div>

          <div class="space-y-2">
            <Label for="lastName">Last Name</Label>
            <Input
              id="lastName"
              v-model="formData.lastName"
              :class="{ 'border-destructive': formErrors.lastName }"
            />
            <p v-if="formErrors.lastName" class="text-sm text-destructive">{{ formErrors.lastName }}</p>
          </div>

          <div class="space-y-2">
            <Label for="email">Email</Label>
            <Input
              id="email"
              v-model="formData.email"
              type="email"
              :class="{ 'border-destructive': formErrors.email }"
            />
            <p v-if="formErrors.email" class="text-sm text-destructive">{{ formErrors.email }}</p>
          </div>

          <div v-if="dialogMode === 'create'" class="space-y-2">
            <Label for="password">Password</Label>
            <Input
              id="password"
              v-model="formData.password"
              type="password"
              :class="{ 'border-destructive': formErrors.password }"
            />
            <p v-if="formErrors.password" class="text-sm text-destructive">{{ formErrors.password }}</p>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="dialogVisible = false">Cancel</Button>
            <Button type="submit" :disabled="isSaving">
              <Loader2 v-if="isSaving" class="mr-2 h-4 w-4 animate-spin" />
              {{ isSaving ? 'Saving...' : 'Save' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Delete Confirmation Dialog -->
    <Dialog v-model:open="deleteDialogVisible">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Delete User</DialogTitle>
          <DialogDescription>
            Are you sure you want to delete <span class="font-semibold">{{ userToDelete?.firstName }} {{ userToDelete?.lastName }}</span>? This action cannot be undone.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" @click="deleteDialogVisible = false">Cancel</Button>
          <Button variant="destructive" @click="deleteUser" :disabled="isDeleting">
            <Loader2 v-if="isDeleting" class="mr-2 h-4 w-4 animate-spin" />
            Delete
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive, watch } from 'vue'
import {
  Plus,
  Search,
  X,
  Pencil,
  Trash2,
  Loader2,
  ArrowUpDown,
} from 'lucide-vue-next'
import { useToast } from '@/composables/useToast'
import { useUsers, useCreateUser, useUpdateUser, useDeleteUser } from '@/composables/useUsers'
import type { User, UserListParams } from '@/types'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent } from '@/components/ui/card'
import {
  Table,
  TableHeader,
  TableBody,
  TableHead,
  TableRow,
  TableCell,
} from '@/components/ui/table'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
  DialogFooter,
} from '@/components/ui/dialog'

const { toast } = useToast()

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
const totalPages = computed(() => Math.ceil(totalRecords.value / (params.perPage ?? 10)))

// Dialog state
const dialogVisible = ref(false)
const deleteDialogVisible = ref(false)
const dialogMode = ref<'create' | 'edit'>('create')
const isSaving = ref(false)
const isDeleting = ref(false)
const userToDelete = ref<User | null>(null)

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
      toast({ title: 'Success', message: 'User created successfully' })
    } else {
      await updateUserMutation.mutateAsync({
        id: formData.id,
        data: {
          firstName: formData.firstName,
          lastName: formData.lastName,
          email: formData.email,
        },
      })
      toast({ title: 'Success', message: 'User updated successfully' })
    }
    dialogVisible.value = false
  } catch (error: unknown) {
    const message =
      (error as { response?: { data?: { errors?: string | { message?: string } } } })?.response?.data
        ?.errors ?? 'An error occurred'
    const errorMessage = typeof message === 'string' ? message : 'An error occurred'
    toast({ title: 'Error', message: errorMessage, variant: 'destructive' })
  } finally {
    isSaving.value = false
  }
}

// Confirm delete
function confirmDelete(user: User) {
  userToDelete.value = user
  deleteDialogVisible.value = true
}

// Delete user
async function deleteUser() {
  if (!userToDelete.value) return

  isDeleting.value = true

  try {
    await deleteUserMutation.mutateAsync(userToDelete.value.id)
    toast({ title: 'Success', message: 'User deleted successfully' })
    deleteDialogVisible.value = false
    userToDelete.value = null
  } catch (error) {
    toast({ title: 'Error', message: 'Failed to delete user', variant: 'destructive' })
  } finally {
    isDeleting.value = false
  }
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

// Sort by column
function sortBy(column: string) {
  if (params.sortBy === column) {
    params.order = params.order === 'asc' ? 'desc' : 'asc'
  } else {
    params.sortBy = column
    params.order = 'asc'
  }
}

// Pagination
function prevPage() {
  if (params.page && params.page > 1) {
    params.page--
  }
}

function nextPage() {
  if (params.page && params.page < totalPages.value) {
    params.page++
  }
}

// Watch perPage changes to reset to page 1
watch(() => params.perPage, () => {
  if (params.page) params.page = 1
})
</script>
