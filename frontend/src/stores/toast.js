import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([])
  let toastId = 0

  const addToast = (toast) => {
    const id = ++toastId
    const newToast = {
      id,
      type: toast.type || 'info', // success, error, warning, info
      title: toast.title || '',
      message: toast.message || '',
      duration: toast.duration || 5000, // 5 seconds default
      ...toast
    }
    
    toasts.value.push(newToast)
    
    // Auto remove after duration
    if (newToast.duration > 0) {
      setTimeout(() => {
        removeToast(id)
      }, newToast.duration)
    }
    
    return id
  }

  const removeToast = (id) => {
    const index = toasts.value.findIndex(toast => toast.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  const clearAll = () => {
    toasts.value = []
  }

  // Convenience methods
  const success = (title, message, options = {}) => {
    return addToast({ type: 'success', title, message, ...options })
  }

  const error = (title, message, options = {}) => {
    return addToast({ type: 'error', title, message, duration: 7000, ...options })
  }

  const warning = (title, message, options = {}) => {
    return addToast({ type: 'warning', title, message, ...options })
  }

  const info = (title, message, options = {}) => {
    return addToast({ type: 'info', title, message, ...options })
  }

  return {
    toasts,
    addToast,
    removeToast,
    clearAll,
    success,
    error,
    warning,
    info
  }
})
