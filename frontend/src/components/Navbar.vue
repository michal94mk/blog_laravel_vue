<template>
  <nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <router-link to="/" class="flex-shrink-0 flex items-center">
            <h1 class="text-xl font-bold text-gray-900">Laravel Blog</h1>
          </router-link>
          
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <router-link
              to="/"
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              active-class="border-indigo-500 text-gray-900"
            >
              Home
            </router-link>
            
            <router-link
              v-if="isAuthenticated"
              to="/posts/create"
              class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              active-class="border-indigo-500 text-gray-900"
            >
              Create Post
            </router-link>
          </div>
        </div>
        
        <div class="flex items-center">
          <div v-if="isAuthenticated" class="flex items-center space-x-4">
            <span class="text-sm text-gray-700">
              Welcome, {{ user?.name }}
            </span>
            <button
              @click="handleLogout"
              class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm font-medium"
            >
              Logout
            </button>
          </div>
          
          <div v-else class="flex items-center space-x-4">
            <router-link
              to="/login"
              class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium"
            >
              Login
            </router-link>
            <router-link
              to="/register"
              class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-md text-sm font-medium"
            >
              Register
            </router-link>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Mobile menu -->
    <div class="sm:hidden" :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }">
      <div class="pt-2 pb-3 space-y-1">
        <router-link
          to="/"
          class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          active-class="border-indigo-500 text-indigo-700"
          @click="mobileMenuOpen = false"
        >
          Home
        </router-link>
        
        <router-link
          v-if="isAuthenticated"
          to="/posts/create"
          class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          active-class="border-indigo-500 text-indigo-700"
          @click="mobileMenuOpen = false"
        >
          Create Post
        </router-link>
        
        <div v-if="!isAuthenticated" class="pt-4 pb-3 border-t border-gray-200">
          <div class="flex items-center px-4 space-x-3">
            <router-link
              to="/login"
              class="text-gray-500 hover:text-gray-700 block px-3 py-2 rounded-md text-base font-medium"
              @click="mobileMenuOpen = false"
            >
              Login
            </router-link>
            <router-link
              to="/register"
              class="bg-indigo-600 hover:bg-indigo-700 text-white block px-3 py-2 rounded-md text-base font-medium"
              @click="mobileMenuOpen = false"
            >
              Register
            </router-link>
          </div>
        </div>
        
        <div v-else class="pt-4 pb-3 border-t border-gray-200">
          <div class="flex items-center px-4">
            <div class="flex-shrink-0">
              <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                <span class="text-sm font-medium text-gray-700">
                  {{ user?.name?.charAt(0) }}
                </span>
              </div>
            </div>
            <div class="ml-3">
              <div class="text-base font-medium text-gray-800">{{ user?.name }}</div>
              <div class="text-sm font-medium text-gray-500">{{ user?.email }}</div>
            </div>
          </div>
          <div class="mt-3 px-2 space-y-1">
            <button
              @click="handleLogout"
              class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'Navbar',
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    
    const mobileMenuOpen = ref(false)
    
    const isAuthenticated = computed(() => authStore.isLoggedIn)
    const user = computed(() => authStore.user)
    
    const handleLogout = async () => {
      await authStore.logout()
      router.push('/')
      mobileMenuOpen.value = false
    }
    
    return {
      mobileMenuOpen,
      isAuthenticated,
      user,
      handleLogout
    }
  }
}
</script>
