<template>
  <div class="max-w-4xl mx-auto">
    <div class="mb-8">
      <h1 class="text-3xl font-bold gradient-text mb-2">My Profile</h1>
      <p class="text-gray-600">Manage your account information and preferences</p>
    </div>
    
    <!-- Profile card -->
    <div class="card p-8">
      <div class="flex items-center space-x-6 mb-8">
        <div class="w-20 h-20 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
          <span class="text-white font-bold text-2xl">
            {{ user?.name?.charAt(0) || 'U' }}
          </span>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-gray-900">{{ user?.name }}</h2>
          <p class="text-gray-600">{{ user?.email }}</p>
          <div class="flex items-center gap-2 mt-1">
            <span 
              v-if="user?.is_admin" 
              class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white"
            >
              Administrator
            </span>
            <span 
              v-else 
              class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-200 text-gray-700"
            >
              User
            </span>
            <span class="text-sm text-gray-500">• Member since {{ formatDate(user?.created_at) }}</span>
          </div>
        </div>
      </div>
      
      <!-- User stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="text-center p-4 bg-gray-50 rounded-lg">
          <div v-if="isLoading" class="animate-pulse">
            <div class="h-8 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-16 mx-auto"></div>
          </div>
          <div v-else>
            <div class="text-2xl font-bold text-indigo-600">{{ userStats.postsCount }}</div>
            <div class="text-sm text-gray-600">Posts</div>
          </div>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-lg">
          <div v-if="isLoading" class="animate-pulse">
            <div class="h-8 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-20 mx-auto"></div>
          </div>
          <div v-else>
            <div class="text-2xl font-bold text-purple-600">{{ userStats.commentsCount }}</div>
            <div class="text-sm text-gray-600">Comments</div>
          </div>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-lg">
          <div v-if="isLoading" class="animate-pulse">
            <div class="h-8 bg-gray-200 rounded mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-24 mx-auto"></div>
          </div>
          <div v-else>
            <div class="text-2xl font-bold text-green-600">{{ userStats.totalCommentsReceived }}</div>
            <div class="text-sm text-gray-600">Comments Received</div>
          </div>
        </div>
      </div>
      
      <!-- Recent activity -->
      <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
        <div v-if="isLoading" class="space-y-4">
          <div v-for="n in 3" :key="n" class="p-4 border border-gray-200 rounded-lg">
            <div class="animate-pulse">
              <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
              <div class="h-3 bg-gray-200 rounded w-full mb-1"></div>
              <div class="h-3 bg-gray-200 rounded w-2/3"></div>
            </div>
          </div>
        </div>
        <div v-else-if="recentPosts.length > 0" class="space-y-4">
          <div
            v-for="post in recentPosts"
            :key="post.id"
            class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition-colors"
          >
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <h4 class="font-medium text-gray-900 mb-1">
                  <router-link :to="`/posts/${post.id}`" class="hover:text-indigo-600">
                    {{ post.title }}
                  </router-link>
                </h4>
                <p class="text-sm text-gray-600 line-clamp-2">{{ post.content }}</p>
              </div>
              <span class="text-xs text-gray-500 ml-4">{{ post.created_at_human }}</span>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8 text-gray-500">
          <p>No recent activity</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { usePostsStore } from '@/stores/posts'

export default {
  name: 'Profile',
  setup() {
    const authStore = useAuthStore()
    const postsStore = usePostsStore()
    
    const user = computed(() => authStore.user)
    const recentPosts = ref([])
    const userStats = ref({
      postsCount: 0,
      commentsCount: 0,
      totalCommentsReceived: 0
    })
    const isLoading = ref(false)
    
    const formatDate = (dateString) => {
      if (!dateString) return 'Unknown'
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
    
    const loadUserData = async () => {
      if (user.value?.id) {
        isLoading.value = true
        try {
          // Fetch real user statistics from API
          const statsResult = await authStore.fetchUserStats()
          if (statsResult.success) {
            updateStats(statsResult.data)
          } else {
            console.error('Failed to load user stats:', statsResult.error)
            // Fallback to empty stats
            updateStats({
              posts_count: 0,
              comments_count: 0,
              total_comments_received: 0,
              recent_posts: []
            })
          }
        } finally {
          isLoading.value = false
        }
      }
    }

    const updateStats = (stats) => {
      userStats.value = {
        postsCount: stats.posts_count,
        commentsCount: stats.comments_count,
        totalCommentsReceived: stats.total_comments_received
      }
      recentPosts.value = stats.recent_posts.slice(0, 3) // Show only 3 recent posts
    }
    
    onMounted(() => {
      loadUserData()

      // Listen for user stats updates
      const handleStatsUpdate = (event) => {
        updateStats(event.detail)
      }

      window.addEventListener('userStatsUpdated', handleStatsUpdate)

      // Cleanup event listener
      onUnmounted(() => {
        window.removeEventListener('userStatsUpdated', handleStatsUpdate)
      })
    })
    
    return {
      user,
      recentPosts,
      userStats,
      isLoading,
      formatDate
    }
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
