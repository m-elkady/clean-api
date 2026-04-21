<template>
  <router-view />
  <Toast />
  <DynamicDialog />
</template>

<script setup lang="ts">
import 'virtual:uno.css'
import Toast from 'primevue/toast'
import DynamicDialog from 'primevue/dynamicdialog'
import { onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'

const { initAuth } = useAuth()

onMounted(() => {
  initAuth()
})
</script>

<style>
/* UnoCSS is imported via 'virtual:uno.css' */
</style>
