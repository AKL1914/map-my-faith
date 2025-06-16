<template>
  <ul
      class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
      :class="{ toggled: isSidebarToggled }"
      id="accordionSidebar"
  >
    <!-- Sidebar - Brand -->
    <router-link class="sidebar-brand d-flex align-items-center justify-content-center" to="/">
      <div class="sidebar-brand-icon">
        <i class="fas fa-map-marker-alt me-2"></i>
      </div>
      <div class="sidebar-brand-text mx-3">Maps</div>
    </router-link>

    <hr class="sidebar-divider" />

    <!-- Main Map Page -->
    <li class="nav-item">
      <router-link to="/maps" class="nav-link" active-class="active">
        <i class="fas fa-map"></i>
        <span>Maps</span>
      </router-link>
    </li>

    <hr class="sidebar-divider" />

    <!-- My Pins -->
    <li class="nav-item">
      <router-link :to="`/user/pins`" class="nav-link" active-class="active">
        <i class="fas fa-map-pin"></i>
        <span>My Pins</span>
      </router-link>
    </li>

    <!-- Leaderboard -->
    <li class="nav-item">
      <router-link to="/leaderboard" class="nav-link" active-class="active">
        <i class="fas fa-award"></i>
        <span>Leaderboard</span>
      </router-link>
    </li>

    <hr class="sidebar-divider d-none d-md-block" />

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" @click="toggleSidebar" id="sidebarToggle"></button>
    </div>
  </ul>
</template>

<script setup>

// If you store user info in localStorage or a store, use that here
const user = JSON.parse(localStorage.getItem('user'))
const userId = user?.id ?? '0'
import { inject } from 'vue'
const isSidebarToggled = inject('isSidebarToggled', false)

</script>