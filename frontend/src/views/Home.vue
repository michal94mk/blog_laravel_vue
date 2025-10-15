<template>
  <div class="home">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-4">Welcome to Laravel Blog</h1>
      <p class="text-lg text-gray-600">Discover amazing stories and share your thoughts with the community.</p>
    </div>
    
    <!-- Loading state -->
    <div v-if="loading" class="text-center py-8">
      <div class="spinner mx-auto mb-4"></div>
      <p class="text-gray-600">Loading posts...</p>
    </div>
    
    <!-- Error state -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
      <div class="text-red-700">
        {{ error }}
      </div>
    </div>
    
    <!-- Posts list -->
    <div v-else-if="posts.length > 0" class="space-y-6">
      <div
        v-for="post in posts"
        :key="post.id"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
      >
        <div class="flex justify-between items-start mb-4">
          <h2 class="text-2xl font-bold text-gray-900 hover:text-indigo-600 cursor-pointer">
            <router-link :to="`/posts/${post.id}`">
              {{ post.title }}
            </router-link>
          </h2>
          <span class="text-sm text-gray-500">
            {{ post.created_at_human }}
          </span>
        </div>
        
        <p class="text-gray-700 mb-4 line-clamp-3">
          {{ post.content }}
        </p>
        
        <div class="flex justify-between items-center">
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">By</span>
            <span class="text-sm font-medium text-gray-900">
              {{ post.user?.name || 'Anonymous' }}
            </span>
          </div>
          
          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">
              {{ post.comments_count || 0 }} comments
            </span>
            <router-link
              :to="`/posts/${post.id}`"
              class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
            >
              Read more →
            </router-link>
          </div>
        </div>
      </div>
      
      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="flex justify-center mt-8">
        <nav class="flex space-x-2">
          <button
            v-for="page in pagination.last_page"
            :key="page"
            @click="loadPage(page)"
            :class="[
              'px-3 py-2 text-sm font-medium rounded-md',
              page === pagination.current_page
                ? 'bg-indigo-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
            ]"
          >
            {{ page }}
          </button>
        </nav>
      </div>
    </div>
    
    <!-- Empty state -->
    <div v-else class="text-center py-12">
      <div class="text-gray-400 mb-4">
        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No posts yet</h3>
      <p class="text-gray-600">Be the first to share your story!</p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { usePostsStore } from '@/stores/posts'

export default {
  name: 'Home',
  setup() {
    const postsStore = usePostsStore()
    
    const posts = ref([])
    const loading = ref(false)
    const error = ref('')
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0
    })
    
    const loadPage = async (page = 1) => {
      loading.value = true
      error.value = ''
      
      const result = await postsStore.fetchPosts(page)
      
      if (result.success) {
        posts.value = result.data.data
        pagination.value = {
          current_page: result.data.current_page,
          last_page: result.data.last_page,
          per_page: result.data.per_page,
          total: result.data.total
        }
      } else {
        error.value = result.error
      }
      
      loading.value = false
    }
    
    onMounted(() => {
      loadPage()
    })
    
    return {
      posts,
      loading,
      error,
      pagination,
      loadPage
    }
  }
}
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
