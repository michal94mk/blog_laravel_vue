import { defineStore } from 'pinia'
import axios from 'axios'
import { useAuthStore } from './auth'
import { useToastStore } from './toast'

export const useCommentsStore = defineStore('comments', {
  state: () => ({
    comments: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchComments(postId) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`/api/v1/posts/${postId}/comments`)
        this.comments = response.data.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch comments'
        return { 
          success: false, 
          error: this.error 
        }
      } finally {
        this.loading = false
      }
    },

    async createComment(postId, commentData) {
      const toastStore = useToastStore()
      this.loading = true
      this.error = null

      try {
        const response = await axios.post(`/api/v1/posts/${postId}/comments`, commentData)
        this.comments.push(response.data.data)

        // Refresh user stats after creating comment
        const authStore = useAuthStore()
        await authStore.refreshUserStats()

        toastStore.success('Comment Added!', 'Your comment has been posted successfully.')
        return { success: true, data: response.data }
      } catch (error) {
        const errorMessage = error.response?.data?.message || 'Failed to create comment'
        this.error = errorMessage
        toastStore.error('Failed to Add Comment', errorMessage)
        return {
          success: false,
          error: errorMessage
        }
      } finally {
        this.loading = false
      }
    },

    async updateComment(id, commentData) {
      const toastStore = useToastStore()
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.put(`/api/v1/comments/${id}`, commentData)
        
        // Update comment in comments array
        const index = this.comments.findIndex(comment => comment.id === parseInt(id))
        if (index !== -1) {
          this.comments[index] = response.data.data
        }
        
        toastStore.success('Comment Updated!', 'Your comment has been updated successfully.')
        return { success: true, data: response.data }
      } catch (error) {
        const errorMessage = error.response?.data?.message || 'Failed to update comment'
        this.error = errorMessage
        toastStore.error('Failed to Update Comment', errorMessage)
        return { 
          success: false, 
          error: errorMessage
        }
      } finally {
        this.loading = false
      }
    },

    async deleteComment(id) {
      const toastStore = useToastStore()
      this.loading = true
      this.error = null

      try {
        await axios.delete(`/api/v1/comments/${id}`)

        // Remove comment from comments array
        this.comments = this.comments.filter(comment => comment.id !== parseInt(id))

        // Refresh user stats after deleting comment
        const authStore = useAuthStore()
        await authStore.refreshUserStats()

        toastStore.success('Comment Deleted', 'The comment has been deleted successfully.')
        return { success: true }
      } catch (error) {
        const errorMessage = error.response?.data?.message || 'Failed to delete comment'
        this.error = errorMessage
        toastStore.error('Failed to Delete Comment', errorMessage)
        return {
          success: false,
          error: errorMessage
        }
      } finally {
        this.loading = false
      }
    },

    clearComments() {
      this.comments = []
    },

    clearError() {
      this.error = null
    }
  }
})
