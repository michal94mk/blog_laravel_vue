<template>
  <div
    aria-live="assertive"
    class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start z-50"
  >
    <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
      <Toast
        v-for="toast in toasts"
        :key="toast.id"
        :toast="toast"
        @close="removeToast(toast.id)"
      />
    </div>
  </div>
</template>

<script>
import { useToastStore } from '@/stores/toast'
import Toast from './Toast.vue'

export default {
  name: 'ToastContainer',
  components: {
    Toast
  },
  setup() {
    const toastStore = useToastStore()
    
    return {
      toasts: toastStore.toasts,
      removeToast: toastStore.removeToast
    }
  }
}
</script>
