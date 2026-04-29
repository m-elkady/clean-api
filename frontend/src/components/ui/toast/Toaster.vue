<script setup lang="ts">
import { useToast } from '@/composables/useToast'
import { X, CheckCircle, AlertCircle } from 'lucide-vue-next'
import { cn } from '@/lib/utils'

const { toasts, remove } = useToast()
</script>

<template>
  <div class="fixed right-4 top-4 z-50 flex flex-col gap-2">
    <div
      v-for="toast in toasts"
      :key="toast.id"
      :class="cn(
        'flex w-96 items-start gap-3 rounded-lg border bg-background p-4 shadow-lg animate-in slide-in-from-right-full',
        toast.variant === 'destructive' && 'border-destructive bg-destructive text-destructive-foreground'
      )"
    >
      <AlertCircle v-if="toast.variant === 'destructive'" class="h-5 w-5 shrink-0 mt-0.5" />
      <CheckCircle v-else class="h-5 w-5 shrink-0 mt-0.5 text-primary" />

      <div class="flex-1 space-y-1">
        <p v-if="toast.title" class="font-medium">{{ toast.title }}</p>
        <p class="text-sm opacity-90">{{ toast.message }}</p>
      </div>

      <button
        @click="remove(toast.id)"
        class="shrink-0 opacity-70 hover:opacity-100"
      >
        <X class="h-4 w-4" />
      </button>
    </div>
  </div>
</template>
