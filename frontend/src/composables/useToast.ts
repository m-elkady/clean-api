import { ref } from 'vue'

export interface Toast {
  id: string
  title?: string
  message: string
  variant?: 'default' | 'destructive'
}

const toasts = ref<Toast[]>([])

export function useToast() {
  function toast(options: Omit<Toast, 'id'>) {
    const id = Math.random().toString(36).substring(2, 9)
    toasts.value.push({ id, ...options })
    setTimeout(() => {
      toasts.value = toasts.value.filter((t) => t.id !== id)
    }, 3000)
  }

  function remove(id: string) {
    toasts.value = toasts.value.filter((t) => t.id !== id)
  }

  return {
    toasts,
    toast,
    remove,
  }
}
