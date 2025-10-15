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
    
    <!-- Post content -->
    <div v-else-if="post" class="bg-white rounded-lg shadow-md p-8 mb-8">
      <div class="flex justify-between items-start mb-6">
        <div>
          <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ post.title }}</h1>
          <div class="flex items-center space-x-4 text-sm text-gray-500">
            <span>By {{ post.user?.name || 'Anonymous' }}</span>
            <span>•</span>
            <span>{{ post.created_at_human }}</span>
          </div>
        </div>
        
        <!-- Post actions -->
        <div v-if="canEditPost" class="flex space-x-2">
          <router-link
            :to="`/posts/${post.id}/edit`"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium"
          >
            Edit
          </router-link>
          <button
            @click="handleDeletePost"
            :disabled="deleting"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium disabled:opacity-50"
          >
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
      
      <div class="prose max-w-none">
        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ post.content }}</p>
      </div>
    </div>
    
    <!-- Comments section -->
    <div v-if="post" class="bg-white rounded-lg shadow-md p-8">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">
        Comments ({{ comments.length }})
      </h2>
      
      <!-- Add comment form -->
      <div v-if="isAuthenticated" class="mb-8">
        <form @submit.prevent="handleAddComment" class="space-y-4">
          <div>
            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
              Add a comment
            </label>
            <textarea
              id="comment"
              v-model="newComment"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Share your thoughts..."
              required
            ></textarea>
          </div>
          
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="addingComment"
              class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md text-sm font-medium disabled:opacity-50"
            >
              {{ addingComment ? 'Adding...' : 'Add Comment' }}
            </button>
          </div>
        </form>
      </div>
      
      <!-- Guest comment prompt -->
      <div v-else class="mb-8 p-4 bg-gray-50 rounded-md">
        <p class="text-gray-600 text-center">
          <router-link to="/login" class="text-indigo-600 hover:text-indigo-800 font-medium">
            Sign in
          </router-link>
          to add a comment
        </p>
      </div>
      
      <!-- Comments list -->
      <div v-if="comments.length > 0" class="space-y-6">
        <div
          v-for="comment in comments"
          :key="comment.id"
          class="border-b border-gray-200 pb-6 last:border-b-0"
        >
          <div class="flex justify-between items-start mb-3">
            <div class="flex items-center space-x-3">
              <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                <span class="text-sm font-medium text-gray-700">
                  {{ comment.user?.name?.charAt(0) || '?' }}
                </span>
              </div>
              <div>
                <span class="text-sm font-medium text-gray-900">
                  {{ comment.user?.name || 'Anonymous' }}
                </span>
                <span class="text-sm text-gray-500 ml-2">
                  {{ comment.created_at_human }}
                </span>
              </div>
            </div>
            
            <!-- Comment actions -->
            <div v-if="canEditComment(comment)" class="flex space-x-2">
              <button
                @click="editComment(comment)"
                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium"
              >
                Edit
              </button>
              <button
                @click="handleDeleteComment(comment.id)"
                :disabled="deletingComment === comment.id"
                class="text-red-600 hover:text-red-800 text-sm font-medium disabled:opacity-50"
              >
                {{ deletingComment === comment.id ? 'Deleting...' : 'Delete' }}
              </button>
            </div>
          </div>
          
          <div class="ml-11">
            <p v-if="!editingComment || editingComment.id !== comment.id" class="text-gray-700 whitespace-pre-wrap">
              {{ comment.comment }}
            </p>
            
            <!-- Edit comment form -->
            <form v-else @submit.prevent="handleUpdateComment(comment)" class="space-y-3">
              <textarea
                v-model="editingComment.comment"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                required
              ></textarea>
              <div class="flex justify-end space-x-2">
                <button
                  type="button"
                  @click="cancelEditComment"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="updatingComment"
                  class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md disabled:opacity-50"
                >
                  {{ updatingComment ? 'Updating...' : 'Update' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <!-- Empty comments state -->
      <div v-else class="text-center py-8">
        <div class="text-gray-400 mb-4">
          <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No comments yet</h3>
        <p class="text-gray-600">Be the first to share your thoughts!</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePostsStore } from '@/stores/posts'
import { useCommentsStore } from '@/stores/comments'
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'PostDetail',
  setup() {
    const route = useRoute()
    const router = useRouter()
    const postsStore = usePostsStore()
    const commentsStore = useCommentsStore()
    const authStore = useAuthStore()
    
    const post = ref(null)
    const comments = ref([])
    const loading = ref(false)
    const error = ref('')
    const deleting = ref(false)
    const newComment = ref('')
    const addingComment = ref(false)
    const deletingComment = ref(null)
    const editingComment = ref(null)
    const updatingComment = ref(false)
    
    const isAuthenticated = computed(() => authStore.isLoggedIn)
    const currentUser = computed(() => authStore.user)
    
    const canEditPost = computed(() => {
      return isAuthenticated.value && post.value && currentUser.value?.id === post.value.user?.id
    })
    
    const canEditComment = (comment) => {
      if (!isAuthenticated.value) return false
      if (currentUser.value?.id === comment.user?.id) return true
      if (currentUser.value?.id === post.value?.user?.id) return true
      return false
    }
    
    const loadPost = async () => {
      loading.value = true
      error.value = ''
      
      const result = await postsStore.fetchPost(route.params.id)
      
      if (result.success) {
        post.value = result.data.data
        await loadComments()
      } else {
        error.value = result.error
      }
      
      loading.value = false
    }
    
    const loadComments = async () => {
      const result = await commentsStore.fetchComments(route.params.id)
      if (result.success) {
        comments.value = result.data.data
      }
    }
    
    const handleDeletePost = async () => {
      if (!confirm('Are you sure you want to delete this post?')) return
      
      deleting.value = true
      const result = await postsStore.deletePost(post.value.id)
      
      if (result.success) {
        router.push('/')
      } else {
        error.value = result.error
      }
      
      deleting.value = false
    }
    
    const handleAddComment = async () => {
      addingComment.value = true
      
      const result = await commentsStore.createComment(route.params.id, {
        comment: newComment.value
      })
      
      if (result.success) {
        newComment.value = ''
        await loadComments()
      } else {
        error.value = result.error
      }
      
      addingComment.value = false
    }
    
    const handleDeleteComment = async (commentId) => {
      if (!confirm('Are you sure you want to delete this comment?')) return
      
      deletingComment.value = commentId
      const result = await commentsStore.deleteComment(commentId)
      
      if (result.success) {
        await loadComments()
      } else {
        error.value = result.error
      }
      
      deletingComment.value = null
    }
    
    const editComment = (comment) => {
      editingComment.value = { ...comment }
    }
    
    const cancelEditComment = () => {
      editingComment.value = null
    }
    
    const handleUpdateComment = async (comment) => {
      updatingComment.value = true
      
      const result = await commentsStore.updateComment(comment.id, {
        comment: editingComment.value.comment
      })
      
      if (result.success) {
        editingComment.value = null
        await loadComments()
      } else {
        error.value = result.error
      }
      
      updatingComment.value = false
    }
    
    onMounted(() => {
      loadPost()
    })
    
    return {
      post,
      comments,
      loading,
      error,
      deleting,
      newComment,
      addingComment,
      deletingComment,
      editingComment,
      updatingComment,
      isAuthenticated,
      canEditPost,
      canEditComment,
      handleDeletePost,
      handleAddComment,
      handleDeleteComment,
      editComment,
      cancelEditComment,
      handleUpdateComment
    }
  }
}
</script>

<style scoped>
.prose {
  max-width: none;
}
</style>
