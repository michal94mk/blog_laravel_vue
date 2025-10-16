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
          <p class="text-sm text-gray-500">Member since {{ formatDate(user?.created_at) }}</p>
        </div>
      </div>
      
      <!-- User stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="text-center p-4 bg-gray-50 rounded-lg">
          <div class="text-2xl font-bold text-indigo-600">{{ userStats.postsCount }}</div>
          <div class="text-sm text-gray-600">Posts</div>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-lg">
          <div class="text-2xl font-bold text-purple-600">{{ userStats.commentsCount }}</div>
          <div class="text-sm text-gray-600">Comments</div>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-lg">
          <div class="text-2xl font-bold text-green-600">{{ userStats.totalLikes }}</div>
          <div class="text-sm text-gray-600">Total Likes</div>
        </div>
      </div>
      
      <!-- Recent activity -->
      <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
        <div v-if="recentPosts.length > 0" class="space-y-4">
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
import { ref, computed, onMounted } from 'vue'
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
      totalLikes: 0
    })
    
    const formatDate = (dateString) => {
      if (!dateString) return 'Unknown'
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
    
    const loadUserData = async () => {
      // Load user's recent posts
      if (user.value?.id) {
        // This would be a real API call to get user's posts
        // For now, we'll simulate with existing posts
        const result = await postsStore.fetchPosts(1)
        if (result.success) {
          recentPosts.value = result.data.data.slice(0, 3) // Show only 3 recent posts
        }
        
        // Simulate user stats (in real app, this would come from API)
        userStats.value = {
          postsCount: Math.floor(Math.random() * 20) + 1,
          commentsCount: Math.floor(Math.random() * 50) + 5,
          totalLikes: Math.floor(Math.random() * 100) + 10
        }
      }
    }
    
    onMounted(() => {
      loadUserData()
    })
    
    return {
      user,
      recentPosts,
      userStats,
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
