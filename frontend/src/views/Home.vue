<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-3xl font-bold tracking-tight">Welcome to Clean API</h1>
      <p class="text-muted-foreground mt-2">
        A full-stack application with Symfony backend and Vue 3 frontend.
      </p>
    </div>

    <Card>
      <CardHeader>
        <CardTitle>Features</CardTitle>
      </CardHeader>
      <CardContent>
        <ul class="space-y-2">
          <li class="flex items-start gap-2">
            <Check class="h-5 w-5 text-primary shrink-0 mt-0.5" />
            <span>JWT Authentication with refresh tokens</span>
          </li>
          <li class="flex items-start gap-2">
            <Check class="h-5 w-5 text-primary shrink-0 mt-0.5" />
            <span>RESTful API with clean architecture</span>
          </li>
          <li class="flex items-start gap-2">
            <Check class="h-5 w-5 text-primary shrink-0 mt-0.5" />
            <span>Vue 3 with TypeScript and shadcn-vue</span>
          </li>
          <li class="flex items-start gap-2">
            <Check class="h-5 w-5 text-primary shrink-0 mt-0.5" />
            <span>Tanstack Query for server state management</span>
          </li>
          <li class="flex items-start gap-2">
            <Check class="h-5 w-5 text-primary shrink-0 mt-0.5" />
            <span>User management with CRUD operations</span>
          </li>
        </ul>
      </CardContent>
    </Card>

    <div class="grid gap-4 md:grid-cols-2">
      <Card class="hover:bg-accent/50 transition-colors cursor-pointer" @click="$router.push('/users')">
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Users class="h-5 w-5" />
            Manage Users
          </CardTitle>
          <CardDescription>
            View, create, edit, and delete users in the system.
          </CardDescription>
        </CardHeader>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Settings class="h-5 w-5" />
            Settings
          </CardTitle>
          <CardDescription>
            Configure your application settings (coming soon).
          </CardDescription>
        </CardHeader>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Check, Users, Settings } from 'lucide-vue-next'
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card'
</script>
