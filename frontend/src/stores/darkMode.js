import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useDarkModeStore = defineStore('darkMode', () => {
  // Get initial value from localStorage or system preference
  const getInitialTheme = () => {
    // Check if we're in browser environment
    if (typeof window === 'undefined') {
      return false
    }
    
    const saved = localStorage.getItem('darkMode')
    if (saved !== null) {
      return saved === 'true'
    }
    
    // Check system preference
    if (window.matchMedia) {
      return window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    
    return false
  }

  const isDark = ref(getInitialTheme())

  // Watch for changes and update localStorage and DOM
  watch(isDark, (newValue) => {
    // Check if we're in browser environment
    if (typeof window === 'undefined' || typeof document === 'undefined') {
      return
    }
    
    localStorage.setItem('darkMode', newValue.toString())
    
    if (newValue) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }, { immediate: true })

  // Toggle dark mode
  const toggle = () => {
    isDark.value = !isDark.value
  }

  // Set specific mode
  const setDark = () => {
    isDark.value = true
  }

  const setLight = () => {
    isDark.value = false
  }

  // Listen for system theme changes
  let mediaQuery = null
  if (typeof window !== 'undefined' && window.matchMedia) {
    mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    const handleSystemThemeChange = (e) => {
      // Only auto-switch if user hasn't manually set a preference
      const saved = localStorage.getItem('darkMode')
      if (saved === null) {
        isDark.value = e.matches
      }
    }

    // Add listener for system theme changes
    mediaQuery.addEventListener('change', handleSystemThemeChange)
  }

  return {
    isDark,
    toggle,
    setDark,
    setLight
  }
})
