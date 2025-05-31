<!-- src/pages/HomePage.vue -->
<template>
  <div class="d-flex flex-column min-vh-100">
    <Navigation />

    <main class="container text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center">
<!--      <img src="/images/logo.png" alt="Logo" class="mb-4 rounded-3" style="max-width: 200px;" />-->

      <h1 class="mb-4 text-secondary">Welcome to Maps</h1>

      <div v-if="isLoggedIn" class="mb-3 w-100 d-flex justify-content-center">
        <RouterLink to="/maps" class="google-btn">
          <i class="fas fa-map-marker-alt me-2"></i>
          <span>View Maps</span>
        </RouterLink>
      </div>

      <div v-else class="mb-3 w-100 d-flex justify-content-center">
        <a :href="googleLoginUrl" class="google-btn">
          <i class="bi bi-google"></i>
          <span>Sign in with Google</span>
        </a>
      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Navigation from '@/components/Navigation.vue'
import Footer from '@/components/Footer.vue'
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const isLoggedIn = computed(() => authStore.user !== null)
const googleLoginUrl = '/login/google'
</script>

<style scoped>
.google-btn {
  background-color: #4285f4;
  color: white;
  border: none;
  font-weight: 500;
  padding: 1rem 1.5rem;
  font-size: 1.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  width: 100%;
  max-width: 360px;
  border-radius: 0.5rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  text-decoration: none;
  transition: background-color 0.3s ease;
}
.google-btn:hover {
  background-color: #357ae8;
}
</style>
