<template>
  <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" @click="$emit('toggle-sidebar')"  class="btn btn-link d-md-none rounded-circle mr-3">
      <i class="fa fa-bars"></i>
    </button>
    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
      <div class="topbar-divider d-none d-sm-block"></div>

      <!-- Nav Item - User Information -->
      <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="mr-2 d-none1 d-lg-inline text-gray-600 small">{{ authStore.user?.name || '' }}</span>
          <img
              class="img-profile rounded-circle"
              src="/theme/img/undraw_profile.svg"
              alt="User profile"
          />
        </a>
        <!-- Dropdown - User Information -->
        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
             aria-labelledby="userDropdown">
          <a class="dropdown-item" href="profile.edit" >
            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
            Profile
          </a>

          <div class="dropdown-divider"></div>

          <form @submit.prevent="logout" class="d-inline">
            <button type="submit" class="dropdown-item">
              <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
              Logout
            </button>
          </form>
        </div>
      </li>

    </ul>

  </nav>


</template>

<script setup>
import { useAuthStore } from '@/stores/auth.js';
import { useRouter } from 'vue-router';

const authStore = useAuthStore()
const router = useRouter();

function logout() {
  authStore.logout();
  router.push('/'); // or wherever your login page is
}
</script>