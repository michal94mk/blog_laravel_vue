<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script>
import Navbar from './Navbar.vue'

export default {
  name: 'Layout',
  components: {
    Navbar
  }
}
</script>
