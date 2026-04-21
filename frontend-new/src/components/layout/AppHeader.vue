<template>
  <header class="bg-primary-600 text-white shadow-md">
    <div class="container mx-auto px-4 py-3 flex-between">
      <div class="flex items-center gap-4">
        <h1 class="text-xl font-semibold">Clean API</h1>
        <nav class="flex gap-2">
          <router-link to="/" class="px-3 py-1 rounded hover:bg-primary-700 transition-colors">
            Home
          </router-link>
          <router-link to="/users" class="px-3 py-1 rounded hover:bg-primary-700 transition-colors">
            Users
          </router-link>
        </nav>
      </div>

      <div v-if="userName" class="flex items-center gap-4">
        <span class="text-sm">{{ userName }}</span>
        <button
          @click="handleLogout"
          class="px-3 py-1 bg-primary-700 hover:bg-primary-800 rounded transition-colors"
        >
          Logout
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const { userName, logout } = useAuth()

async function handleLogout() {
  await logout()
  router.push('/login')
}
</script>
