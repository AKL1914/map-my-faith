<template>
  <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button
        id="sidebarToggleTop"
        class="btn btn-link d-md-none rounded-circle mr-3"
        @click="$emit('toggleSidebar')"
    >
      <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" role="button">
          <span class="mr-2 d-none d-lg-inline text-gray-600 small">User</span>
          <i class="fas fa-user-circle fa-lg"></i>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  name: 'Topbar'
};
</script>