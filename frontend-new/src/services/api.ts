import type { AxiosInstance, AxiosError, InternalAxiosRequestConfig } from 'axios'
import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_URL || '/api'

// Type for queued request callback
type RequestCallback = (token: string) => void

// API client instance
export const apiClient: AxiosInstance = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
})

// Token refresh state
let isRefreshing = false
let refreshSubscribers: RequestCallback[] = []

function subscribeTokenRefresh(callback: RequestCallback): void {
  refreshSubscribers.push(callback)
}

function onRefreshed(token: string): void {
  refreshSubscribers.forEach((callback) => callback(token))
  refreshSubscribers = []
}

// Get tokens from localStorage
function getAccessToken(): string | null {
  return localStorage.getItem('auth_token')
}

function getRefreshToken(): string | null {
  return localStorage.getItem('refresh_token')
}

// Set tokens in localStorage
export function setTokens(accessToken: string, refreshToken: string): void {
  localStorage.setItem('auth_token', accessToken)
  localStorage.setItem('refresh_token', refreshToken)
}

export function clearTokens(): void {
  localStorage.removeItem('auth_token')
  localStorage.removeItem('refresh_token')
  localStorage.removeItem('user')
}

// Request interceptor - add auth token
apiClient.interceptors.request.use(
  (config) => {
    const token = getAccessToken()
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

// Extended axios error config type
interface AxiosRequestConfigWithRetry extends InternalAxiosRequestConfig {
  _retry?: boolean
}

// Response interceptor - handle 401 errors and refresh tokens
apiClient.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    const originalRequest = error.config as AxiosRequestConfigWithRetry

    // If 401 and not already retrying
    if (error.response?.status === 401 && originalRequest && !originalRequest._retry) {
      const refreshToken = getRefreshToken()

      if (refreshToken && !isRefreshing) {
        isRefreshing = true
        originalRequest._retry = true

        try {
          const response = await axios.post(`${API_BASE_URL}/auth/refresh`, {
            refresh_token: refreshToken,
          })

          const { access_token, refresh_token: newRefreshToken } = response.data.data as {
            access_token: string
            refresh_token: string
          }

          setTokens(access_token, newRefreshToken)

          onRefreshed(access_token)
          isRefreshing = false

          // Retry original request with new token
          originalRequest.headers.Authorization = `Bearer ${access_token}`
          return apiClient(originalRequest)
        } catch (refreshError) {
          // Refresh failed, clear tokens and redirect to login
          clearTokens()
          window.location.href = '/login'
          return Promise.reject(refreshError)
        }
      } else if (refreshToken && isRefreshing) {
        // Wait for token refresh to complete
        return new Promise((resolve) => {
          subscribeTokenRefresh((token) => {
            originalRequest.headers.Authorization = `Bearer ${token}`
            resolve(apiClient(originalRequest))
          })
        })
      } else {
        // No refresh token, logout and redirect to login
        clearTokens()
        window.location.href = '/login'
      }
    }

    return Promise.reject(error)
  }
)

export default apiClient
