<template>
  <router-view />
  <Toaster />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'
import { Toaster } from '@/components/ui/toast'

const { initAuth } = useAuth()

onMounted(() => {
  initAuth()
})
</script>
