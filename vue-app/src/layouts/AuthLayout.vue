<!-- src/layouts/AuthLayout.vue -->
<template>
  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <!-- Topbar -->
        <Topbar />

        <!-- Begin Page Content -->
        <div class="container-fluid map-outer-container">
          <main>
            <router-view />
          </main>
        </div>
        <!-- End Page Content -->
      </div>

      <!-- Footer -->
      <Footer />
    </div>
  </div>

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
</template>

<script setup>
import Sidebar from '@/components/Sidebar.vue'
import Topbar from '@/components/Topbar.vue'
import Footer from '@/components/Footer.vue'
</script>