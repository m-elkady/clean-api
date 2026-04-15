<template>
  <v-container class="fill-height">
    <v-responsive class="align-center text-center fill-height">
      <v-card class="mx-auto" max-width="400" flat>
        <v-card-title class="text-h4 text-center">Login</v-card-title>

        <v-card-text>
          <v-form ref="form" @submit.prevent="handleLogin">
            <v-text-field
              v-model="email"
              label="Email"
              type="email"
              variant="outlined"
              :rules="emailRules"
              :disabled="loading"
              class="mb-2"
            />

            <v-text-field
              v-model="password"
              label="Password"
              type="password"
              variant="outlined"
              :rules="passwordRules"
              :disabled="loading"
              class="mb-4"
            />

            <v-btn
              type="submit"
              color="primary"
              size="large"
              block
              :loading="loading"
            >
              Login
            </v-btn>
          </v-form>
        </v-card-text>

        <v-alert
          v-if="error"
          type="error"
          closable
          class="mx-4 mt-4"
          @click:close="error = null"
        >
          {{ error }}
        </v-alert>
      </v-card>
    </v-responsive>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref(null);

const emailRules = [
  (v) => !!v || 'Email is required',
  (v) => /.+@.+\..+/.test(v) || 'Email must be valid',
];

const passwordRules = [
  (v) => !!v || 'Password is required',
  (v) => (v && v.length >= 6) || 'Password must be at least 6 characters',
];

async function handleLogin() {
  error.value = null;

  try {
    loading.value = true;
    await authStore.login(email.value, password.value);
    router.push('/');
  } catch (err) {
    const errors = err.response?.data?.errors;
    if (errors) {
      if (typeof errors === 'string') {
        error.value = errors;
      } else if (errors.message) {
        error.value = errors.message;
      } else {
        error.value = 'Login failed. Please check your credentials.';
      }
    } else {
      error.value = 'Network error. Please try again.';
    }
  } finally {
    loading.value = false;
  }
}
</script>
