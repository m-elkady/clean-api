import { useRouter } from 'vue-router'
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { authService } from '@/services/auth.service'
import type { LoginInput } from '@/types'

export function useAuth() {
  const router = useRouter()
  const authStore = useAuthStore()

  /**
   * Login with email and password
   */
  async function login(credentials: LoginInput): Promise<void> {
    const response = await authService.login(credentials.email, credentials.password)
    const { access_token, refresh_token } = response.data.data

    authStore.setAuth(access_token, refresh_token)

    // Fetch user info after login
    await fetchUser()
  }

  /**
   * Logout current user
   */
  async function logout(): Promise<void> {
    try {
      await authService.logout()
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      authStore.clearAuth()
      router.push('/login')
    }
  }

  /**
   * Fetch current user info
   */
  async function fetchUser(): Promise<void> {
    const response = await authService.me()
    if (response.data.success && response.data.data) {
      authStore.setUser(response.data.data)
    }
  }

  /**
   * Initialize auth state on app load
   */
  async function initAuth(): Promise<void> {
    if (authStore.token) {
      try {
        await fetchUser()
      } catch (error) {
        // Token might be expired, clear auth
        authStore.clearAuth()
      }
    }
  }

  return {
    // State
    isAuthenticated: computed(() => authStore.isAuthenticated),
    user: computed(() => authStore.user),
    userName: computed(() => authStore.userName),

    // Actions
    login,
    logout,
    fetchUser,
    initAuth,
  }
}
