<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div class="text-center">
        <div class="mx-auto h-12 w-12 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-full flex items-center justify-center mb-4">
          <span class="text-white font-bold text-lg">LB</span>
        </div>
        <h2 class="text-3xl font-bold gradient-text">
          Join our community
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Already have an account?
          <router-link to="/login" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
            Sign in here
          </router-link>
        </p>
      </div>
      
      <div class="card p-8">
        <form class="space-y-6" @submit.prevent="handleRegister">
          <div class="space-y-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
              <input
                id="name"
                v-model="form.name"
                name="name"
                type="text"
                autocomplete="name"
                required
                class="input-field"
                placeholder="Enter your full name"
              />
            </div>
            
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
              <input
                id="email"
                v-model="form.email"
                name="email"
                type="email"
                autocomplete="email"
                required
                class="input-field"
                placeholder="Enter your email address"
              />
            </div>
            
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
              <input
                id="password"
                v-model="form.password"
                name="password"
                type="password"
                autocomplete="new-password"
                required
                class="input-field"
                placeholder="Enter your password"
              />
            </div>
            
            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                required
                class="input-field"
                placeholder="Confirm your password"
              />
            </div>
          </div>

        <div v-if="error" class="rounded-md bg-red-50 p-4">
          <div class="text-sm text-red-700">
            {{ error }}
          </div>
        </div>

          <div class="flex space-x-3">
            <router-link
              to="/"
              class="btn-secondary flex-1 text-center"
            >
              ← Back
            </router-link>
            <button
              type="submit"
              :disabled="loading"
              class="btn-primary flex-1 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="spinner mr-2"></span>
              {{ loading ? 'Creating account...' : 'Create account' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'Register',
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    
    const form = ref({
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    })
    
    const loading = ref(false)
    const error = ref('')

    const handleRegister = async () => {
      loading.value = true
      error.value = ''
      
      const result = await authStore.register(form.value)
      
      if (result.success) {
        router.push('/')
      } else {
        error.value = result.error
      }
      
      loading.value = false
    }

    return {
      form,
      loading,
      error,
      handleRegister
    }
  }
}
</script>
