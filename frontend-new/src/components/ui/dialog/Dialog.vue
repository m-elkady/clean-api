<script setup lang="ts">
import { DialogRoot, useForwardPropsEmits } from 'radix-vue'

interface Props {
  open?: boolean
  defaultOpen?: boolean
  modal?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  modal: true,
})

const emit = defineEmits<{
  'update:open': [value: boolean]
}>()

const forwarded = useForwardPropsEmits(props, emit)
</script>

<template>
  <DialogRoot v-bind="forwarded">
    <slot />
  </DialogRoot>
</template>
