<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue'
import { cn } from '@/lib/utils'

export interface InputProps {
  class?: HTMLAttributes['class']
  modelValue?: string | number
  type?: string
  placeholder?: string
  disabled?: boolean
  id?: string
  name?: string
  autocomplete?: string
}

const props = withDefaults(defineProps<InputProps>(), {
  type: 'text',
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const delegatedProps = computed(() => {
  const { class: _, modelValue: __, ...delegated } = props
  return delegated
})

const value = computed({
  get: () => String(props.modelValue ?? ''),
  set: (value) => emit('update:modelValue', value),
})
</script>

<template>
  <input
    :class="cn(
      'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
      props.class
    )"
    v-bind="delegatedProps"
    v-model="value"
  />
</template>
