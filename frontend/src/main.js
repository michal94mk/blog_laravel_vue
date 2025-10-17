import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import './assets/main.css'
import { useAuthStore } from './stores/auth'
import { useDarkModeStore } from './stores/darkMode'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Initialize dark mode before mounting
const darkModeStore = useDarkModeStore()

// Initialize auth before mounting
const authStore = useAuthStore()
authStore.initializeAuth().then(() => {
  app.mount('#app')
})
