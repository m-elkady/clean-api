<script setup lang="ts">
import { type HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

export interface ToastProps {
  class?: HTMLAttributes['class']
  variant?: 'default' | 'destructive'
}

const props = withDefaults(defineProps<ToastProps>(), {
  variant: 'default',
})
</script>

<template>
  <div
    :class="cn(
      'fixed right-4 top-4 z-50 w-96 rounded-lg border bg-background p-4 shadow-lg transition-all',
      props.variant === 'destructive' && 'border-destructive bg-destructive text-destructive-foreground',
      props.class
    )"
  >
    <slot />
  </div>
</template>
