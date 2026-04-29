import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import { computed, toValue, type MaybeRefOrGetter } from 'vue'
import { usersService } from '@/services/users.service'
import type { CreateUserInput, UpdateUserInput, UserListParams } from '@/types'

// Query keys factory
export const userQueryKeys = {
  all: ['users'] as const,
  lists: () => [...userQueryKeys.all, 'list'] as const,
  list: (params: UserListParams) => [...userQueryKeys.lists(), params] as const,
  details: () => [...userQueryKeys.all, 'detail'] as const,
  detail: (id: number) => [...userQueryKeys.details(), id] as const,
}

/**
 * Get paginated list of users
 */
export function useUsers(params: MaybeRefOrGetter<UserListParams> = {}) {
  return useQuery({
    queryKey: userQueryKeys.list(toValue(params)),
    queryFn: () => usersService.getUsers(toValue(params)),
    select: (response) => response.data.data,
  })
}

/**
 * Get a single user by ID
 */
export function useUser(id: MaybeRefOrGetter<number | null>) {
  return useQuery({
    queryKey: userQueryKeys.detail(toValue(id)!),
    queryFn: () => usersService.getUser(toValue(id)!),
    select: (response) => response.data.data,
    enabled: computed(() => !!toValue(id)),
  })
}

/**
 * Create a new user
 */
export function useCreateUser() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (data: CreateUserInput) => usersService.createUser(data),
    onSuccess: () => {
      // Invalidate and refetch users list
      queryClient.invalidateQueries({ queryKey: userQueryKeys.lists() })
    },
  })
}

/**
 * Update an existing user
 */
export function useUpdateUser() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: ({ id, data }: { id: number; data: UpdateUserInput }) =>
      usersService.updateUser(id, data),
    onSuccess: (_, variables) => {
      // Invalidate specific user and users list
      queryClient.invalidateQueries({ queryKey: userQueryKeys.detail(variables.id) })
      queryClient.invalidateQueries({ queryKey: userQueryKeys.lists() })
    },
  })
}

/**
 * Delete a user
 */
export function useDeleteUser() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (id: number) => usersService.deleteUser(id),
    onSuccess: () => {
      // Invalidate users list
      queryClient.invalidateQueries({ queryKey: userQueryKeys.lists() })
    },
  })
}
