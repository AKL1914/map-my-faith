<template>
  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/maps">
      <div class="sidebar-brand-icon">
        <i class="fas fa-map-marker-alt me-2"></i>
      </div>
      <div class="sidebar-brand-text mx-3">Maps</div>
    </a>

    <hr class="sidebar-divider" />

    <li class="nav-item">
      <router-link class="nav-link" to="/maps">
        <i class="fas fa-map"></i>
        <span>Maps</span>
      </router-link>
    </li>
  </ul>
</template>

<script>
export default {
  name: 'Sidebar'
};
</script>