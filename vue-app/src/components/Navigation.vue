<!-- src/components/Navigation.vue -->
<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <router-link to="/" class="navbar-brand">Maps</router-link>
      <div class="d-flex align-items-center gap-2 ms-auto">
        <template v-if="isLoggedIn">
          <button @click="logout" class="btn btn-outline-danger">Logout</button>
        </template>
        <template v-else>
          <a :href="googleLoginUrl" class="btn btn-outline-primary">
            <i class="bi bi-google"></i> Sign in
          </a>
        </template>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const isLoggedIn = computed(() => authStore.user !== null)
const googleLoginUrl = '/login/google'

const logout = () => {
  // add logout logic via Sanctum if needed
}
</script>
