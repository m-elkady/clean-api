<template>
  <div>
    <h1 class="text-2xl font-semibold mb-4">Welcome to Clean API</h1>
    <Card>
      <template #content>
        <p class="text-lg">A full-stack application with Symfony backend and Vue 3 frontend.</p>
        <div class="mt-4 space-y-2">
          <p>Features:</p>
          <ul class="list-disc list-inside space-y-1 ml-4">
            <li>JWT Authentication with refresh tokens</li>
            <li>RESTful API with clean architecture</li>
            <li>Vue 3 with TypeScript and PrimeVue</li>
            <li>Tanstack Query for server state management</li>
            <li>User management with CRUD operations</li>
          </ul>
        </div>
      </template>
    </Card>
  </div>
</template>

<script setup lang="ts">
import Card from 'primevue/card'
</script>
