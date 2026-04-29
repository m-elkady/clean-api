<template>
  <div class="flex min-h-screen items-center justify-center bg-background px-4 relative">
    <!-- Theme Toggle -->
    <Button
      @click="toggleTheme"
      variant="ghost"
      size="icon"
      class="absolute top-4 right-4"
      :title="resolvedTheme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
    >
      <Sun v-if="resolvedTheme === 'dark'" class="h-5 w-5" />
      <Moon v-else class="h-5 w-5" />
    </Button>

    <Card class="w-full max-w-md">
      <CardHeader>
        <CardTitle class="text-2xl text-center">Login</CardTitle>
      </CardHeader>

      <CardContent>
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div class="space-y-2">
            <Label for="email">Email</Label>
            <Input
              id="email"
              v-model="email"
              type="email"
              placeholder="Enter your email"
              :disabled="loading"
              autocomplete="email"
            />
            <p v-if="emailError" class="text-sm text-destructive">{{ emailError }}</p>
          </div>

          <div class="space-y-2">
            <Label for="password">Password</Label>
            <div class="relative">
              <Input
                id="password"
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter your password"
                :disabled="loading"
                autocomplete="current-password"
                class="pr-10"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
              >
                <Eye v-if="!showPassword" class="h-4 w-4" />
                <EyeOff v-else class="h-4 w-4" />
              </button>
            </div>
            <p v-if="passwordError" class="text-sm text-destructive">{{ passwordError }}</p>
          </div>

          <Button
            type="submit"
            class="w-full"
            :disabled="loading || !isFormValid"
          >
            <Loader2 v-if="loading" class="mr-2 h-4 w-4 animate-spin" />
            {{ loading ? 'Signing in...' : 'Login' }}
          </Button>

          <div v-if="error" class="rounded-md bg-destructive/15 p-3">
            <p class="text-sm text-destructive">{{ error }}</p>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { Eye, EyeOff, Loader2, Sun, Moon } from 'lucide-vue-next'
import { useAuth } from '@/composables/useAuth'
import { useTheme } from '@/composables/useTheme'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'

const router = useRouter()
const { login } = useAuth()
const { resolvedTheme, toggleTheme } = useTheme()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref<string | null>(null)
const showPassword = ref(false)

const emailError = computed(() => {
  if (!email.value) return 'Email is required'
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) return 'Email must be valid'
  return ''
})

const passwordError = computed(() => {
  if (!password.value) return 'Password is required'
  if (password.value.length < 6) return 'Password must be at least 6 characters'
  return ''
})

const isFormValid = computed(() => !emailError.value && !passwordError.value)

async function handleLogin() {
  if (!isFormValid.value) return

  error.value = null
  loading.value = true

  try {
    await login({ email: email.value, password: password.value })

    const redirect = router.currentRoute.value.query.redirect as string | undefined
    router.push(redirect || '/')
  } catch (err: unknown) {
    const response = (err as { response?: { data?: { errors?: unknown } } })?.response?.data?.errors
    if (typeof response === 'string') {
      error.value = response
    } else if (response && typeof response === 'object' && 'message' in response) {
      error.value = String(response.message)
    } else {
      error.value = 'Login failed. Please check your credentials.'
    }
  } finally {
    loading.value = false
  }
}
</script>
