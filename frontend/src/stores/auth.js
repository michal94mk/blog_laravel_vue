import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    isAuthenticated: false,
    isLoading: true
  }),

  getters: {
    isLoggedIn: (state) => state.isAuthenticated && !!state.token
  },

  actions: {
    setAuth(user, token) {
      this.user = user
      this.token = token
      this.isAuthenticated = true
      localStorage.setItem('token', token)
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    },

    clearAuth() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
    },

    async login(credentials) {
      try {
        const response = await axios.post('/api/v1/login', credentials)
        const { user, token } = response.data.data
        this.setAuth(user, token)
        return { success: true, data: response.data }
      } catch (error) {
        return { 
          success: false, 
          error: error.response?.data?.message || 'Login failed' 
        }
      }
    },

    async register(userData) {
      try {
        const response = await axios.post('/api/v1/register', userData)
        const { user, token } = response.data.data
        this.setAuth(user, token)
        return { success: true, data: response.data }
      } catch (error) {
        return { 
          success: false, 
          error: error.response?.data?.message || 'Registration failed' 
        }
      }
    },

    async logout() {
      try {
        if (this.token) {
          await axios.post('/api/v1/logout')
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
      }
    },

    async fetchUser() {
      try {
        if (this.token) {
          axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
          const response = await axios.get('/api/v1/me')
          this.user = response.data.data
          this.isAuthenticated = true
          return { success: true, data: response.data }
        }
      } catch (error) {
        this.clearAuth()
        return { 
          success: false, 
          error: error.response?.data?.message || 'Failed to fetch user' 
        }
      }
    },

    async initializeAuth() {
      this.isLoading = true
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        const result = await this.fetchUser()
        if (!result.success) {
          this.clearAuth()
        }
      }
      this.isLoading = false
    }
  }
})
