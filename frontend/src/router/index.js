
/**
 * router/index.ts
 *
 * Automatic routes for `./src/pages/*.vue`
 */

// Composables
import { createRouter, createWebHistory } from 'vue-router'

import Hello from '@/components/Hello.vue'
import UsersList from '@/components/Users/UsersList.vue'

const routes = [
  { path: '/', component: Hello },
  { path: '/users', component: UsersList }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
