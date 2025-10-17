<template>
  <div id="app">
    <router-view />
    <ToastContainer />
  </div>
</template>

<script>
import ToastContainer from '@/components/ToastContainer.vue'

export default {
  name: 'App',
  components: {
    ToastContainer
  }
}
</script>

<style>
#app {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
</style>
