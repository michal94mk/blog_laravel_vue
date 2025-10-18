<template>
  <nav class="glass-effect sticky top-0 z-50 shadow-lg bg-white/80 dark:bg-gray-900/80 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <router-link to="/" class="flex-shrink-0 flex items-center group">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">LB</span>
              </div>
              <h1 class="text-lg md:text-xl font-bold gradient-text group-hover:scale-105 transition-transform duration-200">
                <span class="hidden sm:inline">Laravel Blog</span>
                <span class="sm:hidden">LB</span>
              </h1>
            </div>
          </router-link>
          
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <router-link
              to="/"
              class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              active-class="border-indigo-500 text-gray-900 dark:text-white"
            >
              Home
            </router-link>
            
            <router-link
              v-if="isAuthenticated"
              to="/posts/create"
              class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
              active-class="border-indigo-500 text-gray-900 dark:text-white"
            >
              Create Post
            </router-link>
          </div>
        </div>
        
         <!-- Desktop menu -->
         <div class="hidden md:flex items-center">
           <!-- Dark mode toggle -->
           <div class="mr-4">
             <DarkModeToggle />
           </div>
           
           <!-- Loading state -->
           <div v-if="authStore.isLoading" class="flex items-center space-x-4">
             <div class="animate-pulse flex items-center space-x-2">
               <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
               <div class="w-20 h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
             </div>
           </div>
           
           <div v-else-if="isAuthenticated" class="flex items-center space-x-4">
            <router-link to="/profile" class="flex items-center space-x-3 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg p-2 transition-colors">
              <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-sm">
                  {{ user?.name?.charAt(0) || 'U' }}
                </span>
              </div>
              <div class="hidden lg:block">
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ user?.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Profile</p>
              </div>
            </router-link>
            <button
              @click="handleLogout"
              class="btn-danger text-sm"
            >
              Logout
            </button>
          </div>
          
          <div v-else class="flex items-center space-x-4">
            <router-link
              to="/login"
              class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            >
              Login
            </router-link>
            <router-link
              to="/register"
              class="btn-primary text-sm"
            >
              Register
            </router-link>
          </div>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden">
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
          >
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Mobile menu -->
    <div class="md:hidden" :class="{ 'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }">
      <div class="px-2 pt-2 pb-3 space-y-1 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-t border-gray-200 dark:border-gray-700">
        <!-- Navigation links -->
        <router-link
          to="/"
          class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 block px-3 py-2 rounded-md text-base font-medium transition-colors"
          active-class="text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20"
          @click="mobileMenuOpen = false"
        >
          Home
        </router-link>
        
        <router-link
          v-if="isAuthenticated"
          to="/posts/create"
          class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 block px-3 py-2 rounded-md text-base font-medium transition-colors"
          active-class="text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20"
          @click="mobileMenuOpen = false"
        >
          Create Post
        </router-link>
        
         <!-- Loading state for mobile -->
         <div v-if="authStore.isLoading" class="pt-4 border-t border-gray-200 dark:border-gray-700">
           <div class="px-3 py-2">
             <div class="animate-pulse flex items-center space-x-3">
               <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-full"></div>
               <div class="flex-1">
                 <div class="w-24 h-4 bg-gray-200 dark:bg-gray-600 rounded mb-1"></div>
                 <div class="w-32 h-3 bg-gray-200 dark:bg-gray-600 rounded"></div>
               </div>
             </div>
           </div>
         </div>
         
         <!-- Guest menu -->
         <div v-else-if="!isAuthenticated" class="pt-4 border-t border-gray-200 dark:border-gray-700">
          <div class="px-3 space-y-2">
            <router-link
              to="/login"
              class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 block px-3 py-2 rounded-md text-base font-medium transition-colors"
              @click="mobileMenuOpen = false"
            >
              Login
            </router-link>
            <router-link
              to="/register"
              class="btn-primary block text-center"
              @click="mobileMenuOpen = false"
            >
              Register
            </router-link>
          </div>
        </div>
        
        <!-- Authenticated user menu -->
        <div v-else class="pt-4 border-t border-gray-200 dark:border-gray-700">
          <!-- User profile section -->
          <div class="px-3 py-2">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-sm">
                  {{ user?.name?.charAt(0) || 'U' }}
                </span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ user?.name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ user?.email }}</p>
              </div>
            </div>
          </div>
          
          <!-- Profile and logout actions -->
          <div class="px-3 space-y-1">
            <router-link
              to="/profile"
              class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
              @click="mobileMenuOpen = false"
            >
              View Profile
            </router-link>
            <button
              @click="handleLogout"
              class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
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
import DarkModeToggle from './DarkModeToggle.vue'

export default {
  name: 'Navbar',
  components: {
    DarkModeToggle
  },
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
      authStore,
      handleLogout
    }
  }
}
</script>
