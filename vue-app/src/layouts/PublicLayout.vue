<template>

  <Navigation />
    <main class="container my-3">
      <router-view />
    </main>
    <Footer />
</template>
<script setup>
import Navigation from '@/partials//Navigation.vue'
import Footer from "@/partials/Footer.vue";
import { onMounted } from 'vue'

onMounted(() => {
  const id = 'layout-css'
  const existing = document.getElementById(id)
  if (existing) existing.remove()

  const link = document.createElement('link')
  link.id = id
  link.rel = 'stylesheet'
  link.href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
  document.head.appendChild(link)
})
</script>
<style>

html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}

body {
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}

.bg-gray {
  background-color: #808080 !important;
}

.navbar .nav-link {
  padding: 0.75rem 1rem;
  font-size: 1rem;
}

.navbar .btn {
  font-size: 0.95rem;
}

@media (max-width: 576px) {
  .navbar-brand {
    font-size: 1.25rem;
  }

  footer {
    font-size: 0.9rem;
  }
}

/* Add some custom styles to make it feel like a mobile app */
body {
  font-family: 'Roboto', sans-serif;
}

h1 {
  font-size: 1.75rem;
  font-weight: bold;
}

.card {
  border-radius: 15px; /* Rounded corners for a mobile app feel */
}

.table th, .table td {
  padding: 0.8rem;
}

.table th {
  font-size: 1rem;
  font-weight: bold;
}

.table td {
  font-size: 0.95rem;
}

@media (max-width: 576px) {
  .table th, .table td {
    font-size: 0.85rem; /* Adjust font size for smaller screens */
  }
  h1 {
    font-size: 1.5rem;
  }
}
/* Custom gray background for the table header */
.table thead {
  background-color: #f8f9fa; /* Light gray background */
}

/* Optional: Adjust text color for the table header */
.table thead th {
  color: #495057; /* Darker text for contrast */
}

.bg-blue {
  background-color: #4285f4;
}

.nav-link {
  color: white !important;
}



</style>