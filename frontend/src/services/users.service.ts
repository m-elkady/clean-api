import apiClient from './api'
import type { ApiResponse, PaginatedResponse, User, CreateUserInput, UpdateUserInput, UserListParams } from '@/types'

export const usersService = {
  /**
   * Get paginated list of users with optional filters
   */
  getUsers(params?: UserListParams) {
    return apiClient.get<ApiResponse<PaginatedResponse<User>>>('/user', { params })
  },

  /**
   * Get a single user by ID
   */
  getUser(id: number) {
    return apiClient.get<ApiResponse<User>>(`/user/${id}`)
  },

  /**
   * Create a new user
   */
  createUser(data: CreateUserInput) {
    return apiClient.post<ApiResponse<User>>('/user', data)
  },

  /**
   * Update an existing user
   */
  updateUser(id: number, data: UpdateUserInput) {
    return apiClient.put<ApiResponse<User>>(`/user/${id}`, data)
  },

  /**
   * Delete a user
   */
  deleteUser(id: number) {
    return apiClient.delete<ApiResponse<{ success: boolean }>>(`/user/${id}`)
  },
}
