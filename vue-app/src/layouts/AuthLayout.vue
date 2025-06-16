<!-- src/layouts/AuthLayout.vue -->
<template>
    <!-- Sidebar -->
    <Sidebar />

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <!-- Topbar -->
        <Topbar @toggle-sidebar="toggleSidebar" />

        <!-- Begin Page Content -->
        <div :class="['container-fluid', { 'map-outer-container': isMapsRoute }]">
          <main>
            <router-view />
          </main>
        </div>
        <!-- End Page Content -->
      </div>

      <!-- Footer -->
      <Footer />
    </div>

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
</template>

<script setup>
import { onMounted } from 'vue'
import { ref, provide } from 'vue'
import Sidebar from '@/partials/theme/Sidebar.vue'
import Topbar from '@/partials/theme/Topbar.vue'
import Footer from '@/partials/theme/Footer.vue'
import { computed } from 'vue'
import { useRoute } from 'vue-router'
const route = useRoute()

const isMapsRoute = computed(() => route.path === '/maps')

const isSidebarToggled = ref(true)
provide('isSidebarToggled', isSidebarToggled)

function toggleSidebar() {
  isSidebarToggled.value = !isSidebarToggled.value
}

onMounted(() => {
  const id = 'layout-css'
  const existing = document.getElementById(id)
  if (existing) existing.remove()

  const link = document.createElement('link')
  link.id = id
  link.rel = 'stylesheet'
  link.href = '/theme/css/sb-admin-2.min.css'
  document.head.appendChild(link)
})
</script>