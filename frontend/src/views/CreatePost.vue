<template>
  <div class="max-w-4xl mx-auto">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create New Post</h1>
      <p class="text-gray-600 dark:text-gray-400 mt-2">Share your thoughts with the community</p>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Post Title
          </label>
          <input
            id="title"
            v-model="form.title"
            type="text"
            required
            class="input-field"
            placeholder="Enter a compelling title for your post"
          />
        </div>
        
        <div>
          <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Post Content
          </label>
          <textarea
            id="content"
            v-model="form.content"
            rows="12"
            required
            class="textarea-field"
            placeholder="Write your post content here..."
          ></textarea>
        </div>
        
        <!-- Error display -->
        <div v-if="error" class="bg-red-50 border border-red-200 rounded-md p-4">
          <div class="text-red-700">
            {{ error }}
          </div>
        </div>
        
        <!-- Form actions -->
        <div class="flex justify-end space-x-4">
          <router-link
            to="/"
            class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
          >
            Cancel
          </router-link>
          <button
            type="submit"
            :disabled="loading"
            class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="spinner mr-2"></span>
            {{ loading ? 'Creating Post...' : 'Create Post' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { usePostsStore } from '@/stores/posts'

export default {
  name: 'CreatePost',
  setup() {
    const router = useRouter()
    const postsStore = usePostsStore()
    
    const form = ref({
      title: '',
      content: ''
    })
    
    const loading = ref(false)
    const error = ref('')
    
    const handleSubmit = async () => {
      loading.value = true
      error.value = ''
      
      const result = await postsStore.createPost(form.value)
      
      if (result.success) {
        router.push(`/posts/${result.data.data.id}`)
      } else {
        error.value = result.error
      }
      
      loading.value = false
    }
    
    return {
      form,
      loading,
      error,
      handleSubmit
    }
  }
}
</script>
