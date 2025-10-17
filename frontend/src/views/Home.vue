<template>
  <div class="home">
    <div class="mb-12 text-center">
      <h1 class="text-5xl font-bold gradient-text mb-6">Welcome to Laravel Blog</h1>
      <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Discover amazing stories, share your thoughts, and connect with a vibrant community of writers and readers.</p>
    </div>
    
    <!-- Loading state -->
    <div v-if="loading" class="text-center py-8">
      <div class="spinner mx-auto mb-4"></div>
      <p class="text-gray-600 dark:text-gray-400">Loading posts...</p>
    </div>
    
    <!-- Error state -->
    <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4 mb-6">
      <div class="text-red-700 dark:text-red-400">
        {{ error }}
      </div>
    </div>
    
    <!-- Posts list -->
    <div v-else-if="posts.length > 0">
      <!-- Posts grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
        <div
          v-for="post in posts"
          :key="post.id"
          class="card card-hover p-6 h-full flex flex-col"
        >
          <!-- Post header -->
          <div class="mb-4">
            <div class="flex items-center space-x-3 mb-3">
              <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                <span class="text-white font-bold text-sm">
                  {{ (post.user?.name || 'A').charAt(0).toUpperCase() }}
                </span>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ post.user?.name || 'Anonymous' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ post.created_at_human }}</p>
              </div>
            </div>
            
            <h2 class="text-xl font-bold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 cursor-pointer mb-3 line-clamp-2">
              <router-link :to="`/posts/${post.id}`">
                {{ post.title }}
              </router-link>
            </h2>
          </div>
          
          <!-- Post content -->
          <div class="flex-grow mb-4">
            <p class="text-gray-700 dark:text-gray-300 line-clamp-4 leading-relaxed">
              {{ post.content }}
            </p>
          </div>
          
          <!-- Post footer -->
          <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              <span>{{ post.comments_count || 0 }}</span>
            </div>
            
            <router-link
              :to="`/posts/${post.id}`"
              class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium flex items-center space-x-1 group"
            >
              <span>Read more</span>
              <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </router-link>
          </div>
        </div>
      </div>
      
      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="flex flex-col items-center space-y-4 mt-8">
        <!-- Posts info -->
        <div class="text-sm text-gray-600 dark:text-gray-400">
          Showing {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }} to 
          {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} of 
          {{ pagination.total }} posts
        </div>
        
        <!-- Pagination controls -->
        <nav class="flex items-center space-x-2">
          <!-- Previous button -->
          <button
            @click="loadPage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          
          <!-- Page numbers -->
          <template v-for="page in getVisiblePages()" :key="page">
            <button
              v-if="page !== '...'"
              @click="loadPage(page)"
              :class="[
                'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                page === pagination.current_page
                  ? 'bg-indigo-600 text-white'
                  : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600'
              ]"
            >
              {{ page }}
            </button>
            <span v-else class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">...</span>
          </template>
          
          <!-- Next button -->
          <button
            @click="loadPage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
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
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No posts yet</h3>
      <p class="text-gray-600 dark:text-gray-400">Be the first to share your story!</p>
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
      per_page: 9,
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
    
    const getVisiblePages = () => {
      const current = pagination.value.current_page
      const last = pagination.value.last_page
      const pages = []
      
      if (last <= 7) {
        // Show all pages if 7 or fewer
        for (let i = 1; i <= last; i++) {
          pages.push(i)
        }
      } else {
        // Always show first page
        pages.push(1)
        
        if (current <= 4) {
          // Show first 5 pages, then ellipsis, then last page
          for (let i = 2; i <= 5; i++) {
            pages.push(i)
          }
          pages.push('...')
          pages.push(last)
        } else if (current >= last - 3) {
          // Show first page, ellipsis, then last 5 pages
          pages.push('...')
          for (let i = last - 4; i <= last; i++) {
            pages.push(i)
          }
        } else {
          // Show first page, ellipsis, current-1, current, current+1, ellipsis, last page
          pages.push('...')
          for (let i = current - 1; i <= current + 1; i++) {
            pages.push(i)
          }
          pages.push('...')
          pages.push(last)
        }
      }
      
      return pages
    }
    
    onMounted(() => {
      loadPage()
    })
    
    return {
      posts,
      loading,
      error,
      pagination,
      loadPage,
      getVisiblePages
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

.line-clamp-4 {
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
