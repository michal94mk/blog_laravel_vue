<template>
  <div class="flex items-center justify-center py-8">
    <div class="spinner mr-3"></div>
    <span class="text-gray-600">{{ message }}</span>
  </div>
</template>

<script>
export default {
  name: 'Loading',
  props: {
    message: {
      type: String,
      default: 'Loading...'
    }
  }
}
</script>
