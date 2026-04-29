import apiClient from './api'
import type { ApiResponse, AuthResponse, User } from '@/types'

export const authService = {
  /**
   * Login with email and password
   */
  login(email: string, password: string) {
    return apiClient.post<ApiResponse<AuthResponse>>('/auth/login', { email, password })
  },

  /**
   * Refresh access token
   */
  refreshToken(refreshToken: string) {
    return apiClient.post<ApiResponse<AuthResponse>>('/auth/refresh', { refresh_token: refreshToken })
  },

  /**
   * Get current user info
   */
  me() {
    return apiClient.get<ApiResponse<User>>('/auth/me')
  },

  /**
   * Logout
   */
  logout() {
    return apiClient.post('/auth/logout')
  },
}
