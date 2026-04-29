import { defineStore } from 'pinia'
import type { User } from '@/types'
import { setTokens, clearTokens } from '@/services/api'

interface AuthState {
  token: string | null
  refreshToken: string | null
  user: User | null
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    token: localStorage.getItem('auth_token') || null,
    refreshToken: localStorage.getItem('refresh_token') || null,
    user: JSON.parse(localStorage.getItem('user') || 'null'),
  }),

  getters: {
    isAuthenticated: (state): boolean => !!state.token,
    userName: (state): string => {
      if (!state.user) return ''
      return `${state.user.firstName} ${state.user.lastName}`
    },
  },

  actions: {
    setAuth(token: string, refreshToken: string, user: User | null = null) {
      this.token = token
      this.refreshToken = refreshToken
      setTokens(token, refreshToken)

      if (user) {
        this.user = user
        localStorage.setItem('user', JSON.stringify(user))
      }
    },

    setUser(user: User) {
      this.user = user
      localStorage.setItem('user', JSON.stringify(user))
    },

    clearAuth() {
      this.token = null
      this.refreshToken = null
      this.user = null
      clearTokens()
    },
  },
})
