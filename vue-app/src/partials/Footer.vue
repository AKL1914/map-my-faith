<template>
  <footer class="bg-blue text-white text-center py-3 mt-auto">
    <div class="container">
      <small>&copy; {{ year }} Maps. All rights reserved.</small>
    </div>
  </footer>
</template>

<script setup>
const year = new Date().getFullYear()
</script>
