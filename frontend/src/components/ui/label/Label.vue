<script setup lang="ts">
import { type HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

export interface LabelProps {
  class?: HTMLAttributes['class']
  for?: string
}

const props = defineProps<LabelProps>()
</script>

<template>
  <label
    :class="cn(
      'text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70',
      props.class
    )"
    v-bind="props"
  >
    <slot />
  </label>
</template>
