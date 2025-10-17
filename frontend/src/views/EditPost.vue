<template>
  <div class="max-w-4xl mx-auto">
    <!-- Loading state -->
    <div v-if="loading" class="text-center py-8">
      <div class="spinner mx-auto mb-4"></div>
      <p class="text-gray-600">Loading post...</p>
    </div>
    
    <!-- Error state -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
      <div class="text-red-700">
        {{ error }}
      </div>
    </div>
    
    <!-- Edit form -->
    <div v-else-if="post" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Post</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Update your post content</p>
      </div>
      
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
            :to="`/posts/${post.id}`"
            class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
          >
            Cancel
          </router-link>
          <button
            type="submit"
            :disabled="updating"
            class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="updating" class="spinner mr-2"></span>
            {{ updating ? 'Updating Post...' : 'Update Post' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePostsStore } from '@/stores/posts'

export default {
  name: 'EditPost',
  setup() {
    const route = useRoute()
    const router = useRouter()
    const postsStore = usePostsStore()
    
    const post = ref(null)
    const form = ref({
      title: '',
      content: ''
    })
    
    const loading = ref(false)
    const updating = ref(false)
    const error = ref('')
    
    const loadPost = async () => {
      loading.value = true
      error.value = ''
      
      const result = await postsStore.fetchPost(route.params.id)
      
      if (result.success) {
        post.value = result.data.data
        form.value = {
          title: post.value.title,
          content: post.value.content
        }
      } else {
        error.value = result.error
      }
      
      loading.value = false
    }
    
    const handleSubmit = async () => {
      updating.value = true
      error.value = ''
      
      const result = await postsStore.updatePost(post.value.id, form.value)
      
      if (result.success) {
        router.push(`/posts/${post.value.id}`)
      } else {
        error.value = result.error
      }
      
      updating.value = false
    }
    
    onMounted(() => {
      loadPost()
    })
    
    return {
      post,
      form,
      loading,
      updating,
      error,
      handleSubmit
    }
  }
}
</script>
