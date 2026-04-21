// Generic API Response wrapper
export interface ApiResponse<T = unknown> {
  success: boolean
  data: T
  message?: string
}

// API Error response
export interface ApiError {
  errors: string | string[] | Record<string, string[]> | { message?: string }
}

// Paginated response for list endpoints
export interface PaginatedResponse<T> {
  users: T[]
  count: number
  limit: number
  page?: number
}

// Query params for list with pagination
export interface ListParams {
  page?: number
  limit?: number
  sortBy?: string
  order?: 'asc' | 'desc'
  [key: string]: string | number | undefined
}
