// Login request payload
export interface LoginInput {
  email: string
  password: string
}

// Auth response containing tokens
export interface AuthResponse {
  access_token: string
  refresh_token: string
}

// Combined auth data response
export interface AuthData extends AuthResponse {
  user?: User
}

// User type (re-export for convenience)
import type { User } from './user'
export type { User }

// Stored auth state
export interface StoredAuth {
  token: string | null
  refreshToken: string | null
  user: User | null
}
