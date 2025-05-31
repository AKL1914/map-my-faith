<!-- src/components/Footer.vue -->
<template>
  <footer class="text-center py-3 mt-auto bg-light text-muted small">
    <div>© {{ year }} Maps. All rights reserved.</div>
  </footer>
</template>

<script setup>
const year = new Date().getFullYear()
</script>
