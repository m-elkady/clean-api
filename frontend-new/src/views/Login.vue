<template>
  <div class="flex-center min-h-screen bg-surface">
    <Card class="w-full max-w-md p-4">
      <template #title>
        <h1 class="text-2xl font-semibold text-center">Login</h1>
      </template>

      <template #content>
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium mb-2">Email</label>
            <InputText
              id="email"
              v-model="email"
              type="email"
              placeholder="Enter your email"
              :invalid="!!emailError"
              class="w-full"
              :disabled="loading"
              autocomplete="email"
            />
            <small v-if="emailError" class="text-red-500">{{ emailError }}</small>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium mb-2">Password</label>
            <Password
              id="password"
              v-model="password"
              placeholder="Enter your password"
              :feedback="false"
              toggle-mask
              :invalid="!!passwordError"
              class="w-full"
              :disabled="loading"
              autocomplete="current-password"
            />
            <small v-if="passwordError" class="text-red-500">{{ passwordError }}</small>
          </div>

          <Button
            type="submit"
            label="Login"
            class="w-full"
            :loading="loading"
            :disabled="!isFormValid"
          />

          <Message v-if="error" severity="error" :closable="false" @close="error = null">
            {{ error }}
          </Message>
        </form>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import Card from 'primevue/card'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Message from 'primevue/message'

const router = useRouter()
const { login } = useAuth()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref<string | null>(null)

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

    // Redirect to the page user was trying to access, or home
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
