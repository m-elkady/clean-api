// User entity
export interface User {
  id: number
  firstName: string
  lastName: string
  email: string
}

// Form data for creating a user
export interface CreateUserInput {
  firstName: string
  lastName: string
  email: string
  password: string
}

// Form data for updating a user
export interface UpdateUserInput {
  firstName?: string
  lastName?: string
  email?: string
  password?: string
}

// User search/filter params
export interface UserListParams {
  page?: number
  perPage?: number
  sortBy?: string
  order?: 'asc' | 'desc'
  firstName?: string
  lastName?: string
  email?: string
}
