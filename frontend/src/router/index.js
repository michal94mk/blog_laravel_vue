import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Layout from '@/components/Layout.vue'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import PostDetail from '../views/PostDetail.vue'
import CreatePost from '../views/CreatePost.vue'
import EditPost from '../views/EditPost.vue'
import Profile from '../views/Profile.vue'

const routes = [
  {
    path: '/',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Home',
        component: Home
      },
      {
        path: 'posts/:id',
        name: 'PostDetail',
        component: PostDetail
      },
      {
        path: 'posts/create',
        name: 'CreatePost',
        component: CreatePost,
        meta: { requiresAuth: true }
      },
      {
        path: 'posts/:id/edit',
        name: 'EditPost',
        component: EditPost,
        meta: { requiresAuth: true }
      },
      {
        path: 'profile',
        name: 'Profile',
        component: Profile,
        meta: { requiresAuth: true }
      }
    ]
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Wait for auth initialization to complete
  if (authStore.isLoading) {
    // Wait for initialization to finish
    await new Promise(resolve => {
      const checkLoading = () => {
        if (!authStore.isLoading) {
          resolve()
        } else {
          setTimeout(checkLoading, 10)
        }
      }
      checkLoading()
    })
  }
  
  // Check if route requires authentication
  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    next('/login')
    return
  }
  
  // Check if route is for guests only
  if (to.meta.guest && authStore.isLoggedIn) {
    next('/')
    return
  }
  
  next()
})

export default router
