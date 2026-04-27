<template>
  <header class="border-b bg-background shadow-sm">
    <div class="container mx-auto px-4 py-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-6">
          <h1 class="text-xl font-bold text-primary">Clean API</h1>
          <nav class="flex gap-1">
            <router-link
              to="/"
              class="px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
              active-class="bg-accent text-accent-foreground"
            >
              Home
            </router-link>
            <router-link
              to="/users"
              class="px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
              active-class="bg-accent text-accent-foreground"
            >
              Users
            </router-link>
          </nav>
        </div>

        <div v-if="userName" class="flex items-center gap-3">
          <Button
            @click="toggleTheme"
            variant="ghost"
            size="icon"
            :title="resolvedTheme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
          >
            <Sun v-if="resolvedTheme === 'dark'" class="h-5 w-5" />
            <Moon v-else class="h-5 w-5" />
          </Button>

          <span class="text-sm text-muted-foreground hidden sm:inline">{{ userName }}</span>

          <Button
            @click="handleLogout"
            variant="outline"
            size="sm"
          >
            <LogOut class="mr-2 h-4 w-4" />
            <span class="hidden sm:inline">Logout</span>
          </Button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { Sun, Moon, LogOut } from 'lucide-vue-next'
import { useAuth } from '@/composables/useAuth'
import { useTheme } from '@/composables/useTheme'
import { Button } from '@/components/ui/button'

const router = useRouter()
const { userName, logout } = useAuth()
const { resolvedTheme, toggleTheme } = useTheme()

async function handleLogout() {
  await logout()
  router.push('/login')
}
</script>
