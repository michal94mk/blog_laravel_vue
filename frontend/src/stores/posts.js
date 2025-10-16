import { defineStore } from 'pinia'
import axios from 'axios'
import { useAuthStore } from './auth'

export const usePostsStore = defineStore('posts', {
  state: () => ({
    posts: [],
    currentPost: null,
    loading: false,
    error: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0
    }
  }),

  getters: {
    getPostById: (state) => (id) => {
      return state.posts.find(post => post.id === parseInt(id))
    }
  },

  actions: {
    async fetchPosts(page = 1) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`/api/v1/posts?page=${page}`)
        const { data, ...pagination } = response.data
        
        this.posts = data
        this.pagination = pagination
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch posts'
        return { 
          success: false, 
          error: this.error 
        }
      } finally {
        this.loading = false
      }
    },

    async fetchPost(id) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`/api/v1/posts/${id}`)
        this.currentPost = response.data.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch post'
        return { 
          success: false, 
          error: this.error 
        }
      } finally {
        this.loading = false
      }
    },

    async createPost(postData) {
      this.loading = true
      this.error = null

      try {
        const response = await axios.post('/api/v1/posts', postData)
        this.posts.unshift(response.data.data)

        // Refresh user stats after creating post
        const authStore = useAuthStore()
        await authStore.refreshUserStats()

        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create post'
        return {
          success: false,
          error: this.error
        }
      } finally {
        this.loading = false
      }
    },

    async updatePost(id, postData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.put(`/api/v1/posts/${id}`, postData)
        
        // Update post in posts array
        const index = this.posts.findIndex(post => post.id === parseInt(id))
        if (index !== -1) {
          this.posts[index] = response.data.data
        }

        // Update current post if it's the same
        if (this.currentPost && this.currentPost.id === parseInt(id)) {
          this.currentPost = response.data.data
        }

        // Refresh user stats after updating post (in case title/content changed)
        const authStore = useAuthStore()
        await authStore.refreshUserStats()

        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update post'
        return { 
          success: false, 
          error: this.error 
        }
      } finally {
        this.loading = false
      }
    },

    async deletePost(id) {
      this.loading = true
      this.error = null

      try {
        await axios.delete(`/api/v1/posts/${id}`)

        // Remove post from posts array
        this.posts = this.posts.filter(post => post.id !== parseInt(id))

        // Clear current post if it's the same
        if (this.currentPost && this.currentPost.id === parseInt(id)) {
          this.currentPost = null
        }

        // Refresh user stats after deleting post
        const authStore = useAuthStore()
        await authStore.refreshUserStats()

        return { success: true }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete post'
        return {
          success: false,
          error: this.error
        }
      } finally {
        this.loading = false
      }
    },

    clearError() {
      this.error = null
    },

    clearCurrentPost() {
      this.currentPost = null
    }
  }
})
